<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Str;

class FixMissingData extends Command
{
    protected $signature = 'db:fix-missing-data';
    protected $description = 'Extracts missing qualifications and specialties directly from the bio text locally';

    public function handle()
    {
        $doctors = Doctor::where('qualifications', '')
            ->orWhereNull('qualifications')
            ->orWhereDoesntHave('specialties')
            ->with('specialties')
            ->get();

        $this->info("Found {$doctors->count()} doctors needing data extraction from bio.");

        $qCount = 0;
        $sCount = 0;

        foreach ($doctors as $doctor) {
            $bio = $doctor->bio;
            if (!$bio) continue;

            // 1. Fix missing qualifications
            if (empty($doctor->qualifications)) {
                if (preg_match('/qualification is (.*?)\./i', $bio, $match)) {
                    $doctor->qualifications = trim($match[1]);
                    $doctor->save();
                    $qCount++;
                } else if (preg_match('/qualifications are (.*?)\./i', $bio, $match)) {
                    $doctor->qualifications = trim($match[1]);
                    $doctor->save();
                    $qCount++;
                } else if (preg_match('/qualifications including (.*?)\./i', $bio, $match)) {
                    $doctor->qualifications = trim($match[1]);
                    $doctor->save();
                    $qCount++;
                } else if (preg_match('/with qualifications (.*?)\./i', $bio, $match)) {
                    $doctor->qualifications = trim($match[1]);
                    $doctor->save();
                    $qCount++;
                } else if (preg_match('/(MBBS[A-Za-z0-9,\s\(\)\.\-]*|BDS[A-Za-z0-9,\s\(\)\.\-]*)(?=\.|\sShe|\sHe)/i', $bio, $match)) {
                    // Fallback to catching naked degrees directly if there is no intro text
                    $doctor->qualifications = trim($match[1]);
                    $doctor->save();
                    $qCount++;
                }
            }

            // 2. Fix missing specialties
            if ($doctor->specialties->isEmpty()) {
                $specText = '';
                if (preg_match('/is a skilled (.*?) Specialist/i', $bio, $match) || preg_match('/is a (.*?) Specialist/i', $bio, $match)) {
                    $specText = trim(explode(' in ', $match[1])[0]);
                } else if (preg_match('/is a (.*?) Doctor/i', $bio, $match)) {
                    $specText = trim(explode(' in ', $match[1])[0]);
                } else if (preg_match('/is an (.*?) Specialist/i', $bio, $match) || preg_match('/is an (.*?) Doctor/i', $bio, $match)) {
                    $specText = trim(explode(' in ', $match[1])[0]);
                }

                if (!empty($specText)) {
                    $baseSpecialty = $this->getBaseSpecialty($specText);
                    $trueSlug = Str::slug($baseSpecialty);
                    $trueSpecialty = Specialty::firstOrCreate(
                        ['slug' => $trueSlug],
                        ['name' => ['en' => $baseSpecialty, 'bn' => $baseSpecialty]]
                    );
                    
                    $doctor->specialties()->syncWithoutDetaching([$trueSpecialty->id]);
                    $sCount++;
                }
            }
        }

        $this->info("Successfully extracted $qCount qualifications and $sCount specialties from raw bios!");
    }

    private function getBaseSpecialty($nameRaw)
    {
        $name = strtolower($nameRaw);
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

        foreach ($map as $keyword => $standardName) {
            if (str_contains($name, $keyword)) {
                return $standardName;
            }
        }

        return 'General Physician';
    }
}
