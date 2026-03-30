<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Chamber;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ImportDoctorsCsv extends Command
{
    protected $signature = 'import:doctors-csv {--limit=null}';
    protected $description = 'Import doctors from WordPress Data CSV';

    public function handle()
    {
        $filePath = base_path('WordPress Data/doctor-export-2026-03-26.csv');

        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: $filePath");
            return;
        }

        $this->info("Starting doctor CSV import...");

        $hospitalMap = $this->loadHospitalMap();
        if (count($hospitalMap) > 0) {
            $this->info("Loaded " . count($hospitalMap) . " hospitals for ID mapping.");
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);

        if (!$headers) {
            $this->error("CSV file is empty or missing headers.");
            fclose($handle);
            return;
        }

        // Clean any invisible characters from headers (e.g. BOM)
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        $limit = $this->option('limit');
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if ($limit && $count >= $limit) break;

            if (count($row) !== count($headers)) {
                $this->warn("Skipping a row due to mismatch in column counts.");
                continue;
            }

            $data = array_combine($headers, $row);

            $name = trim($data['post_title'] ?? ($data['doctors_name'] ?? ''));
            if (empty($name)) {
                continue;
            }

            $this->info("Processing Doctor: $name");

            $slug = $data['post_name'] ?? Str::slug($name);
            $bioEn = !empty(trim($data['post_content'] ?? '')) ? trim($data['post_content'] ?? '') : trim($data['about'] ?? '');
            $qualifications = !empty(trim($data['qualification'] ?? '')) ? trim($data['qualification'] ?? '') : trim($data['education'] ?? '');
            $designation = !empty(trim($data['position'] ?? '')) ? trim($data['position'] ?? '') : trim($data['ExperienceTranining'] ?? '');
            $gender = $data['main-gender'] ?? '';
            $phone = $data['phone'] ?? '';
            $photoUrl = $data['featured_image'] ?? '';
            $specialtiesStr = !empty(trim($data['speciality'] ?? '')) ? trim($data['speciality'] ?? '') : (!empty(trim($data['specialist'] ?? '')) ? trim($data['specialist'] ?? '') : trim($data['specialization'] ?? ''));

            // 1. Download image if exists
            $photoPath = null;
            if (!empty($photoUrl)) {
                $filename = basename(parse_url($photoUrl, PHP_URL_PATH));
                $path = "doctors/" . $filename;
                
                if (!Storage::disk('public')->exists($path)) {
                    try {
                        $contents = file_get_contents($photoUrl);
                        if ($contents !== false) {
                            Storage::disk('public')->put($path, $contents);
                            $photoPath = $path;
                        }
                    } catch (\Exception $e) {
                        $this->warn("Could not download image for $name: " . rtrim(substr($e->getMessage(), 0, 50)) . "...");
                    }
                } else {
                    $photoPath = $path;
                }
            }

            // 2. Insert/Update Doctor
            $doctor = Doctor::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'bio' => trim($bioEn),
                    'qualifications' => $qualifications,
                    'designation' => $designation,
                    'gender' => strtolower(trim($gender)),
                    'phone' => $phone,
                    'photo' => $photoPath,
                    'verified' => true,
                ]
            );

            // 3. Handle specialties
            if (!empty($specialtiesStr)) {
                // Specialties might be separated by commas
                $specNames = array_map('trim', explode(',', $specialtiesStr));
                $specialtyIds = [];
                foreach ($specNames as $specName) {
                    if (empty($specName)) continue;
                    $sSlug = Str::slug($specName);
                    if (empty($sSlug)) continue;

                    $spec = Specialty::firstOrCreate(
                        ['slug' => $sSlug],
                        ['name' => ['en' => $specName, 'bn' => $specName]]
                    );
                    $specialtyIds[] = $spec->id;
                }
                if (!empty($specialtyIds)) {
                    $doctor->specialties()->syncWithoutDetaching($specialtyIds);
                }
            }

            // 4. Handle Chambers (hospital, hospital_2)
            $this->attachChamber($doctor, $data, 'hospital', 'visiting_hour', 'address', 'appointment', $hospitalMap);
            $this->attachChamber($doctor, $data, 'hospital_2', 'visiting_hour_2', 'address_2', 'appointment_2', $hospitalMap);

            $count++;
        }

        fclose($handle);
        $this->info("Import completed successfully! Total processed: $count");
    }

    private function attachChamber($doctor, $data, $hospKey, $timeKey, $addrKey, $apptKey, $hospitalMap)
    {
        $hospVal = trim($data[$hospKey] ?? '');
        if (empty($hospVal)) return;

        $hospName = $hospVal;
        // If the value is a numeric ID, map it to the actual hospital name
        if (is_numeric($hospVal)) {
            if (isset($hospitalMap[$hospVal])) {
                $hospName = $hospitalMap[$hospVal];
            } else {
                $this->warn("Skipping orphaned hospital ID $hospVal (not in map).");
                return;
            }
        }

        // Try finding existing hospital safely (name could have extra words like Limited etc)
        $hospital = Hospital::where('name', 'LIKE', '%' . $hospName . '%')->first();

        // Fallback: create new hospital if not found
        if (!$hospital) {
            $slug = Str::slug($hospName);
            if (empty($slug)) {
                $slug = 'hospital-' . uniqid();
            } else {
                // Ensure unique slug
                if (Hospital::where('slug', $slug)->exists()) {
                    $slug .= '-' . rand(100, 999);
                }
            }

            $hospital = Hospital::create([
                'name' => $hospName,
                'slug' => $slug,
                'type' => 'hospital',
                'verified' => false,
            ]);
            $this->line("Created missing Hospital: $hospName");
        }

        // Appointment parsing (sometimes JSON format in CSV export)
        $apptRaw = trim($data[$apptKey] ?? '');
        $apptPhone = '';
        if (!empty($apptRaw)) {
            $jsonParsed = json_decode($apptRaw, true);
            if (is_array($jsonParsed) && isset($jsonParsed['title'])) {
                $apptPhone = trim($jsonParsed['title']);
            } else {
                $apptPhone = $apptRaw;
            }
        }

        Chamber::updateOrCreate(
            ['doctor_id' => $doctor->id, 'hospital_id' => $hospital->id],
            [
                'name' => $hospital->name, // Standardizing chamber name to hospital name
                'address' => trim($data[$addrKey] ?? ''),
                'visiting_hours' => trim($data[$timeKey] ?? ''),
                'phone' => $apptPhone,
            ]
        );
    }

    private function loadHospitalMap()
    {
        $map = [];
        $filePath = base_path('WordPress Data/hospital-export-2026-03-26.csv');
        if (!file_exists($filePath)) {
            return $map;
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);
        if (!$headers) return $map;
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;
            $data = array_combine($headers, $row);
            $id = $data['ID'] ?? '';
            $title = $data['post_title'] ?? '';
            if ($id && $title) {
                $map[$id] = trim($title);
            }
        }
        fclose($handle);
        return $map;
    }
}
