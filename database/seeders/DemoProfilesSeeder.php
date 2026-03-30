<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Ambulance;
use App\Models\Specialty;
use App\Models\Area;
use App\Models\Chamber;

class DemoProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add 10 Hospitals
        for ($i = 1; $i <= 10; $i++) {
            $area = Area::inRandomOrder()->first();
            Hospital::create([
                'name' => "Demo Hospital $i",
                'slug' => Str::slug("Demo Hospital $i") . '-' . rand(100, 999),
                'type' => collect(['hospital', 'clinic', 'diagnostic'])->random(),
                'about' => "A robust demo hospital or clinic setup $i to demonstrate listing.",
                'phone' => "017" . rand(10000000, 99999999),
                'email' => "hospital$i@example.com",
                'website' => "https://demo-hospital-$i.com",
                'address' => "Demo Street " . rand(1, 100) . ", Selected Area",
                'area_id' => $area ? $area->id : null,
                'verified' => (bool)rand(0, 1),
                'featured' => (bool)rand(0, 1),
                'lat' => rand(237000, 238000) / 10000,
                'lng' => rand(903000, 904000) / 10000,
            ]);
        }

        // Add 10 Ambulances
        for ($i = 1; $i <= 10; $i++) {
            $area = Area::inRandomOrder()->first();
            Ambulance::create([
                'provider_name' => "Demo Ambulance Service $i",
                'slug' => Str::slug("Demo Ambulance Service $i") . '-' . rand(100, 999),
                'type' => collect(['ac', 'non_ac', 'icu', 'freezing'])->random(),
                'phone' => "018" . rand(10000000, 99999999),
                'area_id' => $area ? $area->id : null,
                'available_24h' => (bool)rand(0, 1),
            ]);
        }

        // Add 10 Doctors
        $specialties = Specialty::all();
        $hospitals = Hospital::inRandomOrder()->take(20)->get();

        for ($i = 1; $i <= 10; $i++) {
            $doc = Doctor::create([
                'name' => "Dr. Demo Specialist $i",
                'slug' => Str::slug("Dr. Demo Specialist $i") . '-' . rand(100, 999),
                'gender' => collect(['male', 'female'])->random(),
                'qualifications' => "MBBS, FCPS, MD (Demo $i)",
                'designation' => "Senior Consultant Demo $i",
                'bio' => "Dr. Specialist $i is a prominent demo medical professional dedicated to providing quality care. This is a testing profile with multiple chambers.",
                'experience_years' => rand(5, 30),
                'verified' => (bool)rand(0, 1),
                'featured' => (bool)rand(0, 1),
                'phone' => "019" . rand(10000000, 99999999),
                'email' => "dr.demo$i@example.com",
                'bmdc_number' => "A-" . rand(10000, 99999),
            ]);

            // Assign random specialties (1 to 3)
            if ($specialties->count() > 0) {
                $doc->specialties()->syncWithoutDetaching(
                    $specialties->random(rand(1, min(3, $specialties->count())))->pluck('id')->toArray()
                );
            }

            // Create 2-3 Chambers for each Doctor
            $numChambers = rand(2, 4);
            for ($j = 1; $j <= $numChambers; $j++) {
                $hospital = $hospitals->random();
                $area = $hospital->area ?? Area::inRandomOrder()->first();

                Chamber::create([
                    'doctor_id' => $doc->id,
                    'hospital_id' => $hospital->id,
                    'name' => "Demo Chamber $j at {$hospital->name}",
                    'address' => "Room " . rand(100, 500) . ", {$hospital->name}",
                    'area_id' => $area ? $area->id : null,
                    'visiting_hours' => collect(['Sat-Thu: 4PM-8PM', 'Sun-Wed: 10AM-1PM', 'Daily: 6PM-9PM (Fri off)'])->random(),
                    'phone' => "015" . rand(10000000, 99999999),
                    'lat' => $hospital->lat,
                    'lng' => $hospital->lng,
                ]);
            }
        }
    }
}
