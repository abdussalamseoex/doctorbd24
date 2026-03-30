<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Specialty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CleanSpecialties extends Command
{
    protected $signature = 'db:clean-specialties';
    protected $description = 'Cleans up and merges junk specialties extracted by the scraper';

    public function handle()
    {
        $specialties = Specialty::all();
        $this->info("Scanning {$specialties->count()} specialties...");

        // Master Mapping List
        $map = [
            'cardio' => 'Cardiology',
            'heart' => 'Cardiology',
            'neuro' => 'Neurology',
            'brain' => 'Neurology',
            'spine' => 'Neurology',
            'nephro' => 'Nephrology',
            'kidney' => 'Nephrology',
            'uro' => 'Urology',
            'gastro' => 'Gastroenterology',
            'liver' => 'Gastroenterology',
            'ortho' => 'Orthopedics',
            'bone' => 'Orthopedics',
            'gyne' => 'Gynecology',
            'obs' => 'Gynecology',
            'maternal' => 'Gynecology',
            'pedi' => 'Pediatrics',
            'paedi' => 'Pediatrics',
            'child' => 'Pediatrics',
            'neonat' => 'Pediatrics',
            'derm' => 'Dermatology',
            'skin' => 'Dermatology',
            'allergy' => 'Dermatology',
            'venereology' => 'Dermatology',
            'eye' => 'Ophthalmology',
            'opht' => 'Ophthalmology',
            'ent' => 'ENT',
            'ear' => 'ENT',
            'nose' => 'ENT',
            'throat' => 'ENT',
            'dent' => 'Dentistry',
            'tooth' => 'Dentistry',
            'maxillofacial' => 'Dentistry',
            'orthodont' => 'Dentistry',
            'medicin' => 'Medicine',
            'physician' => 'Medicine',
            'surg' => 'Surgery',
            'laparoscopic' => 'Surgery',
            'cancer' => 'Oncology',
            'onco' => 'Oncology',
            'tumor' => 'Oncology',
            'psych' => 'Psychiatry',
            'mental' => 'Psychiatry',
            'endo' => 'Endocrinology',
            'diabe' => 'Endocrinology',
            'thyroid' => 'Endocrinology',
            'hormon' => 'Endocrinology',
            'rheumatolog' => 'Rheumatology',
            'arthritis' => 'Rheumatology',
            'pulmonolog' => 'Pulmonology',
            'chest' => 'Pulmonology',
            'asthma' => 'Pulmonology',
            'respiratory' => 'Pulmonology',
            'hemat' => 'Hematology',
            'haemat' => 'Hematology',
            'blood' => 'Hematology',
            'radio' => 'Radiology',
            'sonolog' => 'Radiology',
            'patholog' => 'Pathology',
            'physiotherap' => 'Physiotherapy',
            'rehabilitat' => 'Physiotherapy',
            'nutri' => 'Nutritionist',
            'diet' => 'Nutritionist',
            'homeopath' => 'Homeopathy',
            'anaes' => 'Anesthesiology',
            'anes' => 'Anesthesiology',
            'icu' => 'Intensive Care Unit',
        ];

        $deletedCount = 0;
        $mergedCount = 0;

        foreach ($specialties as $specialty) {
            $name = strtolower(json_decode($specialty->name, true)['en'] ?? $specialty->name);
            $slug = strtolower($specialty->slug);

            // Is it already a clean base specialty?
            if (in_array(ucwords($name), array_values($map))) {
                continue;
            }

            // Let's find its true base
            $baseSpecialty = null;
            foreach ($map as $keyword => $standardName) {
                if (str_contains($name, $keyword) || str_contains($slug, $keyword)) {
                    $baseSpecialty = $standardName;
                    break;
                }
            }

            // If we couldn't match anything, default to General Physician
            if (!$baseSpecialty) {
                $baseSpecialty = 'General Physician';
            }

            // Create or get the True Base Specialty
            $trueSlug = Str::slug($baseSpecialty);
            $trueSpecialty = Specialty::firstOrCreate(
                ['slug' => $trueSlug],
                ['name' => ['en' => $baseSpecialty, 'bn' => $baseSpecialty]]
            );

            // If they are different IDs, merge them safely
            if ($specialty->id !== $trueSpecialty->id) {
                // Safely migrate doctors using Eloquent
                $doctors = $specialty->doctors()->get();
                foreach ($doctors as $doc) {
                    $doc->specialties()->syncWithoutDetaching([$trueSpecialty->id]);
                    $doc->specialties()->detach($specialty->id);
                }

                // Delete the bad specialty
                $specialty->delete();
                $deletedCount++;
            }
        }

        $this->info("Process complete! Deleted $deletedCount junk specialties and merged them to their absolute root category.");
    }
}
