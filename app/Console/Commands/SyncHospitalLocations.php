<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospital;
use App\Models\Chamber;
use App\Models\District;
use App\Models\Area;
use Illuminate\Support\Str;

class SyncHospitalLocations extends Command
{
    protected $signature = 'db:sync-hospital-locations';
    protected $description = 'Sync hospital locations (division, district, area) from the WordPress hospital export CSV';

    public function handle()
    {
        $filePath = base_path('WordPress Data/hospital-export-2026-03-26.csv');
        if (!file_exists($filePath)) {
            $this->error("Hospital CSV file not found at: $filePath");
            return;
        }

        $this->info("Starting hospital location synchronization...");

        $h = fopen($filePath, 'r');
        $hds = fgetcsv($h);
        $hds[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $hds[0]);
        $hds = array_map('trim', $hds);

        $updatedHospitals = 0;
        $notFoundAreas = [];

        while(($r = fgetcsv($h)) !== false) {
            if(count($r) != count($hds)) continue;
            $data = array_combine($hds, $r);
            
            $name = trim($data['post_title'] ?? '');
            if (empty($name)) continue;

            $areaName = trim($data['area'] ?? '');
            $cityName = trim($data['city'] ?? '');
            
            if (empty($areaName) && empty($cityName)) continue;

            $hospital = Hospital::where('name', 'LIKE', '%' . $name . '%')->first();
            if (!$hospital) continue;

            $district = null;
            if ($cityName) {
                $district = District::where('slug', Str::slug($cityName))
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$cityName%"])
                    ->first();
            }

            $area = null;
            if ($areaName) {
                $q = Area::where('slug', Str::slug($areaName))
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$areaName%"]);
                
                if ($district) {
                    $q->where('district_id', $district->id);
                }
                $area = $q->first();
            }

            $areaId = $area ? $area->id : null;

            if (!$areaId && $district) {
                if ($areaName) {
                    $slug = Str::slug($areaName);
                    if (!Area::where('slug', $slug)->exists()) {
                         $area = Area::create([
                             'district_id' => $district->id,
                             'name' => ['en' => $areaName, 'bn' => $areaName],
                             'slug' => $slug
                         ]);
                         $areaId = $area->id;
                    }
                }
            }

            if (!$areaId && $district && empty($areaName)) {
                $area = Area::where('district_id', $district->id)->first();
                if ($area) $areaId = $area->id;
            }

            if ($areaId) {
                $hospital->area_id = $areaId;
                $hospital->save();
                $updatedHospitals++;
            } else {
                $notFoundAreas[] = "$areaName ($cityName)";
            }
        }
        fclose($h);

        $this->info("Updated Hospitals: $updatedHospitals");
        if (count($notFoundAreas) > 0) {
            $this->warn("Not Found / Unable to map Areas: " . count(array_unique($notFoundAreas)));
        }

        $this->info("Syncing area_id to linked Chambers...");
        $chambers = Chamber::all();
        $updatedChambers = 0;
        foreach($chambers as $chamber) {
            if ($chamber->hospital && $chamber->hospital->area_id) {
                $chamber->area_id = $chamber->hospital->area_id;
                $chamber->save();
                $updatedChambers++;
            }
        }
        $this->info("Updated Chambers: $updatedChambers");
        $this->info("Synchronization complete.");
    }
}
