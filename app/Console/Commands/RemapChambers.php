<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospital;
use App\Models\Chamber;
use Illuminate\Support\Str;

class RemapChambers extends Command
{
    protected $signature = 'db:remap-chambers';
    protected $description = 'Globally recalculates and remaps chamber hospital_ids based on a strict subset matching algorithm to undo previous mis-merges.';

    public function handle()
    {
        $chambers = Chamber::all();
        $this->info("Scanning {$chambers->count()} chambers to remap their hospitals...");
        
        $allHospitals = Hospital::withTrashed()->orderByRaw('LENGTH(name) DESC')->get();
        $remappedCount = 0;
        $createdCount = 0;

        foreach ($chambers as $chamber) {
            $hospitalNameRaw = trim($chamber->name);
            if (empty($hospitalNameRaw)) continue;

            // Advanced Address-Aware Fuzzy Match
            $matchedHospital = null;
            $highestScore = 0;
            
            $scrapedText = strtolower($chamber->name . ' ' . $chamber->address);
            $baseNameClean = strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $chamber->name)[0]));
            
            // Limit candidates to those sharing the first word, or fall back to all if short
            $candidates = Hospital::withTrashed()->where('name', 'LIKE', "%{$baseNameClean}%")->get();
            if ($candidates->isEmpty() && strlen($baseNameClean) > 4) {
                $candidates = Hospital::withTrashed()->get();
            }

            $ignore = ['hospital', 'clinic', 'center', 'centre', 'diagnostic', 'limited', 'ltd', 'complex', 'medical', 'services', 'care', 'health', 'house', 'road', 'dhaka', 'bhaban', 'market', 'tower', 'floor', 'room', 'block', 'sector', 'avenue', 'lane', 'building', 'street', 'city', 'pvt', 'private', 'unit', 'branch', 'general', 'medical', 'college', 'specialized'];

            $scrapedNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $chamber->name));
            preg_match_all('/\b[a-z]{4,}\b/i', $scrapedText, $scrapedWords);
            $scrapedWords = array_unique($scrapedWords[0] ?? []);

            foreach ($candidates as $h) {
                $score = 0;
                $dbName = strtolower($h->name);
                $dbAddress = strtolower((string)$h->address);
                $dbNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $dbName));
                
                // Exact name gives base score 100
                if ($scrapedNameClean === $dbNameClean) {
                    $score += 100;
                } elseif (strlen($dbNameClean) > 10 && str_contains($scrapedNameClean, $dbNameClean)) {
                    $score += 50;
                }

                // Keyword check for specific branch indicators
                preg_match_all('/\b[a-z]{4,}\b/i', $dbName . ' ' . $dbAddress, $dbWords);
                $dbWords = array_unique($dbWords[0] ?? []);

                foreach ($dbWords as $word) {
                    if (in_array($word, $ignore)) continue;
                    
                    if (in_array($word, $scrapedWords)) {
                        // Crucial branch names like "English" get heavy multiplier over generic words
                        if (str_contains($dbName, $word)) {
                            $score += 300;
                        } else {
                            $score += 10;
                        }
                    }
                }

                if ($score > $highestScore) {
                    $highestScore = $score;
                    $matchedHospital = $h;
                }
            }

            // Fallback to strict Slug search manually if scoring fails entirely
            if (!$matchedHospital) {
                $matchedHospital = Hospital::withTrashed()->where('slug', Str::slug($chamber->name))->first();
                
                if (!$matchedHospital) {
                    $matchedHospital = Hospital::create([
                        'slug' => Str::slug($chamber->name),
                        'name' => $chamber->name,
                        'type' => 'hospital',
                        'verified' => false
                    ]);
                    $createdCount++;
                }
            }

            // Restore the matched hospital if it was soft deleted, otherwise the UI might crash or throw constraints
            if ($matchedHospital && $matchedHospital->trashed()) {
                $matchedHospital->restore();
            }

            // Update if different
            if ($chamber->hospital_id !== $matchedHospital->id) {
                $chamber->hospital_id = $matchedHospital->id;
                $chamber->save();
                $remappedCount++;
            }
        }

        $this->info("Complete! Corrected hospital links for $remappedCount chambers. Created $createdCount new distinct hospitals.");
    }
}
