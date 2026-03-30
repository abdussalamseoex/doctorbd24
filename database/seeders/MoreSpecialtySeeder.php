<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class MoreSpecialtySeeder extends Seeder
{
    public function run()
    {
        // Fix gastro icon
        $gastro = Specialty::where('slug', 'gastroenterology')->first();
        if ($gastro) {
            $gastro->icon = '🥨';
            $gastro->save();
        }

        $moreSpecialties = [
            ['en' => 'Nephrology',          'bn' => 'কিডনি রোগ',             'slug' => 'nephrology',           'icon' => '🫘'],
            ['en' => 'Pulmonology',         'bn' => 'বক্ষব্যাধি (পালমোনোলজি)',     'slug' => 'pulmonology',          'icon' => '🫁'],
            ['en' => 'Allergy & Immunology','bn' => 'অ্যালার্জি ও ইমিউনোলজি',   'slug' => 'allergy-immunology',   'icon' => '🤧'],
            ['en' => 'Rheumatology',        'bn' => 'বাত রোগ (রিউম্যাটোলজি)',    'slug' => 'rheumatology',         'icon' => '🦵'],
            ['en' => 'Hematology',          'bn' => 'রক্তরোগ',              'slug' => 'hematology',           'icon' => '🩸'],
            ['en' => 'Neurosurgery',        'bn' => 'স্নায়ু সার্জারি',         'slug' => 'neurosurgery',         'icon' => '🧠'],
            ['en' => 'Pediatric Surgery',   'bn' => 'শিশু সার্জারি',          'slug' => 'pediatric-surgery',    'icon' => '🧸'],
            ['en' => 'Plastic Surgery',     'bn' => 'প্লাস্টিক সার্জারি',        'slug' => 'plastic-surgery',      'icon' => '🎭'],
            ['en' => 'Nutrition & Diet',    'bn' => 'পুষ্টিবিদ্যা',             'slug' => 'nutrition',            'icon' => '🥗'],
            ['en' => 'Physiotherapy',       'bn' => 'ফিজিওথেরাপি',           'slug' => 'physiotherapy',        'icon' => '💆'],
            ['en' => 'Anesthesiology',      'bn' => 'অ্যানেসথেসিয়া',          'slug' => 'anesthesiology',       'icon' => '💉'],
            ['en' => 'Radiology',           'bn' => 'রেডিওলজি',             'slug' => 'radiology',            'icon' => '☢️'],
            ['en' => 'Pathology',           'bn' => 'প্যাথলজি',             'slug' => 'pathology',            'icon' => '🔬'],
            ['en' => 'Family Medicine',     'bn' => 'ফ্যামিলি মেডিসিন',       'slug' => 'family-medicine',      'icon' => '👨‍👩‍👧‍👦'],
            ['en' => 'Pain Management',     'bn' => 'পেইন ম্যানেজমেন্ট',       'slug' => 'pain-management',      'icon' => '🤕'],
            ['en' => 'Infertility',         'bn' => 'বন্ধ্যাত্ব বিশেষজ্ঞ',         'slug' => 'infertility',          'icon' => '🍼'],
        ];

        foreach ($moreSpecialties as $s) {
            Specialty::firstOrCreate(
                ['slug' => $s['slug']],
                ['name' => ['en' => $s['en'], 'bn' => $s['bn']], 'icon' => $s['icon']]
            );
        }
    }
}
