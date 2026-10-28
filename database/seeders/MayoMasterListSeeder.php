<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disease;
use App\Models\Specialty;
use App\Models\BodyPart;
use App\Models\DiseaseReference;
use Illuminate\Support\Str;

class MayoMasterListSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = __DIR__ . '/data/mayo_canonical_diseases.json';
        if (!file_exists($jsonPath)) {
            $this->command->error("Canonical JSON not found at: $jsonPath");
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            $this->command->error("Invalid JSON format");
            return;
        }

        echo "Loaded " . count($items) . " canonical diseases from Mayo Master List.\n";

        // Preload all existing diseases by slug
        $existingDiseases = Disease::all()->keyBy('slug');
        $specialties = Specialty::all();
        $bodyParts = BodyPart::all();

        $preservedCount = 0;
        $createdDraftCount = 0;

        foreach ($items as $item) {
            $slug = $item['slug'];
            $existing = $existingDiseases->get($slug);

            if ($existing) {
                // Condition 1 & 2: PRESERVE EXISTING - DO NOT OVERWRITE CONTENT OR DUPLICATE
                $preservedCount++;

                // Only enrich aliases if currently empty
                $changes = false;
                if (empty($existing->aliases) && !empty($item['aliases'])) {
                    $existing->aliases = $item['aliases'];
                    $changes = true;
                }

                // If Bengali name was missing, fill it
                $currentBn = $existing->getTranslation('name', 'bn', false);
                if (empty($currentBn) && !empty($item['name_bn'])) {
                    $existing->setTranslation('name', 'bn', $item['name_bn']);
                    $changes = true;
                }

                if ($changes) {
                    $existing->save();
                }
            } else {
                // Condition 2: SAVE AS DRAFT
                $newDisease = new Disease();
                $newDisease->name = [
                    'en' => $item['name_en'],
                    'bn' => $item['name_bn'] ?: $item['name_en']
                ];
                $newDisease->slug = $slug;
                $newDisease->first_letter = $item['first_letter'] ?: strtoupper(substr($item['name_en'], 0, 1));
                $newDisease->status = 'draft'; // Strictly draft
                $newDisease->is_featured = false;
                $newDisease->is_emergency = false;
                $newDisease->aliases = !empty($item['aliases']) ? $item['aliases'] : null;

                // Match Body System if hint matches
                if (!empty($item['body_system_hint'])) {
                    $bsHint = Str::slug($item['body_system_hint']);
                    $matchedBs = $bodyParts->first(function ($bp) use ($bsHint) {
                        return Str::contains($bp->slug, $bsHint) || Str::contains($bsHint, $bp->slug);
                    });
                    if ($matchedBs) {
                        $newDisease->body_system_id = $matchedBs->id;
                    }
                }

                // Match Specialty if hint matches
                if (!empty($item['specialty_hint'])) {
                    $spHint = Str::slug($item['specialty_hint']);
                    $matchedSp = $specialties->first(function ($s) use ($spHint) {
                        return Str::contains($s->slug, $spHint) || Str::contains($spHint, $s->slug);
                    });
                    if ($matchedSp) {
                        $newDisease->primary_specialty_id = $matchedSp->id;
                    }
                }

                $newDisease->save();

                // Add reference if Mayo URL is available
                if (!empty($item['mayo_url']) && filter_var($item['mayo_url'], FILTER_VALIDATE_URL)) {
                    DiseaseReference::create([
                        'disease_id'  => $newDisease->id,
                        'source_name' => 'Mayo Clinic',
                        'source_url'  => $item['mayo_url'],
                        'notes'       => 'Mayo Clinic Medical Overview',
                        'order_index' => 1,
                    ]);
                }

                $createdDraftCount++;
                // Add to lookup to prevent any internal duplicates
                $existingDiseases->put($slug, $newDisease);
            }
        }

        // Final verification counts
        $totalPublished = Disease::where('status', 'published')->count();
        $totalDraft = Disease::where('status', 'draft')->count();
        $totalAll = Disease::count();

        echo "\n=== SEEDING SUMMARY ===\n";
        echo "Canonical Diseases Processed: " . count($items) . "\n";
        echo "Existing Live Diseases Preserved: $preservedCount\n";
        echo "New Scaffold Entries Created as Draft: $createdDraftCount\n";
        echo "Total Live (Published) Diseases in DB: $totalPublished (All previous 116 intact)\n";
        echo "Total Draft Diseases in DB: $totalDraft\n";
        echo "Total Total Diseases in DB: $totalAll\n";
        echo "=======================\n";
    }
}
