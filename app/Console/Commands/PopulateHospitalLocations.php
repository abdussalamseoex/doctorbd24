<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospital;
use App\Models\Area;
use App\Models\District;

class PopulateHospitalLocations extends Command
{
    protected $signature = 'db:populate-hospital-locations';
    protected $description = 'Fills missing area_id based on hospital and chamber address text';

    public function handle()
    {
        $hospitals = Hospital::whereNull('area_id')->get();
        $chambers = \App\Models\Chamber::whereNull('area_id')->get();
        
        $this->info("Scanning {$hospitals->count()} hospitals and {$chambers->count()} chambers for area intelligence...");

        // Load mappings
        $areas = Area::all();
        $districts = District::all(); // Preloaded just for fallback matching to assign a generic area if district matches

        $hCount = 0;
        $cCount = 0;

        foreach ($hospitals as $h) {
            $hCount += $this->assignAreaFromAddress($h, $h->address . ' ' . $h->name, $areas, $districts);
        }

        foreach ($chambers as $c) {
            $cCount += $this->assignAreaFromAddress($c, $c->address . ' ' . $c->name, $areas, $districts);
        }

        $this->info("Successfully populated location data for $hCount hospitals and $cCount chambers!");
    }

    private function assignAreaFromAddress($model, $address, $areas, $districts)
    {
        $address = strtolower($address);
        $foundArea = null;

        foreach ($areas as $area) {
            $areaNameEn = strtolower(json_decode($area->name, true)['en'] ?? $area->name);
            if (strlen($areaNameEn) > 3 && strpos($address, $areaNameEn) !== false) {
                $foundArea = $area;
                break;
            }
        }

        // If no strict area is found, check if a District is mentioned (e.g. 'Pabna', 'Sylhet', 'Chattogram')
        if (!$foundArea) {
            foreach ($districts as $district) {
                $districtNameEn = strtolower(json_decode($district->name, true)['en'] ?? $district->name);
                if (strlen($districtNameEn) > 3 && strpos($address, $districtNameEn) !== false) {
                    // Fallback: Bind to the first area in this district (often 'Sadar')
                    $foundArea = $areas->firstWhere('district_id', $district->id);
                    break;
                }
            }
        }

        // Extremely common Dhaka edge cases (Dhaka has hundreds of obscure areas not caught instantly)
        if (!$foundArea && strpos($address, 'dhaka') !== false) {
            // Pick a generic Dhaka Area (e.g. ID 400 or just find first within Dhaka District)
            $foundArea = $areas->first(function($a) {
                return $a->district_id == 47; // Assuming Dhaka is district 47 in typical BD SQL maps!
            });
        }

        if ($foundArea) {
            $model->area_id = $foundArea->id;
            $model->save();
            return 1;
        }

        return 0;
    }
}
