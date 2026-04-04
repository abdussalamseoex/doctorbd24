<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Chamber;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\ReportDuplicate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImportPopularDoctorsCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:popular-doctors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import doctors from popular diagnostic CSV file and save as draft';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path('popular_diagnostic_20260404_2118.csv');

        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: $filePath");
            return;
        }

        $this->info("Starting Popular Doctors CSV import...");

        // Auto-Repair: Previous imports might have been mistakenly published due to mass-assignment exception.
        // We will force them into draft status.
        $repairedCount = Doctor::where('photo', 'like', 'popular/%')
            ->where(function($q) {
                $q->where('status', '!=', 'draft')->orWhereNull('status');
            })
            ->update([
                'status' => 'draft',
                'import_source' => 'popular_diagnostic'
            ]);
            
        if ($repairedCount > 0) {
            $this->info("Repaired $repairedCount previously imported doctors and moved them to Draft.");
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);

        if (!$headers) {
            $this->error("CSV file is empty or missing headers.");
            fclose($handle);
            return;
        }

        // Clean headers BOM
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        $count = 0;
        $duplicateCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                $this->warn("Skipping a row due to mismatch in column counts.");
                continue;
            }

            $data = array_combine($headers, $row);

            $name = trim($data['Name'] ?? '');
            if (empty($name)) continue;

            $this->info("Processing: $name");

            // Prevent duplicating rows if script is run multiple times (e.g. after a timeout)
            $alreadyImported = Doctor::where('name', $name)->where('import_source', 'popular_diagnostic')->first();
            if ($alreadyImported) {
                $this->info("   -> Already imported previously. Skipping...");
                continue;
            }

            // 1. Duplicate Detection Check
            // We search for an existing doctor with a highly similar name
            // (Ignoring common titles like Dr, Prof, Asst, etc. for better matching if needed, but a simple ILIKE is a good start)
            $cleanNameForSearch = trim(str_ireplace(['Prof.', 'Dr.', 'Asst.', 'Assoc.', '(Shepu)'], '', $name));
            if (strlen($cleanNameForSearch) > 3) {
                // Check if any existing doctor has a name containing this clean name
                $existingDuplicate = Doctor::where('id', '!=', 0) // dummy condition just to ensure builder
                                    ->where(function ($q) use ($cleanNameForSearch) {
                                        $q->where('name', 'LIKE', '%' . $cleanNameForSearch . '%')
                                          ->orWhere('slug', 'LIKE', '%' . Str::slug($cleanNameForSearch) . '%');
                                    })
                                    ->first();
            } else {
                $existingDuplicate = Doctor::where('name', $name)->first();
            }

            // Determine slug
            $slug = Str::slug($name);
            if (Doctor::where('slug', $slug)->exists()) {
                $slug .= '-' . rand(1000, 9999);
            }

            $qualifications = trim($data['Degrees'] ?? '');
            $photoUrl = trim($data['Image URL'] ?? '');
            $specialtyName = trim($data['Specialty'] ?? '');
            $branchName = trim($data['Branch'] ?? '');
            $visitingDays = trim($data['Visiting Days'] ?? '');
            $visitingTime = trim($data['Visiting Time'] ?? '');

            // 2. Download Image
            $photoPath = null;
            if (!empty($photoUrl) && filter_var($photoUrl, FILTER_VALIDATE_URL)) {
                // Because these images might be duplicated or generic placeholders (e.g. general doctor vectors)
                // we'll try to save them uniquely.
                $extension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (empty($extension)) $extension = 'jpg';
                
                $filename = 'popular/' . $slug . '-' . uniqid() . '.' . $extension;
                
                if (!Storage::disk('public')->exists($filename)) {
                    try {
                        // Quick context stream to bypass some blocks just in case
                        $opts = [
                            "http" => [
                                "method" => "GET",
                                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
                            ]
                        ];
                        $context = stream_context_create($opts);
                        $contents = file_get_contents($photoUrl, false, $context);

                        if ($contents !== false) {
                            Storage::disk('public')->put($filename, $contents);
                            $photoPath = $filename;
                        }
                    } catch (\Exception $e) {
                        $this->warn("Could not download image for $name: " . $e->getMessage());
                    }
                }
            }

            // 3. Create Draft Doctor
            $doctor = Doctor::create([
                'name' => $name,
                'slug' => $slug,
                'qualifications' => $qualifications,
                'photo' => $photoPath,
                'status' => 'draft', // User requested draft mode!
                'import_source' => 'popular_diagnostic',
                'verified' => false,
                'view_count' => 0
            ]);

            // 4. Handle Specialty
            if (!empty($specialtyName)) {
                $sSlug = Str::slug($specialtyName);
                if (!empty($sSlug)) {
                    $spec = Specialty::firstOrCreate(
                        ['slug' => $sSlug],
                        ['name' => ['en' => $specialtyName, 'bn' => $specialtyName]]
                    );
                    $doctor->specialties()->attach($spec->id);
                }
            }

            // 5. Handle Chamber / Branch
            if (!empty($branchName)) {
                // Map the branch name to Popular Diagnostic Center ...
                $fullHospitalName = "Popular Diagnostic Center, " . $branchName;
                $hSlug = Str::slug($fullHospitalName);

                $hospital = Hospital::firstOrCreate(
                    ['slug' => $hSlug],
                    [
                        'name' => $fullHospitalName,
                        'type' => 'diagnostic'
                    ]
                );

                // Build visiting hours string
                $hoursStr = "";
                if (!empty($visitingDays)) $hoursStr .= $visitingDays;
                if (!empty($visitingTime)) {
                    $hoursStr .= empty($hoursStr) ? $visitingTime : ", " . $visitingTime;
                }

                Chamber::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'hospital_id' => $hospital->id],
                    [
                        'name' => 'Popular Diagnostic Center',
                        'visiting_hours' => $hoursStr,
                    ]
                );
            }

            // 6. Log Duplicate if existing found
            if ($existingDuplicate) {
                // We create a report duplicate record linking the new draft doctor as the 'reportable'
                // and mention who it's duplicating in the reason
                ReportDuplicate::create([
                    'reportable_type' => Doctor::class,
                    'reportable_id' => $doctor->id,
                    'reason' => "Imported Draft (Popular Diagnostic) seems similar to existing published Doctor ID: {$existingDuplicate->id} ({$existingDuplicate->name})",
                    'status' => 'pending'
                ]);
                $duplicateCount++;
                $this->warn("   -> Flagged as potential duplicate of ID: {$existingDuplicate->id}");
            }

            $count++;
        }

        fclose($handle);
        $this->info("Import completed successfully! Total Processed: $count. Total Flagged as Duplicates: $duplicateCount.");
    }
}
