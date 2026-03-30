<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospital;
use App\Models\Chamber;

class FixDuplicateHospitals extends Command
{
    protected $signature = 'db:fix-duplicate-hospitals';
    protected $description = 'Merges duplicated hospital branches into their core parent hospitals.';

    public function handle()
    {
        // Get all hospitals created today (likely by the scraper)
        // We consider the original CSV hospitals as having smaller IDs or earlier timestamps than the newly scraped ones
        $recentHospitals = Hospital::orderByDesc('id')->get();
        
        $this->info("Scanning " . $recentHospitals->count() . " hospitals for duplicates...");
        $mergedCount = 0;

        foreach ($recentHospitals as $recent) {
            // Find an OLDER hospital that perfectly contains the name of this recent one, or vice-versa
            // We use id < recent->id to ensure we merge into the oldest (original) record
            $parent = Hospital::where('id', '<', $recent->id)
                ->where(function($query) use ($recent) {
                    $query->whereRaw('? LIKE CONCAT("%", name, "%")', [$recent->name])
                          ->orWhere('name', 'LIKE', '%' . $recent->name . '%');
                })
                ->whereRaw('LENGTH(name) > 5') // Prevent matching on something short like "The "
                ->first();

            if ($parent) {
                $this->info("Merging '{$recent->name}' -> '{$parent->name}'");
                
                // Move all chambers belonging to the recent duplicate to the parent
                Chamber::where('hospital_id', $recent->id)->update(['hospital_id' => $parent->id]);
                
                // Delete the duplicate hospital
                $recent->delete();
                $mergedCount++;
            }
        }

        $this->info("Complete! Merged and deleted $mergedCount duplicate branch hospitals.");
    }
}
