<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\Area;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Chamber;
use App\Models\District;
use App\Models\Division;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $patientRole = Role::firstOrCreate(['name' => 'patient']);

        // ── Admin user ────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@doctorbd24.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // ── Demo patient ──────────────────────────────────────────
        $patient = User::firstOrCreate(
            ['email' => 'patient@doctorbd24.com'],
            [
                'name'     => 'Demo Patient',
                'password' => Hash::make('password'),
            ]
        );
        $patient->assignRole($patientRole);

        // ── Divisions (Bangladesh 8 divisions) ───────────────────
        $divisions = [
            ['en' => 'Dhaka',       'bn' => 'ঢাকা',       'slug' => 'dhaka'],
            ['en' => 'Chittagong',  'bn' => 'চট্টগ্রাম',  'slug' => 'chittagong'],
            ['en' => 'Rajshahi',    'bn' => 'রাজশাহী',    'slug' => 'rajshahi'],
            ['en' => 'Khulna',      'bn' => 'খুলনা',       'slug' => 'khulna'],
            ['en' => 'Barishal',    'bn' => 'বরিশাল',      'slug' => 'barishal'],
            ['en' => 'Sylhet',      'bn' => 'সিলেট',       'slug' => 'sylhet'],
            ['en' => 'Rangpur',     'bn' => 'রংপুর',       'slug' => 'rangpur'],
            ['en' => 'Mymensingh',  'bn' => 'ময়মনসিংহ',  'slug' => 'mymensingh'],
        ];

        $divisionIds = [];
        foreach ($divisions as $d) {
            $div = Division::firstOrCreate(
                ['slug' => $d['slug']],
                ['name' => ['en' => $d['en'], 'bn' => $d['bn']]]
            );
            $divisionIds[$d['slug']] = $div->id;
        }

        // ── Districts (subset) ────────────────────────────────────
        $dhaka = $divisionIds['dhaka'];
        $ctg   = $divisionIds['chittagong'];
        $syl   = $divisionIds['sylhet'];

        $districts = [
            ['division_id' => $dhaka, 'en' => 'Dhaka',        'bn' => 'ঢাকা',       'slug' => 'dhaka-district'],
            ['division_id' => $dhaka, 'en' => 'Narayanganj',  'bn' => 'নারায়ণগঞ্জ', 'slug' => 'narayanganj'],
            ['division_id' => $dhaka, 'en' => 'Gazipur',      'bn' => 'গাজীপুর',    'slug' => 'gazipur'],
            ['division_id' => $ctg,   'en' => 'Chittagong',   'bn' => 'চট্টগ্রাম',  'slug' => 'chittagong-district'],
            ['division_id' => $ctg,   'en' => 'Cox\'s Bazar', 'bn' => 'কক্সবাজার',  'slug' => 'coxs-bazar'],
            ['division_id' => $syl,   'en' => 'Sylhet',       'bn' => 'সিলেট',       'slug' => 'sylhet-district'],
        ];

        $districtIds = [];
        foreach ($districts as $d) {
            $dist = District::firstOrCreate(
                ['slug' => $d['slug']],
                ['division_id' => $d['division_id'], 'name' => ['en' => $d['en'], 'bn' => $d['bn']]]
            );
            $districtIds[$d['slug']] = $dist->id;
        }

        // ── Areas ─────────────────────────────────────────────────
        $dhakaDistId = $districtIds['dhaka-district'];
        $ctgDistId   = $districtIds['chittagong-district'];

        $areas = [
            ['district_id' => $dhakaDistId, 'en' => 'Dhanmondi',     'bn' => 'ধানমন্ডি',      'slug' => 'dhanmondi'],
            ['district_id' => $dhakaDistId, 'en' => 'Gulshan',       'bn' => 'গুলশান',         'slug' => 'gulshan'],
            ['district_id' => $dhakaDistId, 'en' => 'Mirpur',        'bn' => 'মিরপুর',         'slug' => 'mirpur'],
            ['district_id' => $dhakaDistId, 'en' => 'Uttara',        'bn' => 'উত্তরা',         'slug' => 'uttara'],
            ['district_id' => $dhakaDistId, 'en' => 'Mohammadpur',   'bn' => 'মোহাম্মদপুর',   'slug' => 'mohammadpur'],
            ['district_id' => $dhakaDistId, 'en' => 'Shyamoli',      'bn' => 'শ্যামলী',        'slug' => 'shyamoli'],
            ['district_id' => $dhakaDistId, 'en' => 'Farmgate',      'bn' => 'ফার্মগেট',       'slug' => 'farmgate'],
            ['district_id' => $dhakaDistId, 'en' => 'Shahbagh',      'bn' => 'শাহবাগ',         'slug' => 'shahbagh'],
            ['district_id' => $ctgDistId,   'en' => 'Agrabad',       'bn' => 'আগ্রাবাদ',       'slug' => 'agrabad'],
            ['district_id' => $ctgDistId,   'en' => 'Nasirabad',     'bn' => 'নাসিরাবাদ',      'slug' => 'nasirabad'],
            ['district_id' => $ctgDistId,   'en' => 'Panchlaish',    'bn' => 'পাঁচলাইশ',       'slug' => 'panchlaish'],
        ];

        $areaIds = [];
        foreach ($areas as $a) {
            $area = Area::firstOrCreate(
                ['slug' => $a['slug']],
                ['district_id' => $a['district_id'], 'name' => ['en' => $a['en'], 'bn' => $a['bn']]]
            );
            $areaIds[$a['slug']] = $area->id;
        }

        // ── Specialties ───────────────────────────────────────────
        $specialtyList = [
            ['en' => 'Medicine',           'bn' => 'মেডিসিন',           'slug' => 'medicine',          'icon' => '💊'],
            ['en' => 'Cardiology',         'bn' => 'হৃদরোগ',            'slug' => 'cardiology',         'icon' => '❤️'],
            ['en' => 'Orthopedics',        'bn' => 'অর্থোপেডিক',        'slug' => 'orthopedics',        'icon' => '🦴'],
            ['en' => 'Gynecology',         'bn' => 'গাইনোকোলজি',        'slug' => 'gynecology',         'icon' => '👩'],
            ['en' => 'Pediatrics',         'bn' => 'শিশু রোগ',           'slug' => 'pediatrics',         'icon' => '👶'],
            ['en' => 'Dermatology',        'bn' => 'চর্মরোগ',            'slug' => 'dermatology',        'icon' => '🩺'],
            ['en' => 'Neurology',          'bn' => 'স্নায়ুরোগ',         'slug' => 'neurology',          'icon' => '🧠'],
            ['en' => 'Ophthalmology',      'bn' => 'চক্ষুরোগ',           'slug' => 'ophthalmology',      'icon' => '👁️'],
            ['en' => 'ENT',                'bn' => 'নাক কান গলা',         'slug' => 'ent',                'icon' => '👂'],
            ['en' => 'Dentistry',          'bn' => 'দন্তরোগ',            'slug' => 'dentistry',          'icon' => '🦷'],
            ['en' => 'Urology',            'bn' => 'ইউরোলজি',            'slug' => 'urology',            'icon' => '🩻'],
            ['en' => 'Gastroenterology',   'bn' => 'গ্যাস্ট্রোএন্ট্রোলজি','slug' => 'gastroenterology', 'icon' => '🫁'],
            ['en' => 'Psychiatry',         'bn' => 'মানসিক রোগ',          'slug' => 'psychiatry',        'icon' => '🧘'],
            ['en' => 'Endocrinology',      'bn' => 'এন্ডোক্রাইনোলজি',    'slug' => 'endocrinology',     'icon' => '⚗️'],
            ['en' => 'Oncology',           'bn' => 'ক্যান্সার রোগ',       'slug' => 'oncology',          'icon' => '🎗️'],
            ['en' => 'General Surgery',    'bn' => 'সাধারণ সার্জারি',     'slug' => 'general-surgery',   'icon' => '🔪'],
        ];

        $specialtyIds = [];
        foreach ($specialtyList as $s) {
            $spec = Specialty::firstOrCreate(
                ['slug' => $s['slug']],
                ['name' => ['en' => $s['en'], 'bn' => $s['bn']], 'icon' => $s['icon']]
            );
            $specialtyIds[$s['slug']] = $spec->id;
        }

        // ── Blog Categories ───────────────────────────────────────
        $blogCats = [
            ['name' => 'Health Tips',     'slug' => 'health-tips'],
            ['name' => 'Disease Info',    'slug' => 'disease-info'],
            ['name' => 'Medicine News',   'slug' => 'medicine-news'],
            ['name' => 'Nutrition',       'slug' => 'nutrition'],
        ];
        $blogCatIds = [];
        foreach ($blogCats as $bc) {
            $cat = BlogCategory::firstOrCreate(['slug' => $bc['slug']], ['name' => $bc['name']]);
            $blogCatIds[$bc['slug']] = $cat->id;
        }

        // ── Demo Hospitals ────────────────────────────────────────
        $hospitals = [
            [
                'name'     => 'Dhaka Medical College Hospital',
                'slug'     => 'dhaka-medical-college-hospital',
                'type'     => 'hospital',
                'about'    => 'One of the oldest and largest government hospitals in Bangladesh, providing comprehensive medical services.',
                'phone'    => '02-55165088',
                'email'    => 'dmch@gov.bd',
                'address'  => 'Bakshibazar, Dhaka-1000',
                'area_id'  => $areaIds['shahbagh'],
                'lat'      => 23.7252,
                'lng'      => 90.3991,
                'verified' => true,
                'featured' => true,
            ],
            [
                'name'     => 'Square Hospital',
                'slug'     => 'square-hospital',
                'type'     => 'hospital',
                'about'    => 'Square Hospital is a leading private hospital in Bangladesh offering world-class healthcare services.',
                'phone'    => '02-8159457',
                'email'    => 'info@squarehospital.com',
                'website'  => 'https://www.squarehospital.com',
                'address'  => '18/F Bir Uttam Qazi Nuruzzaman Sarak, West Panthapath, Dhaka 1205',
                'area_id'  => $areaIds['dhanmondi'],
                'lat'      => 23.7508,
                'lng'      => 90.3786,
                'verified' => true,
                'featured' => true,
            ],
            [
                'name'     => 'Chittagong Medical College Hospital',
                'slug'     => 'chittagong-medical-college-hospital',
                'type'     => 'hospital',
                'about'    => 'Premier government hospital serving the Chittagong division.',
                'phone'    => '031-630390',
                'address'  => 'Nasirabad, Chittagong',
                'area_id'  => $areaIds['nasirabad'],
                'lat'      => 22.3706,
                'lng'      => 91.8356,
                'verified' => true,
                'featured' => false,
            ],
            [
                'name'     => 'Popular Diagnostic Centre',
                'slug'     => 'popular-diagnostic-centre-dhaka',
                'type'     => 'diagnostic',
                'about'    => 'Leading diagnostic center with state-of-the-art laboratory and imaging services.',
                'phone'    => '02-9123701',
                'email'    => 'info@populardiagnostic.com',
                'website'  => 'https://www.populardiagnostic.com',
                'address'  => 'House 16, Road 2, Dhanmondi, Dhaka 1205',
                'area_id'  => $areaIds['dhanmondi'],
                'lat'      => 23.7461,
                'lng'      => 90.3742,
                'verified' => true,
                'featured' => false,
            ],
        ];

        $hospitalIds = [];
        foreach ($hospitals as $h) {
            $hosp = Hospital::firstOrCreate(['slug' => $h['slug']], $h);
            $hospitalIds[$h['slug']] = $hosp->id;
        }

        // ── Demo Doctors ──────────────────────────────────────────
        $doctors = [
            [
                'name'             => 'Prof. Dr. Abdul Karim',
                'slug'             => 'prof-dr-abdul-karim',
                'gender'           => 'male',
                'qualifications'   => 'MBBS, FCPS (Medicine), MD (Cardiology)',
                'designation'      => 'Professor & Head of Cardiology',
                'bio'              => 'Prof. Dr. Abdul Karim is a renowned cardiologist with over 25 years of experience in treating heart diseases.',
                'experience_years' => 25,
                'verified'         => true,
                'featured'         => true,
                'phone'            => '01711-000001',
                'email'            => 'drkarim@example.com',
                'bmdc_number'      => 'A-12345',
                'specialties'      => ['cardiology', 'medicine'],
                'chambers'         => [
                    [
                        'hospital_id'   => 'dhaka-medical-college-hospital',
                        'name'          => 'Dhaka Medical College Hospital',
                        'address'       => 'Bakshibazar, Dhaka-1000',
                        'area_id'       => 'shahbagh',
                        'visiting_hours'=> 'Sun-Thu: 9AM-1PM',
                        'phone'         => '02-55165088',
                        'lat'           => 23.7252, 'lng' => 90.3991,
                    ],
                    [
                        'hospital_id'   => 'square-hospital',
                        'name'          => 'Square Hospital',
                        'address'       => 'West Panthapath, Dhaka 1205',
                        'area_id'       => 'dhanmondi',
                        'visiting_hours'=> 'Sat & Mon: 5PM-8PM',
                        'phone'         => '01711-000001',
                        'lat'           => 23.7508, 'lng' => 90.3786,
                    ],
                ],
            ],
            [
                'name'             => 'Dr. Fatema Begum',
                'slug'             => 'dr-fatema-begum',
                'gender'           => 'female',
                'qualifications'   => 'MBBS, FCPS (Gynecology & Obstetrics)',
                'designation'      => 'Senior Consultant, Gynecology',
                'bio'              => 'Dr. Fatema Begum specializes in high-risk pregnancies and laparoscopic surgery with 18 years of experience.',
                'experience_years' => 18,
                'verified'         => true,
                'featured'         => true,
                'phone'            => '01711-000002',
                'specialties'      => ['gynecology'],
                'chambers'         => [
                    [
                        'hospital_id'   => 'square-hospital',
                        'name'          => 'Square Hospital',
                        'address'       => 'West Panthapath, Dhaka',
                        'area_id'       => 'dhanmondi',
                        'visiting_hours'=> 'Sat-Thu: 4PM-8PM',
                        'phone'         => '01711-000002',
                        'lat'           => 23.7508, 'lng' => 90.3786,
                    ],
                ],
            ],
            [
                'name'             => 'Dr. Rakibul Islam',
                'slug'             => 'dr-rakibul-islam',
                'gender'           => 'male',
                'qualifications'   => 'MBBS, DCH, FCPS (Pediatrics)',
                'designation'      => 'Consultant Pediatrician',
                'bio'              => 'Dr. Rakibul Islam is a dedicated pediatrician with expertise in neonatal care and childhood diseases.',
                'experience_years' => 12,
                'verified'         => true,
                'featured'         => false,
                'phone'            => '01711-000003',
                'specialties'      => ['pediatrics'],
                'chambers'         => [
                    [
                        'hospital_id'   => 'popular-diagnostic-centre-dhaka',
                        'name'          => 'Popular Diagnostic Centre',
                        'address'       => 'Dhanmondi, Dhaka',
                        'area_id'       => 'dhanmondi',
                        'visiting_hours'=> 'Daily: 6PM-9PM (Fri off)',
                        'phone'         => '01711-000003',
                        'lat'           => 23.7461, 'lng' => 90.3742,
                    ],
                ],
            ],
            [
                'name'             => 'Dr. Nasrin Akter',
                'slug'             => 'dr-nasrin-akter',
                'gender'           => 'female',
                'qualifications'   => 'MBBS, MCPS (Medicine), MD (Neurology)',
                'designation'      => 'Consultant Neurologist',
                'bio'              => 'Dr. Nasrin Akter is a skilled neurologist treating epilepsy, stroke, and migraine disorders.',
                'experience_years' => 15,
                'verified'         => true,
                'featured'         => false,
                'phone'            => '01711-000004',
                'specialties'      => ['neurology', 'medicine'],
                'chambers'         => [
                    [
                        'hospital_id'   => 'chittagong-medical-college-hospital',
                        'name'          => 'Chittagong Medical College Hospital',
                        'address'       => 'Nasirabad, Chittagong',
                        'area_id'       => 'nasirabad',
                        'visiting_hours'=> 'Sun-Thu: 10AM-1PM',
                        'phone'         => '031-630390',
                        'lat'           => 22.3706, 'lng' => 91.8356,
                    ],
                ],
            ],
            [
                'name'             => 'Dr. Sharif Hossain',
                'slug'             => 'dr-sharif-hossain',
                'gender'           => 'male',
                'qualifications'   => 'BDS, MS (Dentistry)',
                'designation'      => 'Dental Surgeon',
                'bio'              => 'Dr. Sharif Hossain provides comprehensive dental care including orthodontics and implants.',
                'experience_years' => 8,
                'verified'         => false,
                'featured'         => false,
                'phone'            => '01711-000005',
                'specialties'      => ['dentistry'],
                'chambers'         => [
                    [
                        'hospital_id'   => null,
                        'name'          => 'Smile Dental Clinic',
                        'address'       => 'House 5, Road 3, Gulshan-1, Dhaka',
                        'area_id'       => 'gulshan',
                        'visiting_hours'=> 'Sat-Thu: 10AM-8PM',
                        'phone'         => '01711-000005',
                        'lat'           => 23.7808, 'lng' => 90.4197,
                    ],
                ],
            ],
        ];

        foreach ($doctors as $d) {
            $specialties = $d['specialties'];
            $chambers    = $d['chambers'];
            unset($d['specialties'], $d['chambers']);

            $doctor = Doctor::firstOrCreate(['slug' => $d['slug']], $d);

            // attach specialties
            $specIds = array_map(fn($s) => $specialtyIds[$s], $specialties);
            $doctor->specialties()->syncWithoutDetaching($specIds);

            // create chambers
            foreach ($chambers as $c) {
                $c['area_id']    = $areaIds[$c['area_id']];
                $c['hospital_id'] = $c['hospital_id'] ? $hospitalIds[$c['hospital_id']] : null;
                Chamber::firstOrCreate(
                    ['doctor_id' => $doctor->id, 'name' => $c['name']],
                    array_merge($c, ['doctor_id' => $doctor->id])
                );
            }
        }

        // ── Demo Ambulances ───────────────────────────────────────
        $ambulances = [
            ['provider_name' => 'Dhaka Ambulance Service', 'type' => 'ac',     'phone' => '01700-111001', 'area_id' => 'dhanmondi',  'available_24h' => true],
            ['provider_name' => 'Gulshan Ambulance',       'type' => 'icu',    'phone' => '01700-111002', 'area_id' => 'gulshan',    'available_24h' => true],
            ['provider_name' => 'Mirpur Ambulance',        'type' => 'non_ac', 'phone' => '01700-111003', 'area_id' => 'mirpur',     'available_24h' => false],
            ['provider_name' => 'Uttara Ambulance',        'type' => 'ac',     'phone' => '01700-111004', 'area_id' => 'uttara',     'available_24h' => true],
            ['provider_name' => 'CTG ICU Ambulance',       'type' => 'icu',    'phone' => '01700-111005', 'area_id' => 'agrabad',    'available_24h' => true],
            ['provider_name' => 'Panchlaish Ambulance',    'type' => 'non_ac', 'phone' => '01700-111006', 'area_id' => 'panchlaish', 'available_24h' => false],
        ];

        foreach ($ambulances as $a) {
            $a['area_id'] = $areaIds[$a['area_id']];
            $a['slug']    = \Illuminate\Support\Str::slug($a['provider_name']) . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4));
            Ambulance::firstOrCreate(['phone' => $a['phone']], $a);
        }

        // ── Demo Blog Posts ───────────────────────────────────────
        $posts = [
            [
                'blog_category_id' => $blogCatIds['health-tips'],
                'user_id'          => $admin->id,
                'title'            => 'শীতকালে সুস্থ থাকার ১০টি টিপস',
                'slug'             => 'winter-health-tips-bangla',
                'excerpt'          => 'শীতকালে ঠান্ডা, সর্দি ও ভাইরাল সংক্রমণ থেকে বাঁচতে এই টিপসগুলো মেনে চলুন।',
                'body'             => '<p>শীতকালে শরীরের রোগ প্রতিরোধ ক্ষমতা কমে যায়। তাই এই সময় বিশেষ সতর্কতা প্রয়োজন।</p>
                                       <h2>১. পর্যাপ্ত পানি পান করুন</h2><p>শীতে পিপাসা কম লাগে, কিন্তু পানির চাহিদা একই থাকে।</p>
                                       <h2>২. ভিটামিন সি গ্রহণ করুন</h2><p>লেবু, আমলকী, পেয়ারা খান নিয়মিত।</p>',
                'meta_description' => 'শীতকালে সুস্থ থাকার ১০টি কার্যকর টিপস — ডাক্তারদের পরামর্শ।',
                'published_at'     => now()->subDays(5),
            ],
            [
                'blog_category_id' => $blogCatIds['disease-info'],
                'user_id'          => $admin->id,
                'title'            => 'Diabetes: Symptoms, Causes and Management',
                'slug'             => 'diabetes-symptoms-causes-management',
                'excerpt'          => 'Learn about the different types of diabetes, their symptoms, and how to manage blood sugar effectively.',
                'body'             => '<p>Diabetes mellitus is a metabolic disease that causes high blood sugar. Insulin, produced in the pancreas, helps cells absorb and use blood sugar for energy.</p>
                                       <h2>Types of Diabetes</h2><p>Type 1, Type 2, and Gestational Diabetes are the main types.</p>
                                       <h2>Common Symptoms</h2><ul><li>Frequent urination</li><li>Excessive thirst</li><li>Unexplained weight loss</li></ul>',
                'meta_description' => 'Comprehensive guide on diabetes symptoms, causes, risk factors and effective management strategies.',
                'published_at'     => now()->subDays(10),
            ],
            [
                'blog_category_id' => $blogCatIds['nutrition'],
                'user_id'          => $admin->id,
                'title'            => 'হৃদরোগ প্রতিরোধে খাদ্যাভ্যাস',
                'slug'             => 'heart-disease-prevention-diet',
                'excerpt'          => 'হৃদরোগের ঝুঁকি কমাতে কী খাবেন, কী খাবেন না — বিশেষজ্ঞদের পরামর্শ।',
                'body'             => '<p>হৃদরোগ বাংলাদেশে মৃত্যুর অন্যতম প্রধান কারণ। সঠিক খাদ্যাভ্যাসে এই ঝুঁকি অনেকটা কমানো সম্ভব।</p>
                                       <h2>উপকারী খাবার</h2><ul><li>মাছ (বিশেষত সামুদ্রিক মাছ)</li><li>সবুজ শাকসবজি</li><li>ওটমিল</li></ul>',
                'meta_description' => 'হৃদরোগ প্রতিরোধে সঠিক খাদ্যাভ্যাস — কী খাবেন, কী এড়িয়ে চলবেন।',
                'published_at'     => now()->subDays(3),
            ],
        ];

        foreach ($posts as $p) {
            BlogPost::firstOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
