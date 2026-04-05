<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Chamber;
use App\Models\Hospital;
use Illuminate\Support\Str;

class SyncPopularBranchData extends Command
{
    protected $signature = 'sync:popular-branch-data';
    protected $description = 'Quickly synchronize missing branch specific phone number and address data for previously imported Popular Diagnostic doctors without downloading images.';

    public function handle()
    {
        $filePath = base_path('popular_diagnostic_20260404_2118.csv');

        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: $filePath");
            return;
        }

        $this->info("Starting fast synchronization of Popular Branch Data...");

        $handle = fopen($filePath, 'r');
        $totalLines = count(file($filePath, FILE_SKIP_EMPTY_LINES)) - 1;
        $headers = fgetcsv($handle);
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        $csvLineIndex = 0;
        $updatedCount = 0;

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

        while (($row = fgetcsv($handle)) !== false) {
            $csvLineIndex++;

            if (count($row) !== count($headers)) continue;

            $data = array_combine($headers, $row);

            $name = trim($data['Name'] ?? '');
            if (empty($name)) continue;

            $branchName = trim($data['Branch'] ?? '');
            if(empty($branchName)) continue;

            // Search by Name and the generated Slug from before
            // We search first by slug to be highly accurate
            $slugSearch = Str::slug($name);
            $doctor = Doctor::where('name', $name)->orWhere('slug', 'like', $slugSearch.'%')->first();
            
            if (!$doctor) continue; // Not imported yet for some reason

            $fullHospitalName = "Popular Diagnostic Center, " . $branchName;
            $hSlug = Str::slug($fullHospitalName);
            
            // Normalize branch name if there are typos in CSV (e.g. trailing spaces)
            $matchedBranchData = null;
            $matchedBranchKey = null;
            foreach ($branchData as $key => $bd) {
                if (strtolower($key) === strtolower($branchName)) {
                    $matchedBranchData = $bd;
                    $matchedBranchKey = $key;
                    break;
                }
            }

            $tAddress = $matchedBranchData['address'] ?? "Popular Diagnostic Center, $branchName";
            $tPhone = $matchedBranchData['phone'] ?? "";
            $tAreaSearch = $matchedBranchData['area'] ?? $branchName;

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
            $visitingDays = trim($data['Visiting Days'] ?? '');
            $visitingTime = trim($data['Visiting Time'] ?? '');
            $hoursStr = "";
            if (!empty($visitingDays)) $hoursStr .= $visitingDays;
            if (!empty($visitingTime)) {
                $hoursStr .= empty($hoursStr) ? $visitingTime : ", " . $visitingTime;
            }

            // Sync Chamber 
            $chamber = Chamber::where('doctor_id', $doctor->id)->where('hospital_id', $hospital->id)->first();
            if ($chamber) {
                $chamber->update([
                    'address' => $tAddress,
                    'phone' => $tPhone,
                    'area_id' => $areaId,
                    'visiting_hours' => $hoursStr,
                ]);
            } else {
                 Chamber::create([
                    'doctor_id' => $doctor->id,
                    'hospital_id' => $hospital->id,
                    'name' => 'Popular Diagnostic Center',
                    'address' => $tAddress,
                    'phone' => $tPhone,
                    'area_id' => $areaId,
                    'visiting_hours' => $hoursStr,
                ]);
            }

            $updatedCount++;
            if ($updatedCount % 100 === 0) {
                $this->info("Synchronized $updatedCount doctors...");
            }
        }
        fclose($handle);

        $this->info("Completed! Synchronized branch details for $updatedCount doctors.");
    }
}
