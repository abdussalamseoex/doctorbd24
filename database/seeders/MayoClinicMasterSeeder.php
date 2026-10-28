<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BodyPart;
use App\Models\Symptom;
use App\Models\SymptomFactor;
use App\Models\Disease;
use App\Models\Specialty;
use App\Models\DiseaseReference;
use Illuminate\Support\Str;

class MayoClinicMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Body Parts (১০টি প্রধান শারীরিক অঞ্চল)
        $bodyPartsData = [
            [
                'slug' => 'head-brain',
                'name' => ['en' => 'Head & Brain', 'bn' => 'মাথা ও মস্তিষ্ক'],
                'icon' => 'brain',
                'order_index' => 1,
            ],
            [
                'slug' => 'eyes-vision',
                'name' => ['en' => 'Eyes & Vision', 'bn' => 'চোখ ও দৃষ্টি'],
                'icon' => 'eye',
                'order_index' => 2,
            ],
            [
                'slug' => 'ears-nose-throat',
                'name' => ['en' => 'Ears, Nose & Throat', 'bn' => 'কান, নাক ও গলা'],
                'icon' => 'ear',
                'order_index' => 3,
            ],
            [
                'slug' => 'chest-heart',
                'name' => ['en' => 'Chest & Heart', 'bn' => 'বুক ও হৃদপিণ্ড'],
                'icon' => 'heart',
                'order_index' => 4,
            ],
            [
                'slug' => 'lungs-respiratory',
                'name' => ['en' => 'Lungs & Respiratory', 'bn' => 'ফুসফুস ও শ্বাসযন্ত্র'],
                'icon' => 'lungs',
                'order_index' => 5,
            ],
            [
                'slug' => 'abdomen-digestive',
                'name' => ['en' => 'Abdomen & Digestive', 'bn' => 'পেট ও পরিপাকতন্ত্র'],
                'icon' => 'stomach',
                'order_index' => 6,
            ],
            [
                'slug' => 'pelvic-urinary',
                'name' => ['en' => 'Pelvic, Urinary & Reproductive', 'bn' => 'তলপেট, মূত্র ও প্রজননতন্ত্র'],
                'icon' => 'activity',
                'order_index' => 7,
            ],
            [
                'slug' => 'bones-joints',
                'name' => ['en' => 'Bones, Joints & Muscles', 'bn' => 'হাড়, জোড়া ও পেশি'],
                'icon' => 'bone',
                'order_index' => 8,
            ],
            [
                'slug' => 'skin-hair',
                'name' => ['en' => 'Skin & Hair', 'bn' => 'ত্বক ও চুল'],
                'icon' => 'sparkles',
                'order_index' => 9,
            ],
            [
                'slug' => 'whole-body',
                'name' => ['en' => 'Whole Body & General', 'bn' => 'সারা শরীর ও সাধারণ লক্ষণ'],
                'icon' => 'user',
                'order_index' => 10,
            ],
        ];

        $bodyParts = [];
        foreach ($bodyPartsData as $data) {
            $bodyParts[$data['slug']] = BodyPart::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        // 2. Create Core Symptoms (মাস্টার সিম্পটমস)
        $symptomsData = [
            // Head & Brain
            [
                'body_part' => 'head-brain',
                'slug' => 'headache',
                'name' => ['en' => 'Headache', 'bn' => 'মাথাব্যথা'],
                'first_letter' => 'H',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Pain in any region of the head, which may occur on one or both sides.',
                    'bn' => 'মাথার যেকোনো অংশে ব্যথা, যা একপাশে বা উভয়পাশে হতে পারে।'
                ]
            ],
            [
                'body_part' => 'head-brain',
                'slug' => 'dizziness-vertigo',
                'name' => ['en' => 'Dizziness / Vertigo', 'bn' => 'মাথা ঘোরা'],
                'first_letter' => 'D',
                'urgency_level' => 'urgent',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'A feeling of being lightheaded, woozy, or sensation that surroundings are spinning.',
                    'bn' => 'মাথা হালকা লাগা বা চারপাশ বনবন করে ঘোরার অনুভূতি।'
                ]
            ],
            [
                'body_part' => 'head-brain',
                'slug' => 'sudden-severe-headache',
                'name' => ['en' => 'Thunderclap Sudden Severe Headache', 'bn' => 'হঠাৎ তীব্র অসহ্য মাথাব্যথা'],
                'first_letter' => 'T',
                'urgency_level' => 'emergency',
                'is_emergency' => true,
                'is_featured' => false,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Sudden and severe headache that peaks within seconds, often like an explosion.',
                    'bn' => 'হঠাৎ শুরু হওয়া প্রচণ্ড মাথাব্যথা যা কয়েক সেকেন্ডের মধ্যে তীব্র আকার ধারণ করে।'
                ]
            ],

            // Chest & Heart
            [
                'body_part' => 'chest-heart',
                'slug' => 'chest-pain',
                'name' => ['en' => 'Chest Pain or Pressure', 'bn' => 'বুকে ব্যথা বা ভারী চাপ'],
                'first_letter' => 'C',
                'urgency_level' => 'emergency',
                'is_emergency' => true,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Discomfort or heavy squeezing pressure in the chest that may radiate to arm, jaw, or back.',
                    'bn' => 'বুকে তীব্র অস্বস্তি, ভারী পাথর চেপে বসার অনুভূতি যা হাত বা চোয়ালে ছড়িয়ে পড়তে পারে।'
                ]
            ],
            [
                'body_part' => 'chest-heart',
                'slug' => 'palpitations',
                'name' => ['en' => 'Heart Palpitations', 'bn' => 'বুক ধড়ফড় করা'],
                'first_letter' => 'H',
                'urgency_level' => 'urgent',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Feelings of having a fast-beating, fluttering, or pounding heart.',
                    'bn' => 'হৃদস্পন্দন দ্রুত হওয়া বা অস্বাভাবিক বুক ধড়ফড় করার অনুভূতি।'
                ]
            ],

            // Lungs & Respiratory
            [
                'body_part' => 'lungs-respiratory',
                'slug' => 'shortness-of-breath',
                'name' => ['en' => 'Shortness of Breath (Dyspnea)', 'bn' => 'শ্বাসকষ্ট'],
                'first_letter' => 'S',
                'urgency_level' => 'emergency',
                'is_emergency' => true,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Difficulty breathing or feeling like you cannot catch enough air.',
                    'bn' => 'শ্বাস নিতে কষ্ট হওয়া বা বুকে পর্যাপ্ত বাতাস না পাওয়ার অনুভূতি।'
                ]
            ],
            [
                'body_part' => 'lungs-respiratory',
                'slug' => 'chronic-cough',
                'name' => ['en' => 'Persistent Cough', 'bn' => 'দীর্ঘমেয়াদী কাশি'],
                'first_letter' => 'P',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'A cough that lingers for weeks, with or without phlegm.',
                    'bn' => 'কয়েক সপ্তাহের বেশি স্থায়ী কাশি, কফযুক্ত বা শুকনো।'
                ]
            ],
            [
                'body_part' => 'lungs-respiratory',
                'slug' => 'wheezing',
                'name' => ['en' => 'Wheezing (Whistling Breath)', 'bn' => 'শ্বাস নেওয়ার সময় বাঁশির মতো শব্দ'],
                'first_letter' => 'W',
                'urgency_level' => 'urgent',
                'is_emergency' => false,
                'is_featured' => false,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'A high-pitched whistling sound made while breathing.',
                    'bn' => 'শ্বাস-প্রশ্বাসের সময় বুক থেকে বাঁশির মতো শাঁ শাঁ আওয়াজ হওয়া।'
                ]
            ],

            // Abdomen & Digestive
            [
                'body_part' => 'abdomen-digestive',
                'slug' => 'abdominal-pain',
                'name' => ['en' => 'Abdominal Pain', 'bn' => 'পেটব্যথা'],
                'first_letter' => 'A',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Pain, cramp, or ache anywhere between the chest and groin.',
                    'bn' => 'বুকের নিচে থেকে তলপেট পর্যন্ত যেকোনো অংশে ব্যথা বা মোচড়।'
                ]
            ],
            [
                'body_part' => 'abdomen-digestive',
                'slug' => 'severe-lower-right-abdominal-pain',
                'name' => ['en' => 'Severe Lower Right Abdominal Pain', 'bn' => 'তলপেটের ডানপাশে তীব্র ব্যথা'],
                'first_letter' => 'S',
                'urgency_level' => 'emergency',
                'is_emergency' => true,
                'is_featured' => false,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Sudden sharp pain starting around navel and moving to lower right abdomen.',
                    'bn' => 'নাভির চারপাশ থেকে শুরু হয়ে তলপেটের ডানদিকে তীব্র আকার ধারণকারী ব্যথা (অ্যাপেন্ডিসাইটিস নির্দেশক)।'
                ]
            ],
            [
                'body_part' => 'abdomen-digestive',
                'slug' => 'heartburn-acid-reflux',
                'name' => ['en' => 'Heartburn & Acid Reflux', 'bn' => 'বুকজ্বালা ও টক ঢেকুর'],
                'first_letter' => 'H',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'A burning pain in chest or throat caused by stomach acid regurgitation.',
                    'bn' => 'গলার কাছে বা বুকে জ্বালাপোড়া ও মুখে টক জল ওঠার অস্বস্তি।'
                ]
            ],
            [
                'body_part' => 'abdomen-digestive',
                'slug' => 'nausea-vomiting',
                'name' => ['en' => 'Nausea and Vomiting', 'bn' => 'বমি বমি ভাব ও বমি'],
                'first_letter' => 'N',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Feeling sick to the stomach and involuntary emptying of stomach contents.',
                    'bn' => 'পেটে অস্বস্তি, গা গুলানো ও পেটের খাবার মুখ দিয়ে অনিচ্ছাকৃত বের হয়ে যাওয়া।'
                ]
            ],

            // Whole Body / General
            [
                'body_part' => 'whole-body',
                'slug' => 'high-fever',
                'name' => ['en' => 'High Fever', 'bn' => 'তীব্র জ্বর'],
                'first_letter' => 'H',
                'urgency_level' => 'urgent',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Body temperature above 101°F (38.3°C), often accompanied by chills and shivering.',
                    'bn' => 'শরীরের তাপমাত্রা ১০১° ফারেনহাইটের বেশি হওয়া, সাথে কাঁপুনি বা শীত শীত ভাব।'
                ]
            ],
            [
                'body_part' => 'whole-body',
                'slug' => 'unexplained-fatigue',
                'name' => ['en' => 'Extreme Fatigue & Weakness', 'bn' => 'অতিরিক্ত ক্লান্তি ও দুর্বলতা'],
                'first_letter' => 'E',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Constant feeling of exhaustion that does not improve with rest.',
                    'bn' => 'বিশ্রাম নেওয়ার পরও দূর না হওয়া সার্বক্ষণিক অবসাদ ও ক্লান্তি।'
                ]
            ],
            [
                'body_part' => 'whole-body',
                'slug' => 'unexplained-weight-loss',
                'name' => ['en' => 'Unexplained Weight Loss', 'bn' => 'অস্বাভাবিক ওজন হ্রাস'],
                'first_letter' => 'U',
                'urgency_level' => 'urgent',
                'is_emergency' => false,
                'is_featured' => false,
                'gender_scope' => 'all',
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Losing a noticeable amount of weight without dieting or exercise.',
                    'bn' => 'ডায়েট বা ব্যায়াম ছাড়াই শরীরের ওজন দ্রুত কমে যাওয়া।'
                ]
            ],

            // Bones & Joints
            [
                'body_part' => 'bones-joints',
                'slug' => 'joint-pain-swelling',
                'name' => ['en' => 'Joint Pain & Swelling', 'bn' => 'হাড়ের জোড়ায় ব্যথা ও ফোলা'],
                'first_letter' => 'J',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Discomfort, aches, and soreness in any of the body joints.',
                    'bn' => 'হাঁটু, কনুই, আঙুল বা কোমরের জোড়ায় শক্ত ভাব ও ব্যথা।'
                ]
            ],

            // Skin & Hair
            [
                'body_part' => 'skin-hair',
                'slug' => 'skin-rash-itching',
                'name' => ['en' => 'Skin Rash & Itching', 'bn' => 'ত্বকে র‍্যাশ ও চুলকানি'],
                'first_letter' => 'S',
                'urgency_level' => 'routine',
                'is_emergency' => false,
                'is_featured' => true,
                'gender_scope' => 'all',
                'age_group' => 'all',
                'description' => [
                    'en' => 'Red, irritated, swollen, or bumpy areas of skin that itch.',
                    'bn' => 'ত্বকে লালচে ফুসকুড়ি, চুলকানি বা খসখসে অনুভূতি।'
                ]
            ],
        ];

        $symptoms = [];
        foreach ($symptomsData as $data) {
            $bodyPartSlug = $data['body_part'];
            unset($data['body_part']);
            
            $bodyPart = $bodyParts[$bodyPartSlug] ?? null;
            $data['body_part_id'] = $bodyPart ? $bodyPart->id : null;
            $data['status'] = 'published';

            $symptoms[$data['slug']] = Symptom::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        // 3. Create Symptom Factors (ধাপ ৩-এর সম্পর্কিত প্রশ্নাবলি)
        // Factor for Headache
        if (isset($symptoms['headache'])) {
            $headache = $symptoms['headache'];
            
            SymptomFactor::updateOrCreate(
                ['symptom_id' => $headache->id, 'factor_type' => 'quality'],
                [
                    'question' => [
                        'en' => 'What is the nature or character of your headache?',
                        'bn' => 'আপনার মাথাব্যথার ধরন বা প্রকৃতি কেমন?'
                    ],
                    'options' => [
                        ['id' => 'throbbing_one_side', 'text_en' => 'Throbbing/pulsing on one side of head', 'text_bn' => 'মাথার একপাশে দপদপ করা তীব্র ব্যথা', 'red_flag' => false, 'score' => 8],
                        ['id' => 'dull_band_pressure', 'text_en' => 'Dull aching band of pressure around forehead', 'text_bn' => 'কপালে ফিতা বা ব্যান্ড বাঁধার মতো ভোঁতা চাপ', 'red_flag' => false, 'score' => 5],
                        ['id' => 'sharp_behind_eye', 'text_en' => 'Sharp piercing pain behind one eye', 'text_bn' => 'এক চোখের পেছনে বা চারপাশে সূচ ফোটানোর মতো তীব্র ব্যথা', 'red_flag' => false, 'score' => 7],
                        ['id' => 'explosive_thunderclap', 'text_en' => 'Worst headache of life, sudden explosion', 'text_bn' => 'জীবনের সবচেয়ে অসহ্য মাথাব্যথা যা হঠাৎ শুরু হয়েছে', 'red_flag' => true, 'score' => 10],
                    ],
                    'order_index' => 1
                ]
            );

            SymptomFactor::updateOrCreate(
                ['symptom_id' => $headache->id, 'factor_type' => 'accompanying'],
                [
                    'question' => [
                        'en' => 'Do you experience any of these accompanying signs?',
                        'bn' => 'মাথাব্যথার সাথে আর কী কী লক্ষণ রয়েছে?'
                    ],
                    'options' => [
                        ['id' => 'nausea_light_sensitivity', 'text_en' => 'Nausea and sensitivity to bright light or sound', 'text_bn' => 'বমি বমি ভাব এবং আলো বা শব্দ সহ্য করতে না পারা', 'red_flag' => false, 'score' => 8],
                        ['id' => 'stiff_neck_fever', 'text_en' => 'Stiff neck, high fever, or confusion', 'text_bn' => 'ঘাড় শক্ত হয়ে যাওয়া, তীব্র জ্বর বা আবোল-তাবোল বলা', 'red_flag' => true, 'score' => 10],
                        ['id' => 'facial_pressure_runny_nose', 'text_en' => 'Facial fullness, sinus pressure, or nasal congestion', 'text_bn' => 'মুখে বা নাকের দুই পাশে চাপ ও নাক বন্ধ থাকা', 'red_flag' => false, 'score' => 5],
                    ],
                    'order_index' => 2
                ]
            );
        }

        // Factor for Chest Pain
        if (isset($symptoms['chest-pain'])) {
            $chestPain = $symptoms['chest-pain'];
            
            SymptomFactor::updateOrCreate(
                ['symptom_id' => $chestPain->id, 'factor_type' => 'quality'],
                [
                    'question' => [
                        'en' => 'How does the chest discomfort feel?',
                        'bn' => 'বুকে ব্যথার অনুভূতিটা কেমন?'
                    ],
                    'options' => [
                        ['id' => 'crushing_pressure', 'text_en' => 'Heavy crushing weight, tightness or squeezing', 'text_bn' => 'বুকে ভারী পাথর চেপে বসার মতো দমবন্ধ চাপ', 'red_flag' => true, 'score' => 10],
                        ['id' => 'burning_after_meal', 'text_en' => 'Burning sensation moving up towards throat after eating', 'text_bn' => 'খাওয়ার পর গলার দিকে বুক জ্বালাপোড়া ও টক ঢেকুর', 'red_flag' => false, 'score' => 4],
                        ['id' => 'sharp_with_breath', 'text_en' => 'Sharp needle-like pain that worsens when breathing deeply', 'text_bn' => 'গভীর নিঃশ্বাস নিলে বা কাশির সময় সূঁচ ফোটানোর মতো ব্যথা', 'red_flag' => false, 'score' => 6],
                    ],
                    'order_index' => 1
                ]
            );

            SymptomFactor::updateOrCreate(
                ['symptom_id' => $chestPain->id, 'factor_type' => 'accompanying'],
                [
                    'question' => [
                        'en' => 'Does the pain spread or come with any of these symptoms?',
                        'bn' => 'ব্যথা কি শরীরের অন্য কোথাও ছড়িয়ে পড়ছে বা সাথে কী লক্ষণ আছে?'
                    ],
                    'options' => [
                        ['id' => 'radiating_arm_jaw_sweat', 'text_en' => 'Spreading to left arm, neck, jaw, along with cold sweats', 'text_bn' => 'বাম হাত, কাঁধ বা চোয়ালে ছড়িয়ে পড়ছে এবং প্রচণ্ড ঘাম হচ্ছে', 'red_flag' => true, 'score' => 10],
                        ['id' => 'shortness_breath_dizziness', 'text_en' => 'Severe shortness of breath, dizziness or fainting', 'text_bn' => 'মারাত্মক শ্বাসকষ্ট, মাথা ঘোরা বা চোখে অন্ধকার দেখা', 'red_flag' => true, 'score' => 10],
                        ['id' => 'relieved_by_antacid', 'text_en' => 'Relieved after drinking water or taking antacids', 'text_bn' => 'পানি খেলে বা অ্যান্টাসিড খাওয়ার পর কমে যায়', 'red_flag' => false, 'score' => 3],
                    ],
                    'order_index' => 2
                ]
            );
        }

        // 4. Create Benchmark Diseases (মেয়ো ক্লিনিক মানসম্পন্ন প্রাথমিক রোগসমূহ)
        $neurology = Specialty::where('slug', 'neurology')->first();
        $cardiology = Specialty::where('slug', 'cardiology')->first();
        $endocrinology = Specialty::where('slug', 'endocrinology')->orWhere('slug', 'diabetes')->first();
        $medicine = Specialty::where('slug', 'medicine')->orWhere('slug', 'medicine-specialist')->first();
        $pulmonology = Specialty::where('slug', 'pulmonology')->orWhere('slug', 'chest-medicine')->first();
        $surgery = Specialty::where('slug', 'general-surgery')->orWhere('slug', 'surgery')->first();

        // Disease 1: Migraine (মাইগ্রেন)
        $migraine = Disease::updateOrCreate(
            ['slug' => 'migraine'],
            [
                'primary_specialty_id' => $neurology ? $neurology->id : null,
                'name' => [
                    'en' => 'Migraine',
                    'bn' => 'মাইগ্রেন'
                ],
                'first_letter' => 'M',
                'is_featured' => true,
                'is_emergency' => false,
                'status' => 'published',
                'published_at' => now(),
                'overview' => [
                    'en' => '<p>A migraine is a neurological condition that can cause multiple symptoms, most notably a throbbing, pulsing headache usually on one side of the head. It is often accompanied by extreme sensitivity to light, sound, nausea, and vomiting. Migraine attacks can last for hours to days, significantly impairing daily activities.</p>',
                    'bn' => '<p>মাইগ্রেন হলো একটি বিশেষ ধরনের স্নায়বিক রোগ বা মাথাব্যথা, যা সাধারণত মাথার যেকোনো একপাশ থেকে শুরু হয়ে তীব্র দপদপে আকার ধারণ করে। এই ব্যথার সাথে প্রায়শই তীব্র আলো ও শব্দে অস্বস্তি, গা গুলানো এবং বমি হতে পারে। মাইগ্রেনের ব্যথা কয়েক ঘণ্টা থেকে শুরু করে ২-৩ দিন পর্যন্ত স্থায়ী হতে পারে এবং দৈনন্দিন স্বাভাবিক কাজকর্মে মারাত্মক ব্যাঘাত ঘটায়।</p>'
                ],
                'symptoms_summary' => [
                    'en' => '<ul><li>Moderate to severe throbbing pain, usually on one side of the head</li><li>Sensitivity to light (photophobia), sounds (phonophobia), and odors</li><li>Nausea and vomiting</li><li>Visual disturbances known as aura (flashing lights, zigzag patterns) before the headache</li><li>Dizziness or lightheadedness</li></ul>',
                    'bn' => '<ul><li>মাথার একপাশে মাঝারি থেকে তীব্র দপদপ করা ব্যথা</li><li>আলো, উচ্চ শব্দ বা কড়া গন্ধ সহ্য করতে না পারা</li><li>বমি ভাব বা বমি হওয়া</li><li>ব্যথা শুরু হওয়ার আগে চোখের সামনে আলোর ঝলকানি বা আঁকাবাঁকা রেখা দেখা (অরা / Aura)</li><li>মাথা ঘোরা ও তীব্র দুর্বলতা</li></ul>'
                ],
                'causes' => [
                    'en' => '<p>While the exact cause of migraines is not fully understood, genetics and environmental factors play a major role. Changes in brainstem pathways and fluctuations in neurotransmitters, especially serotonin, are closely involved.</p>',
                    'bn' => '<p>মাইগ্রেনের সঠিক কারণ পুরোপুরি স্পষ্ট না হলেও বংশগত কারণ এবং পরিবেশগত প্রভাব এতে বড় ভূমিকা রাখে। মস্তিষ্কের স্নায়ুতন্ত্রের রাসায়নিক ভারসাম্যহীনতা—বিশেষ করে সেরোটোনিন হরমোনের পরিবর্তনের কারণে রক্তনালীর প্রসারণ ঘটে এবং ব্যথা শুরু হয়।</p>'
                ],
                'risk_factors' => [
                    'en' => '<ul><li>Family history of migraine</li><li>Gender: Women are three times more likely to have migraines than men</li><li>Hormonal fluctuations during menstruation, pregnancy, or menopause</li><li>High stress levels and irregular sleep schedule</li></ul>',
                    'bn' => '<ul><li>পরিবারে কারও মাইগ্রেনের ইতিহাস থাকলে</li><li>পুরুষদের তুলনায় নারীদের এই রোগ হওয়ার সম্ভাবনা ৩ গুণ বেশি</li><li>মাসিক ঋতুস্রাব বা হরমোনের তারতম্য</li><li>অতিরিক্ত মানসিক চাপ, রাত জাগা ও অনিয়মিত ঘুম</li><li>দীর্ঘক্ষণ না খেয়ে থাকা বা ক্যাফেইনের অনিয়মিত ব্যবহার</li></ul>'
                ],
                'complications' => [
                    'en' => '<p>Frequent untreated migraines can progress into chronic migraines (headaches on 15 or more days a month) and medication-overuse headaches from excessive painkiller consumption.</p>',
                    'bn' => '<p>সঠিক সময়ে মাইগ্রেনের চিকিৎসা না করালে এটি ক্রনিক মাইগ্রেনে রূপ নিতে পারে (মাসে ১৫ দিনের বেশি ব্যথা থাকা)। এছাড়াও অতিরিক্ত ব্যথানাশক ওষুধ সেবনের ফলে ওষুধের পার্শ্বপ্রতিক্রিয়াজনিত জটিলতা দেখা দেয়।</p>'
                ],
                'prevention' => [
                    'en' => '<ul><li>Maintain a consistent daily sleep and meal routine</li><li>Identify and avoid personal dietary triggers (chocolate, aged cheese, processed meats)</li><li>Practice stress management through meditation or yoga</li><li>Stay well hydrated throughout the day</li></ul>',
                    'bn' => '<ul><li>প্রতিদিন নির্দিষ্ট সময়ে ঘুমাতে যাওয়া এবং পরিমিত ঘুমানো</li><li>নির্দিষ্ট সময়ে পুষ্টিকর খাবার খাওয়া, দীর্ঘক্ষণ না খেয়ে না থাকা</li><li>যেসব খাবার খেলে ব্যথা শুরু হয় (যেমন: অতিরিক্ত কফি, চকলেট, পনির) সেগুলো এড়িয়ে চলা</li><li>নিয়মিত পর্যাপ্ত পানি পান করা ও মানসিক চাপ মুক্ত থাকা</li></ul>'
                ],
                'when_to_see_doctor' => [
                    'en' => '<p>Seek emergency medical attention immediately if your headache comes on suddenly like a thunderclap, is accompanied by high fever and stiff neck, or causes confusion, seizures, numbness, or difficulty speaking.</p>',
                    'bn' => '<p>যদি মাথাব্যথা হঠাৎ প্রচণ্ড বিস্ফোরণের মতো শুরু হয়, সাথে তীব্র জ্বর ও ঘাড় শক্ত হয়ে যায়, অথবা কথা বলতে সমস্যা ও শরীরের একপাশ অবশ লাগে—তবে এটি স্ট্রোক বা মেনিনজাইটিসের লক্ষণ হতে পারে। অবিলম্বে হাসপাতালের জরুরি বিভাগে যান।</p>'
                ],
                'diagnosis_treatment' => [
                    'en' => '<p>Diagnosis is based on medical history, clinical evaluation, and neurological exam. In some cases, MRI or CT scans rule out other underlying causes. Treatment includes acute relief medications (triptans, NSAIDs) and preventive medications.</p>',
                    'bn' => '<p>রোগীর পূর্ব ইতিহাস ও স্নায়বিক পরীক্ষার মাধ্যমে মাইগ্রেন শনাক্ত করা হয়। অন্য কোনো সমস্যা আছে কিনা তা নিশ্চিত করতে চিকিৎসক এমআরআই (MRI) বা সিটি স্ক্যানের পরামর্শ দিতে পারেন। চিকিৎসার ক্ষেত্রে তাৎক্ষণিক ব্যথানাশক এবং নিয়মিত প্রতিরোধী ওষুধ দেওয়া হয়।</p>'
                ]
            ]
        );

        // Attach symptoms to Migraine
        if (isset($symptoms['headache'])) {
            $migraine->symptoms()->syncWithoutDetaching([
                $symptoms['headache']->id => ['is_primary' => true, 'match_weight' => 10],
            ]);
        }
        if (isset($symptoms['nausea-vomiting'])) {
            $migraine->symptoms()->syncWithoutDetaching([
                $symptoms['nausea-vomiting']->id => ['is_primary' => false, 'match_weight' => 7],
            ]);
        }
        if (isset($symptoms['dizziness-vertigo'])) {
            $migraine->symptoms()->syncWithoutDetaching([
                $symptoms['dizziness-vertigo']->id => ['is_primary' => false, 'match_weight' => 6],
            ]);
        }

        // References for Migraine
        DiseaseReference::updateOrCreate(
            ['disease_id' => $migraine->id, 'source_name' => 'Mayo Clinic'],
            [
                'source_url' => 'https://www.mayoclinic.org/diseases-conditions/migraine-headache/symptoms-causes/syc-20360201',
                'notes' => 'Clinical Overview, Symptoms & Prevention',
                'order_index' => 1
            ]
        );
        DiseaseReference::updateOrCreate(
            ['disease_id' => $migraine->id, 'source_name' => 'World Health Organization (WHO)'],
            [
                'source_url' => 'https://www.who.int/news-room/fact-sheets/detail/headache-disorders',
                'notes' => 'Headache Disorders & Global Statistics',
                'order_index' => 2
            ]
        );

        // Disease 2: Hypertension (উচ্চ রক্তচাপ)
        $hypertension = Disease::updateOrCreate(
            ['slug' => 'hypertension'],
            [
                'primary_specialty_id' => $cardiology ? $cardiology->id : ($medicine ? $medicine->id : null),
                'name' => [
                    'en' => 'Hypertension (High Blood Pressure)',
                    'bn' => 'উচ্চ রক্তচাপ (হাইপারটেনশন)'
                ],
                'first_letter' => 'H',
                'is_featured' => true,
                'is_emergency' => false,
                'status' => 'published',
                'published_at' => now(),
                'overview' => [
                    'en' => '<p>Hypertension, commonly known as high blood pressure, is a long-term medical condition in which the force of the blood against artery walls is consistently too high. Often called a "silent killer," it usually presents with no obvious symptoms until severe cardiovascular damage has occurred.</p>',
                    'bn' => '<p>উচ্চ রক্তচাপ বা হাইপারটেনশন একটি দীর্ঘমেয়াদী শারীরিক সমস্যা, যেখানে রক্তনালীর ভেতর দিয়ে রক্ত প্রবাহের চাপ স্বাভাবিক মাত্রার চেয়ে অনেক বেশি থাকে। একে প্রায়শই "নীরব ঘাতক" (Silent Killer) বলা হয়, কারণ দীর্ঘ সময় ধরে কোনো প্রকাশ্য লক্ষণ ছাড়াই এটি হৃদপিণ্ড, কিডনি ও মস্তিষ্কের মারাত্মক ক্ষতি করতে পারে।</p>'
                ],
                'symptoms_summary' => [
                    'en' => '<ul><li>Most people have no symptoms even at dangerous levels</li><li>Occasional dull headaches and dizziness</li><li>Shortness of breath during mild exertion</li><li>Nosebleeds in very severe crisis</li><li>Chest pain or visual changes in malignant hypertension</li></ul>',
                    'bn' => '<ul><li>বেশিরভাগ ক্ষেত্রেই কোনো প্রাথমিক লক্ষণ থাকে না</li><li>মাঝে মাঝে ঘাড় ও মাথার পেছনে ভারি ভাব বা মাথাব্যথা</li><li>মাথা ঘোরা ও চোখে ঝাপসা দেখা</li><li>হালকা পরিশ্রমে শ্বাসকষ্ট ও বুক ধড়ফড় করা</li><li>অতিরিক্ত বেড়ে গেলে নাক দিয়ে রক্ত পড়া</li></ul>'
                ],
                'causes' => [
                    'en' => '<p>Primary hypertension tends to develop gradually over years with no identifiable single cause. Secondary hypertension can be caused by underlying conditions such as kidney disease, adrenal gland tumors, obstructive sleep apnea, or thyroid problems.</p>',
                    'bn' => '<p>প্রাথমিক উচ্চ রক্তচাপের নির্দিষ্ট কোনো একক কারণ থাকে না, এটি বয়স ও জীবনযাত্রার সাথে ধীরে ধীরে গড়ে ওঠে। অন্যদিকে দ্বিতীয় পর্যায়ের উচ্চ রক্তচাপ কিডনির সমস্যা, থাইরয়েডের ব্যাধি, বা স্লিপ অ্যাপনিয়ার মতো অন্যান্য রোগের কারণে হয়ে থাকে।</p>'
                ],
                'risk_factors' => [
                    'en' => '<ul><li>Age: Risk increases as you get older</li><li>High sodium (salt) consumption</li><li>Lack of regular physical exercise and obesity</li><li>Tobacco smoking and excessive alcohol intake</li><li>Chronic stress and family history</li></ul>',
                    'bn' => '<ul><li>অতিরিক্ত কাঁচা লবণ বা লবণাক্ত খাবার খাওয়ার অভ্যাস</li><li>স্থূলতা বা অতিরিক্ত শারীরিক ওজন</li><li>কায়িক পরিশ্রম ও শরীরচর্চার অভাব</li><li>ধূমপান ও অ্যালকোহল সেবন</li><li>পরিবারে মা-বাবা বা ঘনিষ্ঠ স্বজনের উচ্চ রক্তচাপের ইতিহাস</li></ul>'
                ],
                'complications' => [
                    'en' => '<p>Uncontrolled high blood pressure can lead to heart attacks, stroke, heart failure, kidney failure, vision loss from retinopathy, and vascular dementia.</p>',
                    'bn' => '<p>নিয়ন্ত্রণহীন উচ্চ রক্তচাপের ফলে ব্রেন স্ট্রোক, হার্ট অ্যাটাক, হার্ট ফেইলিউর, কিডনি বিকল হওয়া এবং চোখের রেটিনা ক্ষতিগ্রস্ত হয়ে অন্ধত্ব পর্যন্ত হতে পারে।</p>'
                ],
                'prevention' => [
                    'en' => '<ul><li>Follow the DASH diet (rich in fruits, vegetables, and low-fat dairy)</li><li>Limit sodium intake to under 2,300 mg daily</li><li>Engage in at least 150 minutes of moderate aerobic exercise weekly</li><li>Quit smoking and limit alcohol</li><li>Monitor blood pressure regularly at home</li></ul>',
                    'bn' => '<ul><li>খাবারের সাথে বাড়তি লবণ খাওয়া সম্পূর্ণ পরিহার করা</li><li>প্রতিদিন অন্তত ৩০ মিনিট দ্রুত হাঁটা বা হালকা ব্যায়াম করা</li><li>শাকসবজি, ফলমূল ও আঁশযুক্ত খাবার বেশি খাওয়া</li><li>ধূমপান ও তামাকজাত পণ্য পুরোপুরি বর্জন করা</li><li>নিয়মিত প্রেসার মাপার অভ্যাস করা</li></ul>'
                ],
                'when_to_see_doctor' => [
                    'en' => '<p>If your blood pressure reading reaches 180/120 mmHg or higher and you have chest pain, shortness of breath, numbness, back pain, or vision changes, call emergency medical services immediately for a hypertensive crisis.</p>',
                    'bn' => '<p>যদি রক্তচাপ ১৮০/১২০ বা তার বেশি হয় এবং সাথে বুকে তীব্র ব্যথা, শ্বাসকষ্ট, শরীরের একপাশ অবশ লাগা বা চোখে দেখতে সমস্যা হয়—তবে কালবিলম্ব না করে নিকটস্থ হাসপাতালের ইমার্জেন্সিতে যান।</p>'
                ],
                'diagnosis_treatment' => [
                    'en' => '<p>Diagnosed using repeated blood pressure measurements using a sphygmomanometer. Treatment combines lifestyle modifications with antihypertensive medications such as ACE inhibitors, ARBs, Calcium Channel Blockers, or Diuretics as prescribed by a doctor.</p>',
                    'bn' => '<p>চিকিৎসক একাধিকবার প্রেসার মেপে উচ্চ রক্তচাপ শনাক্ত করেন। চিকিৎসায় জীবনযাত্রার পরিবর্তনের পাশাপাশি প্রয়োজন অনুযায়ী নিয়মিত প্রেসারের ওষুধ দেওয়া হয়। ডাক্তারের পরামর্শ ছাড়া কখনোই প্রেসারের ওষুধ বন্ধ করা উচিত নয়।</p>'
                ]
            ]
        );

        if (isset($symptoms['headache'])) {
            $hypertension->symptoms()->syncWithoutDetaching([
                $symptoms['headache']->id => ['is_primary' => false, 'match_weight' => 5],
            ]);
        }
        if (isset($symptoms['dizziness-vertigo'])) {
            $hypertension->symptoms()->syncWithoutDetaching([
                $symptoms['dizziness-vertigo']->id => ['is_primary' => false, 'match_weight' => 6],
            ]);
        }
        if (isset($symptoms['palpitations'])) {
            $hypertension->symptoms()->syncWithoutDetaching([
                $symptoms['palpitations']->id => ['is_primary' => false, 'match_weight' => 7],
            ]);
        }

        DiseaseReference::updateOrCreate(
            ['disease_id' => $hypertension->id, 'source_name' => 'Mayo Clinic'],
            [
                'source_url' => 'https://www.mayoclinic.org/diseases-conditions/high-blood-pressure/symptoms-causes/syc-20373410',
                'notes' => 'High Blood Pressure Symptoms & Causes',
                'order_index' => 1
            ]
        );
        DiseaseReference::updateOrCreate(
            ['disease_id' => $hypertension->id, 'source_name' => 'American Heart Association (AHA)'],
            [
                'source_url' => 'https://www.heart.org/en/health-topics/high-blood-pressure',
                'notes' => 'Hypertension Clinical Guidelines',
                'order_index' => 2
            ]
        );

        // Disease 3: Dengue Fever (ডেঙ্গু জ্বর)
        $dengue = Disease::updateOrCreate(
            ['slug' => 'dengue-fever'],
            [
                'primary_specialty_id' => $medicine ? $medicine->id : null,
                'name' => [
                    'en' => 'Dengue Fever',
                    'bn' => 'ডেঙ্গু জ্বর'
                ],
                'first_letter' => 'D',
                'is_featured' => true,
                'is_emergency' => false,
                'status' => 'published',
                'published_at' => now(),
                'overview' => [
                    'en' => '<p>Dengue is a mosquito-borne viral infection caused by the dengue virus, transmitted primarily by female Aedes mosquitoes. Common in tropical and subtropical climates, dengue causes flu-like illness that can occasionally develop into a potentially lethal complication called severe dengue (dengue hemorrhagic fever).</p>',
                    'bn' => '<p>ডেঙ্গু হলো এডিস মশাবাহিত একটি তীব্র ভাইরাসজনিত জ্বর। এটি বাংলাদেশসহ ক্রান্তীয় অঞ্চলের অন্যতম প্রধান স্বাস্থ্য সমস্যা। সাধারণ ডেঙ্গুতে তীব্র জ্বর ও প্রচণ্ড শরীরে ব্যথার পাশাপাশি সঠিক যত্ন না নিলে এটি মারাত্মক ডেঙ্গু শক সিন্ড্রোম বা হেমোরেজিক ফিভারে রূপ নিতে পারে।</p>'
                ],
                'symptoms_summary' => [
                    'en' => '<ul><li>Sudden high fever (104°F / 40°C)</li><li>Severe headache and pain behind the eyes (retro-orbital pain)</li><li>Excruciating joint and muscle pain ("breakbone fever")</li><li>Nausea, vomiting, and fatigue</li><li>Skin rash appearing 2 to 5 days after onset of fever</li></ul>',
                    'bn' => '<ul><li>হঠাৎ তীব্র জ্বর (১০৩°-১০৪° ফারেনহাইট)</li><li>চোখের পেছনের অংশে প্রচণ্ড ব্যথা এবং তীব্র মাথাব্যথা</li><li>মাংসপেশি ও হাড়ের জোড়ায় মারাত্মক ব্যথা (হাড়ভাঙা ব্যথা)</li><li>বমি বমি ভাব, ক্ষুধামন্দা ও অতিরিক্ত দুর্বলতা</li><li>জ্বরের ৩-৫ দিনের মাথায় ত্বকে লালচে র‍্যাশ বা দানা দেখা দেওয়া</li></ul>'
                ],
                'causes' => [
                    'en' => '<p>Caused by any of four closely related dengue virus serotypes (DENV-1, DENV-2, DENV-3, DENV-4). Transmitted to humans through the bites of infected female Aedes aegypti and Aedes albopictus mosquitoes.</p>',
                    'bn' => '<p>ডেঙ্গু ভাইরাসের ৪টি ধরনের (DENV-১, ২, ৩, ৪) যেকোনো একটির সংক্রমণের কারণে এই রোগ হয়। এডিস ইজিপ্টি মশার কামড়ে এই ভাইরাস মানবদেহে ছড়ায়। এডিস মশা সাধারণত দিনের বেলা, বিশেষ করে সকালে ও সন্ধ্যায় বেশি কামড়ায়।</p>'
                ],
                'risk_factors' => [
                    'en' => '<ul><li>Living in or traveling to tropical areas with high mosquito density</li><li>Previous infection with a different dengue serotype increases severe dengue risk</li><li>Accumulation of standing water in pots, roofs, or tires</li></ul>',
                    'bn' => '<ul><li>আবাসিক এলাকায় জমে থাকা পরিষ্কার পানিতে এডিস মশার প্রজনন</li><li>পূর্বে একবার ডেঙ্গুতে আক্রান্ত হওয়া (দ্বিতীয়বার অন্য সেরোটাইপে আক্রান্ত হলে তীব্র জটিলতার ঝুঁকি বাড়ে)</li><li>শিশুদের এবং বয়োবৃদ্ধদের ক্ষেত্রে অতিরিক্ত ঝুঁকি</li></ul>'
                ],
                'complications' => [
                    'en' => '<p>Severe dengue or Dengue Shock Syndrome involves severe plasma leakage, internal bleeding, severe organ impairment, dangerously low platelet counts, and shock.</p>',
                    'bn' => '<p>রক্তে প্লাটিলেট দ্রুত কমে যাওয়া, মাড়ি বা নাক দিয়ে রক্তক্ষরণ, পেটে পানি জমা এবং রক্তচাপ হঠাৎ বিপজ্জনকভাবে কমে ডেঙ্গু শক সিন্ড্রোমে পরিণত হওয়া।</p>'
                ],
                'prevention' => [
                    'en' => '<ul><li>Prevent mosquito bites by using repellents and mosquito nets even during daytime</li><li>Eliminate standing clean water from flowerpots, air conditioners, and discarded tires</li><li>Wear loose, long-sleeved clothing</li><li>Support community fogging and larvicide spraying</li></ul>',
                    'bn' => '<ul><li>ঘরের ভেতরে ও আশেপাশে কোনো পাত্রে ৩ দিনের বেশি পানি জমতে না দেওয়া</li><li>দিনের বেলা ঘুমানোর সময়ও মশারি ব্যবহার করা</li><li>ফুলহাতা জামা-কাপড় পরা এবং মস্কিটো রিপেলেন্ট ব্যবহার করা</li><li>জ্বর হলে প্যারাসিটামল ছাড়া অ্যাসপিরিন বা অন্য কোনো ব্যথানাশক ওষুধ কখনোই সেবন না করা</li></ul>'
                ],
                'when_to_see_doctor' => [
                    'en' => '<p>Go to an emergency room immediately if warning signs appear: severe abdominal pain, persistent vomiting, bleeding from gums/nose, breathing difficulty, extreme restlessness, or cold clammy skin.</p>',
                    'bn' => '<p>জ্বর কমে যাওয়ার সময় ডেঙ্গুর আসল বিপদ শুরু হয়। যদি পেটে তীব্র ব্যথা, অনবরত বমি, নাক বা দাঁতের মাড়ি দিয়ে রক্ত পড়া, মলের রঙ কালো হওয়া বা চরম অবসাদ দেখা দেয়—তবে অবিলম্বে হাসপাতালে ভর্তি হতে হবে।</p>'
                ],
                'diagnosis_treatment' => [
                    'en' => '<p>Diagnosed via NS1 antigen test (within days 1-3) and Complete Blood Count (CBC) to monitor hematocrit and platelet counts. Treatment focuses on adequate oral hydration (ORS, coconut water, soup), paracetamol for fever, and IV fluid management if hospitalized.</p>',
                    'bn' => '<p>জ্বরের প্রথম ১-৩ দিনের মধ্যে ডেঙ্গু এনএস১ (NS1) অ্যান্টিজেন টেস্ট এবং নিয়মিত রক্তের সিবিসি (CBC) টেস্টের মাধ্যমে প্লাটিলেট ও হেমাটocrit পর্যবেক্ষণ করা হয়। চিকিৎসায় প্রচুর পরিমাণে তরল খাবার (খাবার স্যালাইন, ডাবের পানি, স্যুপ) এবং বিশ্রামই মূল ভূমিকা রাখে।</p>'
                ]
            ]
        );

        if (isset($symptoms['high-fever'])) {
            $dengue->symptoms()->syncWithoutDetaching([
                $symptoms['high-fever']->id => ['is_primary' => true, 'match_weight' => 10],
            ]);
        }
        if (isset($symptoms['headache'])) {
            $dengue->symptoms()->syncWithoutDetaching([
                $symptoms['headache']->id => ['is_primary' => true, 'match_weight' => 8],
            ]);
        }
        if (isset($symptoms['nausea-vomiting'])) {
            $dengue->symptoms()->syncWithoutDetaching([
                $symptoms['nausea-vomiting']->id => ['is_primary' => false, 'match_weight' => 7],
            ]);
        }
        if (isset($symptoms['joint-pain-swelling'])) {
            $dengue->symptoms()->syncWithoutDetaching([
                $symptoms['joint-pain-swelling']->id => ['is_primary' => true, 'match_weight' => 9],
            ]);
        }
        if (isset($symptoms['skin-rash-itching'])) {
            $dengue->symptoms()->syncWithoutDetaching([
                $symptoms['skin-rash-itching']->id => ['is_primary' => false, 'match_weight' => 6],
            ]);
        }

        DiseaseReference::updateOrCreate(
            ['disease_id' => $dengue->id, 'source_name' => 'World Health Organization (WHO)'],
            [
                'source_url' => 'https://www.who.int/news-room/fact-sheets/detail/dengue-and-severe-dengue',
                'notes' => 'Dengue and Severe Dengue Fact Sheet',
                'order_index' => 1
            ]
        );
        DiseaseReference::updateOrCreate(
            ['disease_id' => $dengue->id, 'source_name' => 'Centers for Disease Control and Prevention (CDC)'],
            [
                'source_url' => 'https://www.cdc.gov/dengue/index.html',
                'notes' => 'Clinical Guidelines for Dengue',
                'order_index' => 2
            ]
        );
    }
}
