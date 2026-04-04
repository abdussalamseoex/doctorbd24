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
    protected $signature = 'import:popular-doctors {--chunk= : Number of rows to process}';

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
        \Illuminate\Support\Facades\Cache::put('popular_import_progress', [
            'status' => 'running',
            'current' => 0,
            'total' => 0,
            'message' => 'Initializing...'
        ]);


            

        $chunkSize = $this->option('chunk') ? (int) $this->option('chunk') : null;
        $handle = fopen($filePath, 'r');
        $totalLines = count(file($filePath, FILE_SKIP_EMPTY_LINES)) - 1; // subtract header
        $headers = fgetcsv($handle);

        $currentIndex = (int) \Illuminate\Support\Facades\Cache::get('popular_import_pointer', 0);
        $processedInThisChunk = 0;

        if (!$headers) {
            $this->error("CSV file is empty or missing headers.");
            fclose($handle);
            return;
        }

        // Clean headers BOM
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        $duplicateCount = 0;
        $csvLineIndex = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $csvLineIndex++;

            // Fast forward past already imported lines
            if ($csvLineIndex <= $currentIndex) {
                continue;
            }

            // Save pointer IMMEDIATELY! This absolutely ensures that if the script fatals on THIS row, 
            // the next retry will skip it, completely avoiding poison-pill infinite freeze loops!
            \Illuminate\Support\Facades\Cache::put('popular_import_pointer', $csvLineIndex);

            if (count($row) !== count($headers)) {
                $this->warn("Skipping a row due to mismatch in column counts.");
                continue;
            }

            $data = array_combine($headers, $row);

            $name = trim($data['Name'] ?? '');
            if (empty($name)) continue;
            
            // Gender Detection Heuristic
            $gender = 'male'; // default
            $femaleKeywords = ['Begum', 'Sultana', 'Khatun', 'Akhter', 'Akter', ' Ara', 'Banu', '(Mrs)', '(Mrs.)', 'Nesa', 'Ferdousi', 'Nahar', 'Yasmin', 'Shirin', 'Ayesha', 'Fatema', 'Roksana', 'Hasna', 'Laila', 'Nasrin', 'Farhana', 'Dilruba', 'Salma', 'Tanjina', 'Sharmin', 'Maksuda', 'Jannat'];
            foreach ($femaleKeywords as $keyword) {
                if (stripos($name, $keyword) !== false) {
                    $gender = 'female';
                    break;
                }
            }

            // Designation Deduction
            $designation = '';
            if (stripos($name, 'Prof.') !== false) {
                $designation = 'Professor';
            } elseif (stripos($name, 'Asst. Prof') !== false || stripos($name, 'Asst Prof') !== false) {
                $designation = 'Assistant Professor';
            } elseif (stripos($name, 'Assoc. Prof') !== false || stripos($name, 'Assoc Prof') !== false) {
                $designation = 'Associate Professor';
            }

            if ($csvLineIndex % 5 === 0) {
                \Illuminate\Support\Facades\Cache::put('popular_import_progress', [
                    'status' => 'running',
                    'current' => $csvLineIndex,
                    'total' => $totalLines,
                    'message' => "Processing: $name"
                ]);
            }

            $this->info("Processing: $name");

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
                        // Use Laravel's HTTP client with a strict timeout to avoid infinite freezes
                        $response = \Illuminate\Support\Facades\Http::timeout(5)
                                        ->withoutVerifying()
                                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                                        ->get($photoUrl);

                        if ($response->successful()) {
                            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());
                            $photoPath = $filename;
                        } else {
                            $this->warn("Image server responded with error for $name");
                        }
                    } catch (\Exception $e) {
                        $this->warn("Image download timeout or error for $name. Skipping image: " . $e->getMessage());
                    }
                }
            }

            // 3. Create Draft Doctor
            $doctor = Doctor::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'gender' => $gender,
                    'designation' => $designation,
                    'qualifications' => $qualifications,
                    'photo' => $photoPath,
                    'status' => 'draft',
                    'import_source' => 'popular_diagnostic',
                'verified' => false,
                'view_count' => 0
            ]);

            // 4. Handle Specialty
            if (!empty($specialtyName)) {
                // Split by comma, slash, and ampersand to prevent long compound specialties
                $sString = str_replace(['&', '/', '|'], ',', $specialtyName);
                $specialtiesList = array_filter(array_map('trim', explode(',', $sString)));
                
                foreach ($specialtiesList as $sName) {
                    if (strlen($sName) < 3) continue; // Skip extremely short acronyms accidentally generated

                    $sSlug = Str::slug($sName);
                    if (!empty($sSlug)) {
                        $spec = Specialty::firstOrCreate(
                            ['slug' => $sSlug],
                            ['name' => ['en' => $sName, 'bn' => $sName]]
                        );
                        $doctor->specialties()->attach($spec->id);
                    }
                }
            }

            // 5. Handle Chamber / Branch
            if (!empty($branchName)) {
                // Map the branch name to Popular Diagnostic Center ...
                $fullHospitalName = "Popular Diagnostic Center, " . $branchName;
                $hSlug = Str::slug($fullHospitalName);

                // Branch specific static data mapping
                $branchData = [
                    'Dhanmondi' => ['address' => 'House-16, Road- 2, Dhanmondi, Dhaka-1205', 'phone' => '09613 787801', 'area' => 'Dhanmondi'],
                    'English road' => ['address' => 'House-2, English Road, Dhaka', 'phone' => '09613 787802', 'area' => 'Kotwali'],
                    'Shantinagar' => ['address' => 'Unit- 01, House- 11, Shantinagar, Dhaka', 'phone' => '09613 787803', 'area' => 'Shantinagar'],
                    'Badda' => ['address' => 'Cha-90/2, North Badda (Pragoti Sarani), Dhaka', 'phone' => '09613 787809', 'area' => 'Badda'],
                    'Mirpur' => ['address' => 'House- 67, Block- C, Section- 11, Mirpur, Dhaka', 'phone' => '09613 787807', 'area' => 'Mirpur'],
                    'Uttara Jashim Uddin (Sector-04)' => ['address' => 'House-21, Road-7, Sector-4, Jashim Uddin, Uttara, Dhaka', 'phone' => '09613 787805', 'area' => 'Uttara'],
                    'Uttara (Sector-09)' => ['address' => 'House-47, Road- 14, Sector- 9, Uttara, Dhaka', 'phone' => '09613 787805', 'area' => 'Uttara'],
                    'Narayangonj' => ['address' => '231/4, B.B. Road, Chashara, Narayanganj', 'phone' => '09613 787804', 'area' => 'Narayanganj'],
                    'Savar' => ['address' => 'B-73/2, Talbag, Savar, Dhaka', 'phone' => '09613 787808', 'area' => 'Savar'],
                    'Gazipur' => ['address' => 'Chandana Chowrasta, Gazipur', 'phone' => '09613 787815', 'area' => 'Gazipur'],
                    'Narsingdi' => ['address' => '135/2, B.M. Tower, C.W. Road, Narsingdi', 'phone' => '09613 787823', 'area' => 'Narsingdi'],
                    'Sylhet' => ['address' => 'Medical College Road, Rikabi Bazar, Sylhet', 'phone' => '09613 787810', 'area' => 'Sylhet'],
                    'Comilla' => ['address' => 'Jhawtola, Comilla', 'phone' => '09613 787812', 'area' => 'Comilla'],
                    'Noakhali' => ['address' => 'Hospital Road, Maijdee Court, Noakhali', 'phone' => '09613 787817', 'area' => 'Noakhali'],
                    'Chittagong' => ['address' => '20/B, K.B. Fazlul Kader Road, Panchlaish, Chittagong', 'phone' => '09613 787810', 'area' => 'Chittagong'],
                    'Rajshahi' => ['address' => 'Laxmipur, Rajshahi', 'phone' => '09613 787811', 'area' => 'Rajshahi'],
                    'Rangpur' => ['address' => 'Dhap, Jail Road, Rangpur', 'phone' => '09613 787813', 'area' => 'Rangpur'],
                    'Bogura' => ['address' => 'Thanthania, Sherpur Road, Bogura', 'phone' => '09613 787812', 'area' => 'Bogura'],
                    'Barisal' => ['address' => 'Kalibari Road, Barisal', 'phone' => '09613 787814', 'area' => 'Barisal'],
                    'Mymensingh' => ['address' => '171/1, Charpara, Mymensingh', 'phone' => '09613 787814', 'area' => 'Mymensingh'],
                    'Dinajpur' => ['address' => 'Ganeshtola, Dinajpur', 'phone' => '09613 787816', 'area' => 'Dinajpur'],
                    'Kushtia' => ['address' => 'Mazampur, Kushtia', 'phone' => '09613 787822', 'area' => 'Kushtia'],
                    'Faridpur' => ['address' => 'Goalchamot, Faridpur', 'phone' => '09613 787821', 'area' => 'Faridpur'],
                    'Pabna' => ['address' => 'Hospital Road, Pabna', 'phone' => '09613 787824', 'area' => 'Pabna']
                ];

                $tAddress = $branchData[$branchName]['address'] ?? "Popular Diagnostic Center, $branchName";
                $tPhone = $branchData[$branchName]['phone'] ?? "";
                $tAreaSearch = $branchData[$branchName]['area'] ?? $branchName;

                // Dynamically fetch the best matching area_id from DB
                $mappedArea = \App\Models\Area::whereRaw('LOWER(name) like ?', ['%' . strtolower($tAreaSearch) . '%'])->first();
                $areaId = $mappedArea ? $mappedArea->id : null;

                $hospital = Hospital::updateOrCreate(
                    ['slug' => $hSlug],
                    [
                        'name' => $fullHospitalName,
                        'type' => 'diagnostic',
                        'address' => $tAddress,
                        'phone' => $tPhone,
                        'area_id' => $areaId
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
                        'address' => $tAddress,
                        'phone' => $tPhone,
                        'area_id' => $areaId,
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

            $processedInThisChunk++;

            if ($chunkSize && $processedInThisChunk >= $chunkSize) {
                break;
            }
        }

        fclose($handle);

        if (feof($handle) || $csvLineIndex >= $totalLines) {
            \Illuminate\Support\Facades\Cache::put('popular_import_progress', [
                'status' => 'completed',
                'current' => $totalLines,
                'total' => $totalLines,
                'message' => "Import completed successfully! Total Processed: $totalLines. Total Flagged as Duplicates: $duplicateCount."
            ]);
            // Reset pointer
            \Illuminate\Support\Facades\Cache::forget('popular_import_pointer');
        }

        $this->info("Import completed successfully! Pointer is at $csvLineIndex.");
    }
}
