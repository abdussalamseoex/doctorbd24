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

class MayoClinicComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. Body Parts (১০টি শারীরস্থান অঞ্চল)
        // ─────────────────────────────────────────────────────────────
        $bodyPartsData = [
            ['slug' => 'head-brain', 'name' => ['en' => 'Head & Brain', 'bn' => 'মাথা ও মস্তিষ্ক'], 'icon' => 'brain', 'order_index' => 1],
            ['slug' => 'eyes-vision', 'name' => ['en' => 'Eyes & Vision', 'bn' => 'চোখ ও দৃষ্টি'], 'icon' => 'eye', 'order_index' => 2],
            ['slug' => 'ears-nose-throat', 'name' => ['en' => 'Ears, Nose & Throat', 'bn' => 'কান, নাক ও গলা'], 'icon' => 'ear', 'order_index' => 3],
            ['slug' => 'chest-heart', 'name' => ['en' => 'Chest & Heart', 'bn' => 'বুক ও হৃদপিণ্ড'], 'icon' => 'heart', 'order_index' => 4],
            ['slug' => 'lungs-respiratory', 'name' => ['en' => 'Lungs & Respiratory', 'bn' => 'ফুসফুস ও শ্বাসযন্ত্র'], 'icon' => 'lungs', 'order_index' => 5],
            ['slug' => 'abdomen-digestive', 'name' => ['en' => 'Abdomen & Digestive', 'bn' => 'পেট ও পরিপাকতন্ত্র'], 'icon' => 'stomach', 'order_index' => 6],
            ['slug' => 'pelvic-urinary', 'name' => ['en' => 'Pelvic, Urinary & Reproductive', 'bn' => 'তলপেট, মূত্র ও প্রজননতন্ত্র'], 'icon' => 'activity', 'order_index' => 7],
            ['slug' => 'bones-joints', 'name' => ['en' => 'Bones, Joints & Muscles', 'bn' => 'হাড়, জোড়া ও পেশি'], 'icon' => 'bone', 'order_index' => 8],
            ['slug' => 'skin-hair', 'name' => ['en' => 'Skin & Hair', 'bn' => 'ত্বক ও চুল'], 'icon' => 'sparkles', 'order_index' => 9],
            ['slug' => 'whole-body', 'name' => ['en' => 'Whole Body & General', 'bn' => 'সারা শরীর ও সাধারণ লক্ষণ'], 'icon' => 'user', 'order_index' => 10],
        ];

        $bodyParts = [];
        foreach ($bodyPartsData as $data) {
            $bodyParts[$data['slug']] = BodyPart::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        // Cache specialty IDs
        $specialties = Specialty::pluck('id', 'slug')->toArray();
        $fallbackSpecialty = $specialties['medicine'] ?? ($specialties['general-physician'] ?? null);

        // ─────────────────────────────────────────────────────────────
        // 2. 166 Diseases Covering ALL Letters A to Z
        // ─────────────────────────────────────────────────────────────
        $diseasesList = [
            // Letter A
            [
                'name_en' => 'Asthma', 'name_bn' => 'হাঁপানি বা অ্যাজমা', 'slug' => 'asthma', 'letter' => 'A',
                'specialty' => 'pulmonology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Asthma is a chronic condition in which your airways narrow and swell and may produce extra mucus. This can make breathing difficult and trigger coughing, wheezing, and shortness of breath.</p>',
                'overview_bn' => '<p>অ্যাজমা বা হাঁপানি ফুসফুসের একটি দীর্ঘস্থায়ী রোগ যার ফলে শ্বাসনালী ফুলে যায়, সংকুচিত হয় এবং অতিরিক্ত শ্লেষ্মা তৈরি হয়। এতে শ্বাস নিতে কষ্ট, কাশি এবং বুকে বাঁশির মতো শব্দ হয়।</p>'
            ],
            [
                'name_en' => 'Alzheimer\'s Disease', 'name_bn' => 'আলঝেইমার রোগ (স্মৃতিভ্রংশ)', 'slug' => 'alzheimers-disease', 'letter' => 'A',
                'specialty' => 'neurology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Alzheimer\'s disease is a progressive neurologic disorder that causes brain cells to waste away (degenerate) and die, resulting in memory loss and cognitive decline.</p>',
                'overview_bn' => '<p>আলঝেইমার একটি প্রগতিশীল নিউরোলজিক্যাল ব্যাধি যাতে মস্তিষ্কের কোষ ক্ষতিগ্রস্ত হয়, ফলে স্মৃতিশক্তি লোপ পায় এবং দৈনন্দিন কার্যক্ষমতা নষ্ট হয়।</p>'
            ],
            [
                'name_en' => 'Anemia', 'name_bn' => 'রক্তস্বল্পতা বা অ্যানিমিয়া', 'slug' => 'anemia', 'letter' => 'A',
                'specialty' => 'hematology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Anemia is a problem of not having enough healthy red blood cells or hemoglobin to carry adequate oxygen to your body\'s tissues, leading to fatigue and weakness.</p>',
                'overview_bn' => '<p>শরীরে পর্যাপ্ত স্বাস্থ্যকর লোহিত রক্তকণিকা বা হিমোগ্লোবিনের অভাবজনিত অবস্থাকে রক্তস্বল্পতা বা অ্যানিমিয়া বলা হয়, যার ফলে দুর্বলতা ও ক্লান্তি দেখা দেয়।</p>'
            ],
            [
                'name_en' => 'Appendicitis', 'name_bn' => 'অ্যাপেন্ডিসাইটিস', 'slug' => 'appendicitis', 'letter' => 'A',
                'specialty' => 'surgery', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Appendicitis is an inflammation of the appendix, a finger-shaped pouch that projects from your colon on the lower right side of your abdomen. It requires urgent surgical care.</p>',
                'overview_bn' => '<p>অ্যাপেন্ডিসাইটিস হলো অ্যাপেন্ডিক্সের তীব্র প্রদাহ যা তলপেটের ডানদিকে তীব্র ব্যথা সৃষ্টি করে এবং জরুরি অস্ত্রোপচারের প্রয়োজন হয়।</p>'
            ],
            [
                'name_en' => 'Arthritis', 'name_bn' => 'আর্থ্রাইটিস বা বাত রোগ', 'slug' => 'arthritis', 'letter' => 'A',
                'specialty' => 'rheumatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Arthritis is the swelling and tenderness of one or more joints. The main symptoms are joint pain and stiffness, which typically worsen with age.</p>',
                'overview_bn' => '<p>আর্থ্রাইটিস হলো এক বা একাধিক অস্থিসন্ধির প্রদাহ ও ফোলাভাব, যার প্রধান লক্ষণ হলো জোড়ায় ব্যথা এবং নড়াচড়ায় আড়ষ্টতা।</p>'
            ],
            [
                'name_en' => 'Acid Reflux (GERD)', 'name_bn' => 'এসিড রিফ্লাক্স বা গ্যাস্ট্রিক', 'slug' => 'acid-reflux-gerd', 'letter' => 'A',
                'specialty' => 'gastroenterology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Gastroesophageal reflux disease (GERD) occurs when stomach acid repeatedly flows back into the tube connecting your mouth and stomach (esophagus).</p>',
                'overview_bn' => '<p>পাকস্থলীর এসিড খাদ্যনালীতে উঠে আসাকে এসিড রিফ্লাক্স বা জিইআরডি বলা হয়। এর ফলে বুক জ্বালাপোড়া ও টক ঢেঁকুর ওঠে।</p>'
            ],
            [
                'name_en' => 'Allergic Rhinitis', 'name_bn' => 'অ্যালার্জিক রাইনাইটিস (নাকের অ্যালার্জি)', 'slug' => 'allergic-rhinitis', 'letter' => 'A',
                'specialty' => 'ent', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Allergic rhinitis causes cold-like symptoms such as sneezing, itchiness, a runny nose, and nasal congestion caused by allergens like pollen or dust.</p>',
                'overview_bn' => '<p>ধুলোবালি বা অ্যালার্জেনের কারণে ঘনঘন হাঁচি, নাক দিয়ে পানি পড়া এবং নাক বন্ধ হয়ে থাকাকে অ্যালার্জিক রাইনাইটিস বলে।</p>'
            ],
            [
                'name_en' => 'Anxiety Disorder', 'name_bn' => 'অ্যাংজাইটি বা দুশ্চিন্তাজনিত রোগ', 'slug' => 'anxiety-disorder', 'letter' => 'A',
                'specialty' => 'psychiatry', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Anxiety disorders involve intense, excessive, and persistent worry and fear about everyday situations, frequently accompanied by rapid heart rate.</p>',
                'overview_bn' => '<p>দৈনন্দিন বিষয়ে অতিরিক্ত, নিয়ন্ত্রণহীন ও দীর্ঘমেয়াদী দুশ্চিন্তা এবং ভীতি অনুভব করাকে অ্যাংজাইটি ডিসঅর্ডার বলা হয়।</p>'
            ],
            [
                'name_en' => 'Acne Vulgaris', 'name_bn' => 'ব্রণ বা অ্যাকনি', 'slug' => 'acne-vulgaris', 'letter' => 'A',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Acne is a skin condition that occurs when hair follicles plug with oil and dead skin cells, causing whiteheads, blackheads, or pimples.</p>',
                'overview_bn' => '<p>ত্বকের রোমকূপ তেল ও মৃতকোষে বন্ধ হয়ে তৈরি হওয়া ফুসকুড়ি বা প্রদাহকে ব্রণ বলা হয়।</p>'
            ],
            [
                'name_en' => 'Angina Pectoris', 'name_bn' => 'এনজাইনা (হৃদরোগের বুকে ব্যথা)', 'slug' => 'angina-pectoris', 'letter' => 'A',
                'specialty' => 'cardiology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Angina is chest pain or discomfort caused when your heart muscle doesn\'t get enough oxygen-rich blood, signaling coronary artery disease.</p>',
                'overview_bn' => '<p>হৃদযন্ত্রের পেশীতে রক্ত চলাচলে বাধার কারণে বুকে তীব্র চাপ বা ব্যথার অনুভূতিকে এনজাইনা বলা হয়।</p>'
            ],

            // Letter B
            [
                'name_en' => 'Bronchitis', 'name_bn' => 'ব্রঙ্কাইটিস', 'slug' => 'bronchitis', 'letter' => 'B',
                'specialty' => 'pulmonology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Bronchitis is an inflammation of the lining of your bronchial tubes, which carry air to and from your lungs. It causes coughing thickened mucus.</p>',
                'overview_bn' => '<p>ফুসফুসের শ্বাসনালী বা ব্রঙ্কাসের ভেতরের আবরণের প্রদাহকে ব্রঙ্কাইটিস বলা হয়, যার ফলে ঘন কফযুক্ত কাশি হয়।</p>'
            ],
            [
                'name_en' => 'Bell\'s Palsy', 'name_bn' => 'বেলস পালসি (মুখের প্যারালাইসিস)', 'slug' => 'bells-palsy', 'letter' => 'B',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Bell\'s palsy causes sudden, temporary weakness in your facial muscles, making half of your face appear to droop.</p>',
                'overview_bn' => '<p>মুখমণ্ডলের একপাশের পেশী হঠাৎ দুর্বল বা অবশ হয়ে মুখ বাঁকা হয়ে যাওয়াকে বেলস পালসি বলে।</p>'
            ],
            [
                'name_en' => 'Bipolar Disorder', 'name_bn' => 'বাইপোলার ডিসঅর্ডার', 'slug' => 'bipolar-disorder', 'letter' => 'B',
                'specialty' => 'psychiatry', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Bipolar disorder causes extreme mood swings that include emotional highs (mania or hypomania) and lows (depression).</p>',
                'overview_bn' => '<p>মেজাজের চরম ওঠানামা—কখনো অতিমাত্রায় আনন্দ ও উত্তেজনা, আবার কখনো গভীর বিষণ্নতা দেখা দেওয়াই বাইপোলার ডিসঅর্ডার।</p>'
            ],
            [
                'name_en' => 'Breast Cancer', 'name_bn' => 'স্তন ক্যান্সার', 'slug' => 'breast-cancer', 'letter' => 'B',
                'specialty' => 'oncology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Breast cancer is cancer that forms in the cells of the breasts, commonly presenting as a painless lump in the breast tissue.</p>',
                'overview_bn' => '<p>স্তনের কোষে অনিয়ন্ত্রিত কোষ বৃদ্ধির মাধ্যমে সৃষ্ট ক্যান্সার, যা সাধারণত ব্যথাহীন পিণ্ড বা চাকা হিসেবে প্রকাশ পায়।</p>'
            ],
            [
                'name_en' => 'Brain Tumor', 'name_bn' => 'ব্রেন টিউমার', 'slug' => 'brain-tumor', 'letter' => 'B',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>A brain tumor is a mass or growth of abnormal cells in your brain that can cause persistent headaches, seizures, or vision problems.</p>',
                'overview_bn' => '<p>মস্তিষ্কের ভেতর অস্বাভাবিক কোষের অস্বাভাবিক বৃদ্ধি বা পিণ্ডকে ব্রেন টিউমার বলে।</p>'
            ],
            [
                'name_en' => 'Benign Prostatic Hyperplasia (BPH)', 'name_bn' => 'প্রোস্টেট বড় হওয়া (বিপিএইচ)', 'slug' => 'benign-prostatic-hyperplasia-bph', 'letter' => 'B',
                'specialty' => 'urology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>BPH is an enlarged prostate gland that can cause uncomfortable urinary symptoms, such as blocking the flow of urine out of the bladder.</p>',
                'overview_bn' => '<p>বয়স বাড়ার সাথে সাথে পুরুষের প্রোস্টেট গ্রন্থির অস্বাভাবিক বৃদ্ধি, যার কারণে ঘনঘন বা আটকে আটকে প্রস্রাব হয়।</p>'
            ],
            [
                'name_en' => 'Back Pain (Chronic)', 'name_bn' => 'কোমরের দীর্ঘমেয়াদী ব্যথা', 'slug' => 'chronic-back-pain', 'letter' => 'B',
                'specialty' => 'orthopedics', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Chronic back pain is pain in the lower or upper back persisting for more than 12 weeks, often originating from muscles, nerves, or spinal discs.</p>',
                'overview_bn' => '<p>মেরুদণ্ড, মাংসপেশি বা স্নায়ুর সমস্যার কারণে ১২ সপ্তাহের বেশি সময় ধরে কোমরে স্থায়ী ব্যথা অনুভব করা।</p>'
            ],

            // Letter C
            [
                'name_en' => 'COVID-19', 'name_bn' => 'কোভিড-১৯ (করোনা সংক্রমণ)', 'slug' => 'covid-19', 'letter' => 'C',
                'specialty' => 'medicine', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>COVID-19 is an infectious disease caused by the SARS-CoV-2 virus, leading to fever, cough, loss of smell, and respiratory symptoms.</p>',
                'overview_bn' => '<p>করোনাভাইরাস দ্বারা সৃষ্ট ছোঁয়াচে সংক্রমণ যার প্রধান লক্ষণ জ্বর, শুকনো কাশি, ক্লান্তি এবং ঘ্রাণশক্তি লোপ পাওয়া।</p>'
            ],
            [
                'name_en' => 'Cataract', 'name_bn' => 'চোখের ছানি পড়া', 'slug' => 'cataract', 'letter' => 'C',
                'specialty' => 'ophthalmology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>A cataract is a clouding of the normally clear lens of your eye, leading to blurred vision and difficulty seeing clearly at night.</p>',
                'overview_bn' => '<p>চোখের স্বাভাবিক স্বচ্ছ লেন্স ঘোলাটে হয়ে দৃষ্টি ঝাপসা হয়ে যাওয়াকে চোখের ছানি পড়া বলে।</p>'
            ],
            [
                'name_en' => 'Cervical Spondylosis', 'name_bn' => 'সার্ভাইকাল স্পন্ডিলাইসিস (ঘাড়ের ব্যথা)', 'slug' => 'cervical-spondylosis', 'letter' => 'C',
                'specialty' => 'orthopedics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Cervical spondylosis is age-related wear and tear of the spinal discs in your neck, causing neck stiffness and radiating arm pain.</p>',
                'overview_bn' => '<p>ঘাড়ের মেরুদণ্ডের হাড় ও ডিস্কের ক্ষয়রোগ, যাতে ঘাড় শক্ত হয়ে যায় এবং কাঁধ ও হাতে ব্যথা ছড়ায়।</p>'
            ],
            [
                'name_en' => 'Chronic Kidney Disease (CKD)', 'name_bn' => 'দীর্ঘস্থায়ী কিডনি রোগ (সিকেডি)', 'slug' => 'chronic-kidney-disease-ckd', 'letter' => 'C',
                'specialty' => 'nephrology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>CKD involves gradual loss of kidney function over time, often caused by uncontrolled diabetes or long-standing high blood pressure.</p>',
                'overview_bn' => '<p>ধীরে ধীরে কিডনির রক্ত পরিশোধনের স্বাভাবিক ক্ষমতা নষ্ট হয়ে যাওয়াকে দীর্ঘস্থায়ী কিডনি রোগ বলে।</p>'
            ],
            [
                'name_en' => 'Cirrhosis of Liver', 'name_bn' => 'লিভার সিরোসিস', 'slug' => 'cirrhosis-of-liver', 'letter' => 'C',
                'specialty' => 'hepatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Cirrhosis is severe, irreversible scarring of the liver caused by long-term liver damage from viral hepatitis or other conditions.</p>',
                'overview_bn' => '<p>লিভারের কোষে দীর্ঘস্থায়ী ক্ষতি ও ক্ষতের কারণে লিভারের স্বাভাবিক কার্যক্ষমতা নষ্ট হয়ে যাওয়া।</p>'
            ],
            [
                'name_en' => 'Conjunctivitis (Pink Eye)', 'name_bn' => 'কনজাংটিভাইটিস (চোখ ওঠা)', 'slug' => 'conjunctivitis-pink-eye', 'letter' => 'C',
                'specialty' => 'ophthalmology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Conjunctivitis is inflammation or infection of the transparent membrane lining your eyelid and eyeball, causing redness and discharge.</p>',
                'overview_bn' => '<p>চোখের সাদা অংশের আবরণের সংক্রমণ বা প্রদাহ, যার কারণে চোখ লাল হওয়া, খচখচ করা ও পিঁচুটি পড়ে।</p>'
            ],
            [
                'name_en' => 'Constipation', 'name_bn' => 'কোষ্ঠকাঠিন্য', 'slug' => 'constipation', 'letter' => 'C',
                'specialty' => 'gastroenterology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Constipation means infrequent bowel movements or difficult passage of stools that persists for several weeks or longer.</p>',
                'overview_bn' => '<p>নিয়মিত মলত্যাগ না হওয়া বা মল অত্যন্ত শক্ত হয়ে কষ্টদায়কভাবে বের হওয়াকে কোষ্ঠকাঠিন্য বলে।</p>'
            ],
            [
                'name_en' => 'Chickenpox', 'name_bn' => 'জলবসন্ত বা চিকেনপক্স', 'slug' => 'chickenpox', 'letter' => 'C',
                'specialty' => 'pediatrics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Chickenpox is a contagious viral infection causing an itchy, blister-like rash on the skin along with mild fever.</p>',
                'overview_bn' => '<p>ভেরিসেলা ভাইরাসের কারণে সৃষ্ট অত্যন্ত ছোঁয়াচে রোগ যাতে শরীরে চুলকানিযুক্ত পানিপূর্ণ ছোট ছোট ফোস্কা ওঠে।</p>'
            ],
            [
                'name_en' => 'Cholera', 'name_bn' => 'কলেরা', 'slug' => 'cholera', 'letter' => 'C',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Cholera is an acute bacterial diarrheal illness caused by Vibrio cholerae, causing severe watery diarrhea and life-threatening dehydration.</p>',
                'overview_bn' => '<p>ভিব্রিও কলেরি ব্যাকটেরিয়ার সংক্রমণে তীব্র চালধোয়া পানির মতো পাতলা পায়খানা ও বমিজনিত মারাত্মক পানিশূন্যতার রোগ।</p>'
            ],

            // Letter D
            [
                'name_en' => 'Dengue Fever', 'name_bn' => 'ডেঙ্গু জ্বর', 'slug' => 'dengue-fever', 'letter' => 'D',
                'specialty' => 'medicine', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Dengue fever is a mosquito-borne tropical disease caused by the dengue virus, causing high fever, intense headache, eye pain, and joint aches.</p>',
                'overview_bn' => '<p>এডিস মশার কামড়ে ছড়ানো ভাইরাসজনিত জ্বর যাতে তীব্র মাথাব্যথা, চোখের পেছনে যন্ত্রণা এবং অস্থিসন্ধিতে প্রচণ্ড ব্যথা হয়।</p>'
            ],
            [
                'name_en' => 'Diabetes Mellitus (Type 2)', 'name_bn' => 'টাইপ ২ ডায়াবেটিস', 'slug' => 'type-2-diabetes', 'letter' => 'D',
                'specialty' => 'diabetes', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Type 2 diabetes is a chronic condition that affects the way your body regulates and uses sugar (glucose) as a fuel, leading to elevated blood sugar.</p>',
                'overview_bn' => '<p>ইনসুলিনের অকার্যকারিতা বা ঘাটতির কারণে রক্তে গ্লুকোজের মাত্রা বেড়ে যাওয়ার দীর্ঘমেয়াদী মেটাবলিক রোগ।</p>'
            ],
            [
                'name_en' => 'Depression (Major Depressive Disorder)', 'name_bn' => 'বিষণ্নতা বা ডিপ্রেশন', 'slug' => 'depression', 'letter' => 'D',
                'specialty' => 'psychiatry', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Depression is a mood disorder that causes a persistent feeling of sadness and loss of interest in activities you once enjoyed.</p>',
                'overview_bn' => '<p>দীর্ঘস্থায়ী মনমরা ভাব, কোনো কাজে আনন্দ না পাওয়া ও সার্বিক বিষাদময় মানসিক অবস্থাকে ডিপ্রেশন বলা হয়।</p>'
            ],
            [
                'name_en' => 'Diarrhea', 'name_bn' => 'ডায়রিয়া বা পাতলা পায়খানা', 'slug' => 'diarrhea', 'letter' => 'D',
                'specialty' => 'gastroenterology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Diarrhea describes loose, watery stools that occur more frequently than usual, posing a risk of rapid dehydration.</p>',
                'overview_bn' => '<p>দিনে তিন বা ততোধিক বার তরল বা পাতলা পায়খানা হওয়া, যার প্রধান ঝুঁকি মারাত্মক পানিশূন্যতা।</p>'
            ],
            [
                'name_en' => 'Deep Vein Thrombosis (DVT)', 'name_bn' => 'ডিপ ভেইন থ্রম্বোসিস (ডিভিটি)', 'slug' => 'deep-vein-thrombosis-dvt', 'letter' => 'D',
                'specialty' => 'cardiology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>DVT occurs when a blood clot forms in one or more of the deep veins in your body, usually in the legs, which can travel to the lungs.</p>',
                'overview_bn' => '<p>পায়ের গভীর রক্তনালীতে রক্ত জমাট বাঁধা, যা মারাত্মক ফুসফুসের ব্লকেজ (পালমোনারি এমবোলিজম) ঘটাতে পারে।</p>'
            ],
            [
                'name_en' => 'Dementia', 'name_bn' => 'ডিমেনশিয়া (স্মৃতিহীনতা)', 'slug' => 'dementia', 'letter' => 'D',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Dementia is a term for several conditions characterized by impairment of at least two brain functions, such as memory loss and judgment.</p>',
                'overview_bn' => '<p>স্মৃতিশক্তি, চিন্তাভাবনা ও সঠিক সিদ্ধান্ত নেওয়ার ক্ষমতা লোপ পাওয়া সংক্রান্ত মানসিক অবনতি।</p>'
            ],
            [
                'name_en' => 'Dysentery', 'name_bn' => 'আমাশয়', 'slug' => 'dysentery', 'letter' => 'D',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Dysentery is an intestinal infection that causes severe diarrhea containing mucus or blood, often with severe stomach cramps.</p>',
                'overview_bn' => '<p>অন্ত্রের ব্যাকটেরিয়া বা এমিবার সংক্রমণে আম ও রক্তযুক্ত ঘনঘন পাতলা পায়খানা ও পেটে মোচড় দেওয়া।</p>'
            ],

            // Letter E
            [
                'name_en' => 'Epilepsy', 'name_bn' => 'মৃগীরোগ বা এপিলেপসি', 'slug' => 'epilepsy', 'letter' => 'E',
                'specialty' => 'neurology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Epilepsy is a central nervous system (neurological) disorder in which brain activity becomes abnormal, causing seizures or periods of unusual behavior.</p>',
                'overview_bn' => '<p>মস্তিষ্কের স্নায়বিক কার্যক্রমে অস্বাভাবিকতার কারণে শরীরে নিয়মিত খিঁচুনি বা সাময়িক অচেতনতা দেখা দেওয়ার রোগ।</p>'
            ],
            [
                'name_en' => 'Eczema', 'name_bn' => 'একজিমা', 'slug' => 'eczema', 'letter' => 'E',
                'specialty' => 'dermatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Eczema is a condition that makes your skin red and itchy. It is common in children but can occur at any age and is long-lasting.</p>',
                'overview_bn' => '<p>ত্বকের প্রদাহজনিত রোগ যাতে ত্বক শুষ্ক, খসখসে, লালচে ও তীব্র চুলকানিযুক্ত হয়ে যায়।</p>'
            ],
            [
                'name_en' => 'Endometriosis', 'name_bn' => 'এন্ডোমেট্রিওসিস', 'slug' => 'endometriosis', 'letter' => 'E',
                'specialty' => 'gynecology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Endometriosis is an often painful disorder in which tissue similar to the tissue that normally lines the inside of your uterus grows outside your uterus.</p>',
                'overview_bn' => '<p>জরায়ুর ভেতরের আবরণীর মতো টিস্যু জরায়ুর বাইরে বৃদ্ধি পেয়ে তলপেটে তীব্র মাসিককালীন ব্যথা ও বন্ধ্যাত্ব ঘটায়।</p>'
            ],
            [
                'name_en' => 'Ear Infection (Otitis Media)', 'name_bn' => 'কানের ইনফেকশন (ওটাইটিস মিডিয়া)', 'slug' => 'ear-infection-otitis-media', 'letter' => 'E',
                'specialty' => 'ent', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>An ear infection is an infection of the middle ear, the air-filled space behind the eardrum that contains the tiny vibrating bones of the ear.</p>',
                'overview_bn' => '<p>কানের পর্দার পেছনের মধ্যকর্ণে ব্যাকটেরিয়া বা ভাইরাসজনিত প্রদাহ যাতে তীব্র কানব্যথা ও পুঁজ পড়ে।</p>'
            ],
            [
                'name_en' => 'Encephalitis', 'name_bn' => 'এনসেফালাইটিস (মস্তিষ্কের তীব্র প্রদাহ)', 'slug' => 'encephalitis', 'letter' => 'E',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Encephalitis is inflammation of the active brain tissue itself, usually resulting from an infection and causing high fever, confusion, and seizures.</p>',
                'overview_bn' => '<p>ভাইরাস সংক্রমণের কারণে মস্তিষ্কের প্যারেনকাইমার মারাত্মক প্রদাহ যাতে তীব্র জ্বর ও জ্ঞান হারানোর ঝুঁকি থাকে।</p>'
            ],

            // Letter F
            [
                'name_en' => 'Fatty Liver Disease', 'name_bn' => 'ফ্যাটি লিভার রোগ', 'slug' => 'fatty-liver-disease', 'letter' => 'F',
                'specialty' => 'hepatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Fatty liver disease is a condition caused by the storage of extra fat in liver cells, commonly associated with obesity, diabetes, and metabolic syndrome.</p>',
                'overview_bn' => '<p>লিভারের কোষে অতিরিক্ত চর্বি জমাজনিত অবস্থা, যা ডায়াবেটিস ও অতিরিক্ত ওজনের রোগীদের বেশি দেখা যায়।</p>'
            ],
            [
                'name_en' => 'Fibromyalgia', 'name_bn' => 'ফাইব্রোমায়ালজিয়া', 'slug' => 'fibromyalgia', 'letter' => 'F',
                'specialty' => 'rheumatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Fibromyalgia is a disorder characterized by widespread musculoskeletal pain accompanied by fatigue, sleep, memory, and mood issues.</p>',
                'overview_bn' => '<p>সারা শরীরে মাংসপেশি ও অস্থিসন্ধিতে দীর্ঘস্থায়ী ব্যথা, তীব্র ক্লান্তি এবং ঘুমের সমস্যা সৃষ্টিকারী ব্যাধি।</p>'
            ],
            [
                'name_en' => 'Food Poisoning', 'name_bn' => 'খাদ্যে বিষক্রিয়া বা ফুড পয়জনিং', 'slug' => 'food-poisoning', 'letter' => 'F',
                'specialty' => 'medicine', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Food poisoning is an illness caused by eating contaminated food, causing vomiting, watery diarrhea, and fever within hours.</p>',
                'overview_bn' => '<p>জীবাণুযুক্ত বা পচা-বাসি খাবার খাওয়ার পর পেটে তীব্র মোচড়, ঘনঘন বমি ও পাতলা পায়খানা হওয়া।</p>'
            ],
            [
                'name_en' => 'Flu (Influenza)', 'name_bn' => 'ইনফ্লুয়েঞ্জা বা সাধারণ ফ্লু', 'slug' => 'influenza-flu', 'letter' => 'F',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Influenza is a common viral infection that can be deadly, especially in high-risk groups, attacking your lungs, nose, and throat.</p>',
                'overview_bn' => '<p>ইনফ্লুয়েঞ্জা ভাইরাস দ্বারা শ্বাসতন্ত্রের আক্রমণ যাতে হঠাৎ তীব্র জ্বর, শরীর ব্যথা ও দুর্বলতা দেখা দেয়।</p>'
            ],
            [
                'name_en' => 'Fungal Skin Infection', 'name_bn' => 'ছত্রাক সংক্রমণ বা দাদ', 'slug' => 'fungal-skin-infection', 'letter' => 'F',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Fungal skin infections commonly cause circular, red, itchy rashes (ringworm, athlete\'s foot, or jock itch) in warm, moist body folds.</p>',
                'overview_bn' => '<p>ত্বকের বিভিন্ন খাঁজে ও স্যাঁতসেঁতে অংশে ছত্রাকের আক্রমণ যাতে গোল চাকার মতো লালচে চুলকানিযুক্ত দাগ হয়।</p>'
            ],
            [
                'name_en' => 'Frozen Shoulder', 'name_bn' => 'ফ্রোজেন শোল্ডার (কাঁধ জ্যাম হওয়া)', 'slug' => 'frozen-shoulder', 'letter' => 'F',
                'specialty' => 'orthopedics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Frozen shoulder, also called adhesive capsulitis, involves stiffness and pain in your shoulder joint that gradually worsens over months.</p>',
                'overview_bn' => '<p>কাঁধের জয়েন্টে তীব্র ব্যথা ও নড়াচড়ার ক্ষমতা প্রায় সম্পূর্ণ নষ্ট হয়ে যাওয়ার মতো আড়ষ্টতা।</p>'
            ],

            // Letter G
            [
                'name_en' => 'Gastritis', 'name_bn' => 'গ্যাস্ট্রাইটিস বা গ্যাস্ট্রিকের সমস্যা', 'slug' => 'gastritis', 'letter' => 'G',
                'specialty' => 'gastroenterology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Gastritis is an inflammation, irritation, or erosion of the lining of the stomach, causing upper abdominal pain, nausea, and indigestion.</p>',
                'overview_bn' => '<p>পাকস্থলীর অভ্যন্তরীণ স্তরের প্রদাহ বা ঘা, যার ফলে পেটের উপরের অংশে ব্যথা, জ্বালাপোড়া ও বমি ভাব হয়।</p>'
            ],
            [
                'name_en' => 'Gallstones (Cholelithiasis)', 'name_bn' => 'পিত্তথলির পাথর (গলস্টোন)', 'slug' => 'gallstones-cholelithiasis', 'letter' => 'G',
                'specialty' => 'surgery', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Gallstones are hardened deposits of digestive fluid that can form in your gallbladder, causing severe upper right abdominal pain.</p>',
                'overview_bn' => '<p>পিত্তরসে কোলেস্টেরল ও বিলিরুবিন জমে পিত্তথলিতে পাথর তৈরি হওয়া, যা পেটের ডানদিকে তীব্র ব্যথা ঘটায়।</p>'
            ],
            [
                'name_en' => 'Glaucoma', 'name_bn' => 'গ্লুকোমা (চোখের উচ্চ চাপ)', 'slug' => 'glaucoma', 'letter' => 'G',
                'specialty' => 'ophthalmology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Glaucoma is a group of eye conditions that damage the optic nerve, often caused by abnormally high pressure in your eye, leading to blindness.</p>',
                'overview_bn' => '<p>চোখের অভ্যন্তরীণ চাপ বৃদ্ধির ফলে দৃষ্টিস্নায়ুর (অপটিক নার্ভ) ক্ষতি হয়ে স্থায়ী অন্ধত্ব ডেকে আনার রোগ।</p>'
            ],
            [
                'name_en' => 'Gout', 'name_bn' => 'গাউট বা গেঁটে বাত', 'slug' => 'gout', 'letter' => 'G',
                'specialty' => 'rheumatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Gout is a common and complex form of arthritis characterized by sudden, severe attacks of pain, swelling, and redness in the big toe joint.</p>',
                'overview_bn' => '<p>রক্তে অতিরিক্ত ইউরিক এসিড জমা হয়ে পায়ের বুড়ো আঙুল ও বিভিন্ন জোড়ায় তীব্র অসহ্য ব্যথা ও ফোলাভাব।</p>'
            ],
            [
                'name_en' => 'Gastroenteritis', 'name_bn' => 'গ্যাস্ট্রোএন্টারাইটিস (পেটের ইনফেকশন)', 'slug' => 'gastroenteritis', 'letter' => 'G',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Viral gastroenteritis is an intestinal infection marked by watery diarrhea, abdominal cramps, nausea or vomiting, and sometimes fever.</p>',
                'overview_bn' => '<p>পাকস্থলী ও অন্ত্রের তীব্র ভাইরাস বা ব্যাকটেরিয়া সংক্রমণ যাতে পেটে ব্যথা, বমি ও পাতলা পায়খানা হয়।</p>'
            ],

            // Letter H
            [
                'name_en' => 'Hypertension (High Blood Pressure)', 'name_bn' => 'উচ্চ রক্তচাপ বা হাইপারটেনশন', 'slug' => 'hypertension', 'letter' => 'H',
                'specialty' => 'cardiology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Hypertension is a common condition in which the long-term force of the blood against your artery walls is high enough that it may eventually cause heart disease.</p>',
                'overview_bn' => '<p>ধমনীর দেয়ালে রক্তের চাপ স্বাভাবিকের চেয়ে দীর্ঘসময় বেশি থাকা, যা হার্ট অ্যাটাক ও স্ট্রোকের ঝুঁকি বাড়ায়।</p>'
            ],
            [
                'name_en' => 'Heart Attack (Myocardial Infarction)', 'name_bn' => 'হার্ট অ্যাটাক', 'slug' => 'heart-attack', 'letter' => 'H',
                'specialty' => 'cardiology', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>A heart attack occurs when the flow of blood to the heart is severely reduced or blocked, requiring emergency hospitalization within minutes.</p>',
                'overview_bn' => '<p>করোনারি ধমনীতে রক্ত চলাচল বন্ধ হয়ে হৃদপেশীর ক্ষতি হওয়া; লক্ষণ: বুকে পাথর চাপা তীব্র ব্যথা, ঘাম ও বাম হাতে ব্যথা ছড়ানো। অবিলম্বে জরুরি হাসপাতালে নেওয়া আবশ্যক।</p>'
            ],
            [
                'name_en' => 'Hepatitis B', 'name_bn' => 'হেপাটাইটিস বি', 'slug' => 'hepatitis-b', 'letter' => 'H',
                'specialty' => 'hepatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Hepatitis B is a serious liver infection caused by the hepatitis B virus (HBV) that can become chronic and lead to liver cirrhosis or cancer.</p>',
                'overview_bn' => '<p>হেপাটাইটিস বি ভাইরাস দ্বারা লিভারের মারাত্মক সংক্রমণ যা দীর্ঘমেয়াদে লিভার সিরোসিস বা ক্যান্সারের কারণ হতে পারে।</p>'
            ],
            [
                'name_en' => 'Hemorrhoids (Piles)', 'name_bn' => 'পাইলস বা অর্শ রোগ', 'slug' => 'hemorrhoids-piles', 'letter' => 'H',
                'specialty' => 'surgery', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Hemorrhoids are swollen veins in your anus and lower rectum, often caused by straining during bowel movements or chronic constipation.</p>',
                'overview_bn' => '<p>মলদ্বার ও পায়ুপথের ভেতরের শিরা ফুলে গিয়ে রক্তপাত, মাংসপিণ্ড বাইরে বের হওয়া ও চুলকানি দেখা দেওয়া।</p>'
            ],
            [
                'name_en' => 'Hypothyroidism', 'name_bn' => 'হাইপোথাইরয়েডিজম (থাইরয়েডের ঘাটতি)', 'slug' => 'hypothyroidism', 'letter' => 'H',
                'specialty' => 'endocrinology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Hypothyroidism is a condition in which your thyroid gland doesn\'t produce enough of certain crucial hormones, causing unexplained weight gain and fatigue.</p>',
                'overview_bn' => '<p>থাইরয়েড গ্রন্থি থেকে পরিমিত হরমোন নিঃসৃত না হওয়ার ফলে শরীর ভারী হওয়া, ওজন বৃদ্ধি ও অতিরিক্ত শীত অনুভব হওয়া।</p>'
            ],
            [
                'name_en' => 'Hernia', 'name_bn' => 'হার্নিয়া', 'slug' => 'hernia', 'letter' => 'H',
                'specialty' => 'surgery', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>A hernia occurs when an internal organ or other body part protrudes through the wall of muscle or tissue that normally contains it, commonly in the groin.</p>',
                'overview_bn' => '<p>পেটের দুর্বল মাংসপেশি ভেদ করে ভেতরের কোনো অংশ (যেমন অন্ত্র) ফুলে বাইরে বের হয়ে আসাকে হার্নিয়া বলে।</p>'
            ],

            // Letter I
            [
                'name_en' => 'Irritable Bowel Syndrome (IBS)', 'name_bn' => 'ইরিটেবল বাওয়েল সিন্ড্রোম (আইবিএস)', 'slug' => 'irritable-bowel-syndrome-ibs', 'letter' => 'I',
                'specialty' => 'gastroenterology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>IBS is a common disorder that affects the large intestine, causing cramping, abdominal pain, bloating, gas, and alternating diarrhea or constipation.</p>',
                'overview_bn' => '<p>বৃহদন্ত্রের সংবেদনশীলতাজনিত দীর্ঘস্থায়ী সমস্যা যাতে পেটে মোচড়, গ্যাস এবং ঘনঘন পাতলা পায়খানা বা কোষ্ঠকাঠিন্য হয়।</p>'
            ],
            [
                'name_en' => 'Insomnia', 'name_bn' => 'অনিদ্রা বা ইনসোমনিয়া', 'slug' => 'insomnia', 'letter' => 'I',
                'specialty' => 'psychiatry', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Insomnia is a common sleep disorder that can make it hard to fall asleep, hard to stay asleep, or cause you to wake up too early and not feel rested.</p>',
                'overview_bn' => '<p>রাতে পর্যাপ্ত ঘুমে ব্যাঘাত ঘটা, সহজে ঘুম না আসা বা বারবার ঘুম ভেঙে যাওয়ার দীর্ঘমেয়াদী সমস্যা।</p>'
            ],
            [
                'name_en' => 'Iron Deficiency Anemia', 'name_bn' => 'আয়রনের ঘাটতিজনিত রক্তস্বল্পতা', 'slug' => 'iron-deficiency-anemia', 'letter' => 'I',
                'specialty' => 'hematology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Iron deficiency anemia is a condition in which blood lacks adequate healthy red blood cells due to insufficient iron in the body.</p>',
                'overview_bn' => '<p>দেহে আয়রনের অভাবে হিমোগ্লোবিন উৎপাদন কমে গিয়ে দুর্বলতা, বুক ধড়ফড় ও ফ্যাকাশে বর্ণ ধারণ করার রোগ।</p>'
            ],
            [
                'name_en' => 'Infertility', 'name_bn' => 'বন্ধ্যাত্ব', 'slug' => 'infertility', 'letter' => 'I',
                'specialty' => 'gynecology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Infertility is defined as not being able to get pregnant despite having regular, unprotected sex for at least one year.</p>',
                'overview_bn' => '<p>নিয়মিত চেষ্টা করার পরও এক বছর বা ততোধিক সময়ে সন্তান ধারণে ব্যর্থতাকে চিকিৎসাবিজ্ঞানে বন্ধ্যাত্ব বলা হয়।</p>'
            ],

            // Letter J
            [
                'name_en' => 'Jaundice', 'name_bn' => 'জন্ডিস', 'slug' => 'jaundice', 'letter' => 'J',
                'specialty' => 'hepatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Jaundice is a yellow discoloration of the skin, mucous membranes, and the whites of eyes caused by increased levels of bilirubin in the blood.</p>',
                'overview_bn' => '<p>রক্তে বিলিরুবিনের মাত্রা বেড়ে যাওয়ার ফলে চোখ, প্রস্রাব ও সারা শরীর হলুদ বর্ণ ধারণ করা।</p>'
            ],
            [
                'name_en' => 'Juvenile Idiopathic Arthritis', 'name_bn' => 'শিশুদের বাত রোগ (জুভেনাইল আর্থ্রাইটিস)', 'slug' => 'juvenile-idiopathic-arthritis', 'letter' => 'J',
                'specialty' => 'pediatrics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Juvenile idiopathic arthritis is the most common type of arthritis in kids under 16, causing persistent joint pain, swelling, and stiffness.</p>',
                'overview_bn' => '<p>১৬ বছরের নিচের শিশুদের অস্থিসন্ধিতে দীর্ঘস্থায়ী ফোলাভাব, ব্যথা ও সকালে হাঁটাচলায় আড়ষ্টতা সৃষ্টিকারী বাত রোগ।</p>'
            ],

            // Letter K
            [
                'name_en' => 'Kidney Stones (Nephrolithiasis)', 'name_bn' => 'কিডনিতে পাথর', 'slug' => 'kidney-stones', 'letter' => 'K',
                'specialty' => 'urology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Kidney stones are hard deposits made of minerals and salts that form inside your kidneys, causing excruciating side and back pain.</p>',
                'overview_bn' => '<p>কিডনির ভেতর খনিজ ও লবণের জমাট বাঁধা শক্ত পাথর, যা পিঠের একপাশে ও কুচকিতে তীব্র অসহ্য যন্ত্রণা ঘটায়।</p>'
            ],
            [
                'name_en' => 'Kidney Failure (Acute & Chronic)', 'name_bn' => 'কিডনি ফেইলিউর বা কিডনি বিকল', 'slug' => 'kidney-failure', 'letter' => 'K',
                'specialty' => 'nephrology', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Kidney failure occurs when your kidneys lose the ability to sufficiently filter waste from your blood, requiring dialysis or transplantation.</p>',
                'overview_bn' => '<p>কিডনির রক্ত থেকে ক্ষতিকর বর্জ্য ও অতিরিক্ত পানি অপসারণের ক্ষমতা সম্পূর্ণ লোপ পাওয়া।</p>'
            ],
            [
                'name_en' => 'Keratitis', 'name_bn' => 'কেরাটাইটিস (কর্নিয়ার প্রদাহ)', 'slug' => 'keratitis', 'letter' => 'K',
                'specialty' => 'ophthalmology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Keratitis is an inflammation of the cornea, the clear tissue on the front of your eye, causing severe eye pain and blurred vision.</p>',
                'overview_bn' => '<p>চোখের সম্মুখভাগের স্বচ্ছ পর্দা বা কর্নিয়ার ইনফেকশন যাতে তীব্র চোখব্যথা, আলোতে চোখ বন্ধ হয়ে যাওয়া ও ঝাপসা দেখা দেয়।</p>'
            ],

            // Letter L
            [
                'name_en' => 'Liver Cirrhosis', 'name_bn' => 'লিভার সিরোসিস', 'slug' => 'liver-cirrhosis', 'letter' => 'L',
                'specialty' => 'hepatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Liver cirrhosis is late-stage fibrosis of the liver caused by many forms of liver diseases, characterized by fluid retention (ascites) and jaundice.</p>',
                'overview_bn' => '<p>দীর্ঘস্থায়ী লিভার ক্ষতের কারণে লিভার শক্ত হয়ে পেটে পানি আসা (এসাইটিস) এবং জন্ডিস দেখা দেওয়ার মারাত্মক অবস্থা।</p>'
            ],
            [
                'name_en' => 'Leukemia (Blood Cancer)', 'name_bn' => 'লিউকেমিয়া (ব্লাড ক্যান্সার)', 'slug' => 'leukemia', 'letter' => 'L',
                'specialty' => 'oncology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Leukemia is cancer of the body\'s blood-forming tissues, including the bone marrow and the lymphatic system, causing rapid fatigue and easy bruising.</p>',
                'overview_bn' => '<p>রক্ত ও অস্থিমজ্জার ক্যান্সার যাতে অস্বাভাবিক শ্বেত রক্তকণিকা অনিয়ন্ত্রিতভাবে বেড়ে গিয়ে স্বাভাবিক রক্তকণিকা উৎপাদন ব্যাহত করে।</p>'
            ],
            [
                'name_en' => 'Lung Cancer', 'name_bn' => 'ফুসফুসের ক্যান্সার', 'slug' => 'lung-cancer', 'letter' => 'L',
                'specialty' => 'pulmonology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Lung cancer is a type of cancer that begins in the lungs, commonly signaled by persistent cough, coughing up blood, and weight loss.</p>',
                'overview_bn' => '<p>ফুসফুসের কোষে টিউমার তৈরি হওয়া, যার অন্যতম প্রধান কারণ ধূমপান; লক্ষণ: কাশির সাথে রক্ত ও দ্রুত ওজন হ্রাস।</p>'
            ],
            [
                'name_en' => 'Low Back Pain', 'name_bn' => 'কোমর ব্যথা বা লাম্বাগো', 'slug' => 'low-back-pain', 'letter' => 'L',
                'specialty' => 'orthopedics', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Low back pain is extremely common and may result from a muscle injury or a disc problem, impacting normal mobility.</p>',
                'overview_bn' => '<p>ভারী ওজন তোলা, দীর্ঘক্ষণ বসে থাকা বা মেরুদণ্ডের ক্ষয়ের কারণে কোমরে তীব্র টান ও ব্যথা হওয়া।</p>'
            ],

            // Letter M
            [
                'name_en' => 'Migraine', 'name_bn' => 'মাইগ্রেন', 'slug' => 'migraine', 'letter' => 'M',
                'specialty' => 'neurology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>A migraine is a headache that can cause severe throbbing pain or a pulsing sensation, usually on one side of the head, often accompanied by nausea.</p>',
                'overview_bn' => '<p>মাথার একপাশে তীব্র দপদপ করা মাথাব্যথা যাতে বমি বমি ভাব এবং আলো ও শব্দের প্রতি চরম সংবেদনশীলতা থাকে।</p>'
            ],
            [
                'name_en' => 'Malaria', 'name_bn' => 'ম্যালেরিয়া জ্বর', 'slug' => 'malaria', 'letter' => 'M',
                'specialty' => 'medicine', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Malaria is a disease caused by a plasmodium parasite, transmitted by the bite of infected Anopheles mosquitoes, causing recurring high fevers with severe chills.</p>',
                'overview_bn' => '<p>অ্যানোফিলিস মশার কামড়ে ছড়ানো পরজীবী সংক্রমণ যাতে কাঁপুনি দিয়ে নির্দিষ্ট সময় পর পর তীব্র জ্বর আসে এবং ঘাম দিয়ে ছাড়ে।</p>'
            ],
            [
                'name_en' => 'Meningitis', 'name_bn' => 'মেনিনজাইটিস', 'slug' => 'meningitis', 'letter' => 'M',
                'specialty' => 'neurology', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Meningitis is an infection and inflammation of the fluid and membranes surrounding your brain and spinal cord, presenting as high fever and stiff neck.</p>',
                'overview_bn' => '<p>মস্তিষ্ক ও সুষুম্নাকাণ্ডের ঝিল্লির মারাত্মক ইনফেকশন যার লক্ষণ তীব্র জ্বর, অসহ্য মাথাব্যথা ও ঘাড় শক্ত হয়ে যাওয়া। এটি একটি জরুরি চিকিৎসা অবস্থা।</p>'
            ],
            [
                'name_en' => 'Measles', 'name_bn' => 'হাম বা মিসেলস', 'slug' => 'measles', 'letter' => 'M',
                'specialty' => 'pediatrics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Measles is a childhood infection caused by a virus, once quite common, characterized by a rash of large, flat blotches that often flow into one another.</p>',
                'overview_bn' => '<p>ভাইরাসজনিত ছোঁয়াচে রোগ যাতে শিশুদের চোখ লাল হওয়া, সর্দি, কাশির সাথে সারা শরীরে লালচে দাগ বা র্যাশ ছড়িয়ে পড়ে।</p>'
            ],

            // Letter N
            [
                'name_en' => 'Nephrotic Syndrome', 'name_bn' => 'নেফ্রোটিক সিন্ড্রোম', 'slug' => 'nephrotic-syndrome', 'letter' => 'N',
                'specialty' => 'nephrology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Nephrotic syndrome is a kidney disorder that causes your body to pass too much protein in your urine, resulting in facial puffiness and swelling in feet.</p>',
                'overview_bn' => '<p>কিডনির ছাঁকনি ক্ষতিগ্রস্ত হয়ে প্রস্রাবের সাথে অতিরিক্ত প্রোটিন নির্গত হওয়া, ফলে চোখ-মুখ ও পা ফুলে যায়।</p>'
            ],
            [
                'name_en' => 'Neuropathy (Peripheral)', 'name_bn' => 'পেরিফেরাল নিউরোপ্যাথি (স্নায়ুর দুর্বলতা)', 'slug' => 'peripheral-neuropathy', 'letter' => 'N',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Peripheral neuropathy results when the nerves outside brain and spinal cord are damaged, causing weakness, numbness, and burning pain in feet.</p>',
                'overview_bn' => '<p>হাত ও পায়ের প্রান্তীয় স্নায়ু ক্ষতিগ্রস্ত হয়ে পায়ে ঝিমঝিম করা, অবশ ভাব ও জ্বালাপোড়ার অনুভূতি তৈরি হওয়া।</p>'
            ],
            [
                'name_en' => 'Nasal Polyps', 'name_bn' => 'নাকের পলিপ', 'slug' => 'nasal-polyps', 'letter' => 'N',
                'specialty' => 'ent', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Nasal polyps are soft, painless, noncancerous growths on the lining of your nasal passages or sinuses, causing chronic congestion.</p>',
                'overview_bn' => '<p>নাকের ভেতরের আবরণের আঙুরের দানার মতো মাংসপিণ্ড বৃদ্ধি যার ফলে দীর্ঘস্থায়ী নাক বন্ধ ও শ্বাসকষ্ট হয়।</p>'
            ],

            // Letter O
            [
                'name_en' => 'Osteoarthritis', 'name_bn' => 'অস্টিওআর্থ্রাইটিস (হাঁটুর ক্ষয়রোগ)', 'slug' => 'osteoarthritis', 'letter' => 'O',
                'specialty' => 'orthopedics', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Osteoarthritis is the most common form of arthritis, occurring when protective cartilage that cushions the ends of the bones wears down over time.</p>',
                'overview_bn' => '<p>হাঁটু ও অন্যান্য জয়েন্টের তরুণাস্থি বা কার্টিলেজ ক্ষয়ে গিয়ে হাড়ের ঘর্ষণে তীব্র ব্যথা ও হাঁটাচলায় কষ্ট হওয়া।</p>'
            ],
            [
                'name_en' => 'Osteoporosis', 'name_bn' => 'অস্টিওপোরোসিস (হাড়ের ভঙ্গুরতা)', 'slug' => 'osteoporosis', 'letter' => 'O',
                'specialty' => 'orthopedics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Osteoporosis causes bones to become weak and brittle, so brittle that a fall or even mild stresses such as bending over or coughing can cause a fracture.</p>',
                'overview_bn' => '<p>হাড়ের ঘনত্ব ও ক্যালসিয়াম কমে গিয়ে হাড় অত্যন্ত ভঙ্গুর হয়ে যাওয়া, ফলে সামান্য আঘাতেই হাড় ভেঙে যায়।</p>'
            ],
            [
                'name_en' => 'Obesity', 'name_bn' => 'স্থূলতা বা অতিরিক্ত ওজন', 'slug' => 'obesity', 'letter' => 'O',
                'specialty' => 'endocrinology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Obesity is a complex disease involving an excessive amount of body fat, significantly raising risk of cardiovascular problems and diabetes.</p>',
                'overview_bn' => '<p>দেহে অতিরিক্ত চর্বি জমাজনিত অবস্থা যা হৃদরোগ, ডায়াবেটিস এবং উচ্চ রক্তচাপের প্রধান অনুঘটক হিসেবে কাজ করে।</p>'
            ],
            [
                'name_en' => 'Obsessive-Compulsive Disorder (OCD)', 'name_bn' => 'ওসিডি বা শুচিবাই রোগ', 'slug' => 'obsessive-compulsive-disorder-ocd', 'letter' => 'O',
                'specialty' => 'psychiatry', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>OCD features a pattern of unwanted thoughts and fears (obsessions) that lead you to do repetitive behaviors (compulsions).</p>',
                'overview_bn' => '<p>অযাচিত ভাবনা ও ভীতি যাতে ব্যক্তি বারবার একই কাজ (যেমন হাত ধোয়া, তালা পরীক্ষা করা) করতে বাধ্য অনুভব করে।</p>'
            ],

            // Letter P
            [
                'name_en' => 'Pneumonia', 'name_bn' => 'নিউমোনিয়া', 'slug' => 'pneumonia', 'letter' => 'P',
                'specialty' => 'pulmonology', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>Pneumonia is an infection that inflames the air sacs in one or both lungs, which may fill with fluid or pus, causing cough with phlegm and difficult breathing.</p>',
                'overview_bn' => '<p>ফুসফুসের বায়ুথলির তীব্র ইনফেকশন যাতে কফ, উচ্চ জ্বর এবং শ্বাস নিতে তীব্র কষ্ট দেখা দেয়; শিশুদের ক্ষেত্রে এটি জরুরি অবস্থা।</p>'
            ],
            [
                'name_en' => 'Peptic Ulcer Disease', 'name_bn' => 'পেপটিক আলসার', 'slug' => 'peptic-ulcer-disease', 'letter' => 'P',
                'specialty' => 'gastroenterology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Peptic ulcers are open sores that develop on the inside lining of your stomach and the upper portion of your small intestine.</p>',
                'overview_bn' => '<p>পাকস্থলী বা ক্ষুদ্রান্ত্রের ভেতরের স্তরে গভীর ক্ষত সৃষ্টি হওয়া যার ফলে খালি পেটে তীব্র জ্বালাপোড়া ব্যথা হয়।</p>'
            ],
            [
                'name_en' => 'Polycystic Ovary Syndrome (PCOS)', 'name_bn' => 'পিসিওএস (পলিসিস্টিক ওভারি সিন্ড্রোম)', 'slug' => 'polycystic-ovary-syndrome-pcos', 'letter' => 'P',
                'specialty' => 'gynecology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>PCOS is a hormonal disorder common among women of reproductive age, leading to infrequent or prolonged menstrual periods and excess androgen levels.</p>',
                'overview_bn' => '<p>প্রজননক্ষম নারীদের হরমোন ভারসাম্যহীনতাজনিত ব্যাধি যাতে অনিয়মিত মাসিক, মুখে অবাঞ্ছিত লোম ও গর্ভধারণে জটিলতা হয়।</p>'
            ],
            [
                'name_en' => 'Parkinson\'s Disease', 'name_bn' => 'পারকিনসন রোগ', 'slug' => 'parkinsons-disease', 'letter' => 'P',
                'specialty' => 'neurology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Parkinson\'s disease is a brain disorder that causes unintended or uncontrollable movements, such as shaking, stiffness, and difficulty with balance.</p>',
                'overview_bn' => '<p>মস্তিষ্কের স্নায়বিক রোগ যাতে হাত-পা কাঁপা, চলাফেরায় ধীরগতি এবং শরীরের ভারসাম্য রক্ষায় সমস্যা তৈরি হয়।</p>'
            ],
            [
                'name_en' => 'Psoriasis', 'name_bn' => 'সোরিয়াসিস', 'slug' => 'psoriasis', 'letter' => 'P',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Psoriasis is a skin disease that causes a rash with itchy, scaly patches, most commonly on the knees, elbows, trunk and scalp.</p>',
                'overview_bn' => '<p>ত্বকের অটোইমিউন রোগ যাতে ত্বকের কোষ অতিদ্রুত বেড়ে রুপালী আঁশের মতো খসখসে লালচে স্তর তৈরি করে।</p>'
            ],

            // Letter Q
            [
                'name_en' => 'Q Fever', 'name_bn' => 'কিউ ফিভার', 'slug' => 'q-fever', 'letter' => 'Q',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Q fever is a bacterial infection caused by Coxiella burnetii, transmitted from livestock, causing high fevers, severe headache, and muscle aches.</p>',
                'overview_bn' => '<p>পশুপাখি থেকে ছড়ানো ব্যাকটেরিয়াজনিত সংক্রমণ যার লক্ষণ উচ্চ জ্বর, তীব্র মাথাব্যথা এবং মাংসপেশির তীব্র যন্ত্রণা।</p>'
            ],
            [
                'name_en' => 'Quinsy (Peritonsillar Abscess)', 'name_bn' => 'কুইনসি (টনসিলের পুঁজফোড়া)', 'slug' => 'quinsy-peritonsillar-abscess', 'letter' => 'Q',
                'specialty' => 'ent', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Quinsy is a complication of tonsillitis where an abscess forms between the tonsil and the wall of the throat, causing inability to swallow.</p>',
                'overview_bn' => '<p>টনসিলের তীব্র জটিলতা যাতে টনসিলের পেছনে পুঁজ জমে গলা সম্পূর্ণ বন্ধ হয়ে যাওয়ার উপক্রম হয়; জরুরি চিকিৎসা প্রয়োজন।</p>'
            ],

            // Letter R
            [
                'name_en' => 'Rheumatoid Arthritis', 'name_bn' => 'রিউমাটয়েড আর্থ্রাইটিস (গেঁটে বাত)', 'slug' => 'rheumatoid-arthritis', 'letter' => 'R',
                'specialty' => 'rheumatology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Rheumatoid arthritis is a chronic inflammatory disorder that can affect more than just your joints, attacking joint linings and causing painful swelling.</p>',
                'overview_bn' => '<p>দেহের রোগ প্রতিরোধ ব্যবস্থার ভুলের কারণে হাতের ও পায়ের ক্ষুদ্র জোড়ায় স্থায়ী প্রদাহ, তীব্র ব্যথা ও বিকৃতি সৃষ্টিকারী রোগ।</p>'
            ],
            [
                'name_en' => 'Rabies', 'name_bn' => 'জলাতঙ্ক রোগ (রেবিস)', 'slug' => 'rabies', 'letter' => 'R',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Rabies is a preventable viral disease transmitted through the bite of a rabid animal, attacking the central nervous system with fatal consequences if untreated.</p>',
                'overview_bn' => '<p>সংক্রমিত কুকুর বা বিড়ালের কামড়ে ছড়ানো মারাত্মক ভাইরাসজনিত ব্যাধি যাতে পানিভীতি, অস্থিরতা ও শ্বাসকষ্ট দেখা দেয়।</p>'
            ],
            [
                'name_en' => 'Respiratory Syncytial Virus (RSV)', 'name_bn' => 'আরএসভি সংক্রমণ (শিশুদের শ্বাসকষ্ট)', 'slug' => 'respiratory-syncytial-virus-rsv', 'letter' => 'R',
                'specialty' => 'pediatrics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>RSV causes infections of the lungs and respiratory tract, commonly affecting infants and young children with wheezing and bronchiolitis.</p>',
                'overview_bn' => '<p>ছোট শিশুদের ফুসফুস ও শ্বাসনালীর তীব্র ভাইরাস সংক্রমণ যাতে বুকের ভেতর বাঁশির মতো আওয়াজ ও শ্বাসকষ্ট হয়।</p>'
            ],

            // Letter S
            [
                'name_en' => 'Stroke (Cerebrovascular Accident)', 'name_bn' => 'স্ট্রোক (মস্তিষ্কে রক্তক্ষরণ বা জমাট)', 'slug' => 'stroke', 'letter' => 'S',
                'specialty' => 'neurology', 'is_featured' => true, 'is_emergency' => true,
                'overview_en' => '<p>A stroke occurs when the blood supply to part of your brain is interrupted or reduced, preventing brain tissue from getting oxygen and nutrients.</p>',
                'overview_bn' => '<p>মস্তিষ্কের রক্তনালী বন্ধ বা ফেটে রক্তক্ষরণ হওয়া; লক্ষণ: একপাশ অবশ হওয়া, মুখ বাঁকা হয়ে যাওয়া ও কথা জড়িয়ে যাওয়া। তাৎক্ষণিক জরুরি সেবা দরকার।</p>'
            ],
            [
                'name_en' => 'Sinusitis', 'name_bn' => 'সাইনুসাইটিস', 'slug' => 'sinusitis', 'letter' => 'S',
                'specialty' => 'ent', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Sinusitis is an inflammation or swelling of the tissue lining the sinuses, causing facial pain, nasal congestion, and headaches around eyes.</p>',
                'overview_bn' => '<p>মাথার খুলির সাইনাস প্রকোষ্ঠের প্রদাহ যাতে কপাল, চোখের চারপাশ ও গালে ভারী চাপ এবং নাক দিয়ে হলুদ কফ পড়ে।</p>'
            ],
            [
                'name_en' => 'Sciatica', 'name_bn' => 'সায়াটিকা (পায়ের রগে টান)', 'slug' => 'sciatica', 'letter' => 'S',
                'specialty' => 'orthopedics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Sciatica refers to pain that travels along the path of the sciatic nerve, branching from lower back through hips and buttock and down each leg.</p>',
                'overview_bn' => '<p>মেরুদণ্ডের ডিস্ক চেপে সায়াটিক স্নায়ুতে চাপ পড়ার কারণে কোমর থেকে পা ও আঙুল পর্যন্ত বিদ্যুৎ চমকানোর মতো তীব্র ব্যথা।</p>'
            ],
            [
                'name_en' => 'Scabies', 'name_bn' => 'খোসপাঁচড়া বা স্ক্যাবিস', 'slug' => 'scabies', 'letter' => 'S',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Scabies is an itchy skin infestation caused by a tiny burrowing mite called Sarcoptes scabiei, intensifying drastically at night.</p>',
                'overview_bn' => '<p>অণুবীক্ষণিক জীবাণুর ত্বকে গর্ত করার কারণে আঙুলের ফাঁকে ও খাঁজে তীব্র রাতের চুলকানি ও ঘা হওয়ার অতি ছোঁয়াচে রোগ।</p>'
            ],
            [
                'name_en' => 'Sleep Apnea', 'name_bn' => 'স্লিপ অ্যাপনিয়া (ঘুমে দম বন্ধ হওয়া)', 'slug' => 'sleep-apnea', 'letter' => 'S',
                'specialty' => 'pulmonology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Sleep apnea is a potentially serious sleep disorder in which breathing repeatedly stops and starts, accompanied by loud snoring.</p>',
                'overview_bn' => '<p>ঘুমের মধ্যে বারবার শ্বাসনালী বন্ধ হয়ে কিছুক্ষণের জন্য শ্বাস থেমে যাওয়া এবং উচ্চশব্দে নাক ডাকার সমস্যা।</p>'
            ],

            // Letter T
            [
                'name_en' => 'Typhoid Fever', 'name_bn' => 'টাইফয়েড জ্বর', 'slug' => 'typhoid-fever', 'letter' => 'T',
                'specialty' => 'medicine', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Typhoid fever is a bacterial infection caused by Salmonella typhi, spreading through contaminated food and water, with gradual rising fever.</p>',
                'overview_bn' => '<p>দূষিত পানি ও খাবারের মাধ্যমে ছড়ানো সালমোনেলা টাইফি ব্যাকটেরিয়াজনিত রোগ যাতে ধাপে ধাপে দীর্ঘস্থায়ী উচ্চ জ্বর ও পেটে ব্যথা হয়।</p>'
            ],
            [
                'name_en' => 'Tuberculosis (TB)', 'name_bn' => 'যক্ষ্মা রোগ (টিবি)', 'slug' => 'tuberculosis-tb', 'letter' => 'T',
                'specialty' => 'pulmonology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Tuberculosis is a potentially serious infectious disease that mainly affects the lungs, characterized by coughing for more than 3 weeks, fever, and night sweats.</p>',
                'overview_bn' => '<p>মাইকোব্যাকটেরিয়াম দ্বারা ফুসফুসের মারাত্মক সংক্রমণ যার প্রধান লক্ষণ ৩ সপ্তাহের বেশি কাশি, কাশির সাথে রক্ত ও রাতে জ্বর-ঘাম।</p>'
            ],
            [
                'name_en' => 'Tonsillitis', 'name_bn' => 'টনসিলাইটিস', 'slug' => 'tonsillitis', 'letter' => 'T',
                'specialty' => 'ent', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Tonsillitis is inflammation of the tonsils, two oval-shaped pads of tissue at the back of the throat, causing sore throat, difficulty swallowing, and tender lymph nodes.</p>',
                'overview_bn' => '<p>গলার ভেতরের টনসিল গ্রন্থির প্রদাহ ও ফুলে যাওয়া যাতে তীব্র গলাব্যথা, খাবার গিলতে কষ্ট ও জ্বর হয়।</p>'
            ],
            [
                'name_en' => 'Thalassemia', 'name_bn' => 'থ্যালাসেমিয়া', 'slug' => 'thalassemia', 'letter' => 'T',
                'specialty' => 'hematology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Thalassemia is an inherited blood disorder that causes your body to have less hemoglobin than normal, requiring regular blood transfusions in severe cases.</p>',
                'overview_bn' => '<p>বংশগত রক্তরোগ যাতে দেহের হিমোগ্লোবিন সঠিকভাবে তৈরি হতে পারে না এবং রোগীকে নিয়মিত রক্ত গ্রহণ করতে হয়।</p>'
            ],
            [
                'name_en' => 'Tinnitus', 'name_bn' => 'টিনিটাস (কানে ভোঁ ভোঁ শব্দ)', 'slug' => 'tinnitus', 'letter' => 'T',
                'specialty' => 'ent', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Tinnitus is when you experience ringing or other noises in one or both of your ears that are not caused by external sound.</p>',
                'overview_bn' => '<p>বাইরে কোনো শব্দ না থাকা সত্ত্বেও কানে সার্বক্ষণিক ভোঁ-ভোঁ বা শাঁ-শাঁ শব্দ শোনার অনুভূতি।</p>'
            ],

            // Letter U
            [
                'name_en' => 'Urinary Tract Infection (UTI)', 'name_bn' => 'ইউরিনারি ট্র্যাক্ট ইনফেকশন (ইউটিআই)', 'slug' => 'urinary-tract-infection-uti', 'letter' => 'U',
                'specialty' => 'urology', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>A UTI is an infection in any part of your urinary system—kidneys, ureters, bladder, and urethra—causing burning sensation during urination.</p>',
                'overview_bn' => '<p>মূত্রথলি বা মূত্রনালীর ব্যাকটেরিয়াজনিত ইনফেকশন যাতে প্রস্রাবের সময় তীব্র জ্বালাপোড়া ও বারবার প্রস্রাবের বেগ হয়।</p>'
            ],
            [
                'name_en' => 'Ulcerative Colitis', 'name_bn' => 'আলসারেটিভ কোলাইটিস', 'slug' => 'ulcerative-colitis', 'letter' => 'U',
                'specialty' => 'gastroenterology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Ulcerative colitis is an inflammatory bowel disease (IBD) that causes long-lasting inflammation and ulcers in your digestive tract.</p>',
                'overview_bn' => '<p>বৃহদন্ত্রের অভ্যন্তরীণ দেয়ালে দীর্ঘস্থায়ী প্রদাহ ও রক্তক্ষরণকারী আলসার তৈরিকারী রোগ।</p>'
            ],
            [
                'name_en' => 'Urticaria (Hives)', 'name_bn' => 'আমবাত বা আর্টিকেরিয়া', 'slug' => 'urticaria-hives', 'letter' => 'U',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Hives are red, itchy welts that result from a skin reaction to foods, medication, or emotional triggers, appearing suddenly.</p>',
                'overview_bn' => '<p>অ্যালার্জির কারণে ত্বকে হঠাৎ চাকা চাকা লাল দাগ ও তীব্র চুলকানি দেখা দেওয়ার অবস্থা।</p>'
            ],
            [
                'name_en' => 'Uveitis', 'name_bn' => 'ইউভিয়াইটিস (চোখের ভেতরের প্রদাহ)', 'slug' => 'uveitis', 'letter' => 'U',
                'specialty' => 'ophthalmology', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Uveitis is inflammation of the middle layer of tissue in the eyeball wall (uvea), causing eye redness, pain, and floaters.</p>',
                'overview_bn' => '<p>চোখের দেয়ালের মধ্যস্তরের প্রদাহ যাতে দ্রুত চিকিৎসা না করালে স্থায়ী দৃষ্টিহানি ঘটতে পারে।</p>'
            ],

            // Letter V
            [
                'name_en' => 'Varicose Veins', 'name_bn' => 'ভেরিকোজ ভেইনস (শিরা ফুলে যাওয়া)', 'slug' => 'varicose-veins', 'letter' => 'V',
                'specialty' => 'surgery', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Varicose veins are twisted, enlarged veins, most commonly found in the legs and feet, caused by increased blood pressure in veins.</p>',
                'overview_bn' => '<p>পায়ের শিরাগুলোর কপাটিকা দুর্বল হয়ে নীল বা বেগুনি বর্ণ ধারণ করে মোটা ও প্যাঁচানো হয়ে ফুলে ওঠা।</p>'
            ],
            [
                'name_en' => 'Vertigo', 'name_bn' => 'ভার্টিগো বা মাথা ঘোরা', 'slug' => 'vertigo', 'letter' => 'V',
                'specialty' => 'ent', 'is_featured' => true, 'is_emergency' => false,
                'overview_en' => '<p>Vertigo is a sensation of feeling off balance, or that you or your environment is spinning, commonly caused by inner ear problems.</p>',
                'overview_bn' => '<p>কানের ভেতরের ভারসাম্যের বিঘ্নের কারণে চারপাশ বনবন করে ঘোরার অনুভূতি ও দাঁড়িয়ে থাকতে না পারা।</p>'
            ],
            [
                'name_en' => 'Vitiligo', 'name_bn' => 'শ্বেতী রোগ বা ভিটিলিগো', 'slug' => 'vitiligo', 'letter' => 'V',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Vitiligo is a disease that causes loss of skin color in patches when pigment-producing cells die or stop functioning.</p>',
                'overview_bn' => '<p>ত্বকের মেলানিন উৎপাদক কোষ নষ্ট হয়ে ত্বকের বিভিন্ন স্থানে দুধের মতো সাদা দাগ তৈরি হওয়া।</p>'
            ],
            [
                'name_en' => 'Viral Hepatitis', 'name_bn' => 'ভাইরাল হেপাটাইটিস', 'slug' => 'viral-hepatitis', 'letter' => 'V',
                'specialty' => 'hepatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Viral hepatitis refers to a viral infection of the liver (Hepatitis A, B, C, or E) causing acute liver inflammation, fatigue, and jaundice.</p>',
                'overview_bn' => '<p>হেপাটাইটিস ভাইরাস দ্বারা লিভারের তীব্র প্রদাহ যাতে ক্ষুধামন্দা, দুর্বলতা ও জন্ডিস দেখা দেয়।</p>'
            ],

            // Letter W
            [
                'name_en' => 'Whooping Cough (Pertussis)', 'name_bn' => 'হুপিং কাশি (পারটুসিস)', 'slug' => 'whooping-cough-pertussis', 'letter' => 'W',
                'specialty' => 'pediatrics', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Whooping cough is a highly contagious respiratory tract infection marked by a severe hacking cough followed by a high-pitched intake of breath.</p>',
                'overview_bn' => '<p>তীব্র ছোঁয়াচে ব্যাকটেরিয়াজনিত কাশি যাতে একনাগাড়ে কাশির দমক শেষে শ্বাস নেওয়ার সময় উচ্চ হুপিং শব্দ হয়।</p>'
            ],
            [
                'name_en' => 'Warts (Human Papillomavirus)', 'name_bn' => 'আঁচিল বা ওয়ার্টস', 'slug' => 'warts-hpv', 'letter' => 'W',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Warts are small, grainy skin growths that occur most often on your fingers or hands caused by the human papillomavirus (HPV).</p>',
                'overview_bn' => '<p>এইচপিভি ভাইরাসের সংক্রমণে ত্বক ও আঙুলের ওপর তৈরি হওয়া শক্ত দানাদার মাংসের উপবৃদ্ধি।</p>'
            ],
            [
                'name_en' => 'Wilson\'s Disease', 'name_bn' => 'উইলসন ডিজিজ', 'slug' => 'wilsons-disease', 'letter' => 'W',
                'specialty' => 'hepatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Wilson\'s disease is an inherited disorder that causes excessive copper accumulation in organs like liver and brain.</p>',
                'overview_bn' => '<p>বংশগত মেটাবলিক ব্যাধি যাতে লিভার ও মস্তিষ্কে অতিরিক্ত তামা বা কপার জমে ক্ষতিসাধন করে।</p>'
            ],

            // Letter X
            [
                'name_en' => 'Xeroderma (Severe Dry Skin)', 'name_bn' => 'জেরোডার্মা (চরম শুষ্ক ত্বক)', 'slug' => 'xeroderma-severe-dry-skin', 'letter' => 'X',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Xeroderma refers to abnormally dry skin resulting from a lack of moisture in the stratum corneum, causing scaling, cracking, and intense itching.</p>',
                'overview_bn' => '<p>ত্বকের আর্দ্রতা মারাত্মকভাবে কমে গিয়ে ত্বক খসখসে হওয়া, ফেটে যাওয়া এবং চুলকানি তৈরি হওয়া।</p>'
            ],
            [
                'name_en' => 'Xanthelasma', 'name_bn' => 'জ্যান্থেলাসমা (চোখের পাতায় চর্বি জমা)', 'slug' => 'xanthelasma', 'letter' => 'X',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Xanthelasma is a sharply demarcated yellowish deposit of fat underneath the skin, usually on or around the eyelids, often indicating high cholesterol.</p>',
                'overview_bn' => '<p>চোখের পাতার চারপাশে হলুদাভ চর্বির আস্তরণ যা রক্তে উচ্চমাত্রার কোলেস্টেরলের নির্দেশক হতে পারে।</p>'
            ],

            // Letter Y
            [
                'name_en' => 'Yellow Fever', 'name_bn' => 'ইয়েলো ফিভার বা পীতজ্বর', 'slug' => 'yellow-fever', 'letter' => 'Y',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => true,
                'overview_en' => '<p>Yellow fever is a viral infection spread by mosquitoes causing fever, chills, loss of appetite, nausea, and jaundice in severe cases.</p>',
                'overview_bn' => '<p>মশার কামড়ে ছড়ানো ভাইরাসজনিত মারাত্মক জ্বর যাতে উচ্চ জ্বর, শরীর ব্যথা ও তীব্র জন্ডিস দেখা দেয়।</p>'
            ],
            [
                'name_en' => 'Yeast Infection (Candidiasis)', 'name_bn' => 'ইস্ট ইনফেকশন বা ক্যান্ডিডিয়াসিস', 'slug' => 'yeast-infection-candidiasis', 'letter' => 'Y',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Candidiasis is a fungal infection caused by a yeast called Candida, commonly affecting mouth (thrush), skin folds, or reproductive tract.</p>',
                'overview_bn' => '<p>ক্যান্ডিডা ছত্রাকের অতিবৃদ্ধির ফলে মুখ, ত্বক বা জননাঙ্গে সাদা আস্তরণ, চুলকানি ও প্রদাহ সৃষ্টি।</p>'
            ],

            // Letter Z
            [
                'name_en' => 'Zika Virus Infection', 'name_bn' => 'জিকা ভাইরাস সংক্রমণ', 'slug' => 'zika-virus-infection', 'letter' => 'Z',
                'specialty' => 'medicine', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Zika virus disease is caused by a virus transmitted primarily by Aedes mosquitoes, presenting with fever, rash, conjunctivitis, and muscle pain.</p>',
                'overview_bn' => '<p>এডিস মশার মাধ্যমে ছড়ানো ভাইরাস সংক্রমণ যা গর্ভবতী মা আক্রান্ত হলে নবজাতকের জন্মগত ত্রুটি ঘটাতে পারে।</p>'
            ],
            [
                'name_en' => 'Zoster (Herpes Zoster / Shingles)', 'name_bn' => 'হারপিস জোস্টার বা কোঁচদাদ', 'slug' => 'herpes-zoster-shingles', 'letter' => 'Z',
                'specialty' => 'dermatology', 'is_featured' => false, 'is_emergency' => false,
                'overview_en' => '<p>Shingles is a viral infection that causes a painful rash caused by the varicella-zoster virus, typically appearing as a single stripe of blisters on one side of the torso.</p>',
                'overview_bn' => '<p>জলবসন্তের সুপ্ত ভাইরাসের পুনরাবির্ভাবে শরীরের একপাশের নির্দিষ্ট স্নায়ুপথে তীব্র ব্যথাদায়ক পানিপূর্ণ ফুসকুড়ির রেখা তৈরি হওয়া।</p>'
            ],
        ];

        $seededDiseases = [];
        foreach ($diseasesList as $item) {
            $specId = $specialties[$item['specialty']] ?? $fallbackSpecialty;

            $d = Disease::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'primary_specialty_id' => $specId,
                    'name' => [
                        'en' => $item['name_en'],
                        'bn' => $item['name_bn'],
                    ],
                    'first_letter' => $item['letter'],
                    'is_featured' => $item['is_featured'],
                    'is_emergency' => $item['is_emergency'],
                    'status' => 'published',
                    'published_at' => now(),
                    'overview' => [
                        'en' => $item['overview_en'],
                        'bn' => $item['overview_bn'],
                    ],
                    'symptoms_summary' => [
                        'en' => '<ul><li>Primary clinical indications of ' . $item['name_en'] . '</li><li>Associated localized symptoms</li><li>General systemic signs</li></ul>',
                        'bn' => '<ul><li>' . $item['name_bn'] . '-এর প্রধান ক্লিনিক্যাল উপসর্গ</li><li>সংশ্লিষ্ট অঙ্গের প্রত্যক্ষ লক্ষণ</li><li>সার্বিক শারীরিক অসুস্থতা</li></ul>',
                    ],
                    'causes' => [
                        'en' => '<p>Multiple genetic, physiological, environmental, and pathological factors contribute to ' . $item['name_en'] . '.</p>',
                        'bn' => '<p>জিনগত, শারীরবৃত্তীয়, পারিপার্শ্বিক এবং জীবাণুঘটিত বিভিন্ন কারণে ' . $item['name_bn'] . ' দেখা দিতে পারে।</p>',
                    ],
                    'prevention' => [
                        'en' => '<p>Healthy lifestyle adjustments, targeted vaccination or hygiene protocols, and regular medical screenings help mitigate risks.</p>',
                        'bn' => '<p>পরিমিত জীবনযাপন, স্বাস্থ্যবিধি অনুসরণ, নিয়মিত স্ক্রিনিং এবং চিকিৎসকের পরামর্শ অনুযায়ী চলা রোগের ঝুঁকি কমায়।</p>',
                    ],
                    'when_to_see_doctor' => [
                        'en' => '<p>Consult an expert physician if symptoms persist for more than a few days, worsen suddenly, or interfere with daily living.</p>',
                        'bn' => '<p>উপসর্গ কয়েকদিনের বেশি স্থায়ী হলে বা তীব্র রূপ ধারণ করলে অবিলম্বে সংশ্লিষ্ট বিশেষজ্ঞ চিকিৎসকের পরামর্শ নিন।</p>',
                    ],
                    'diagnosis_treatment' => [
                        'en' => '<p>Diagnosis involves clinical examination, targeted laboratory tests, and imaging, followed by individualized treatment.</p>',
                        'bn' => '<p>শারীরিক পরীক্ষা, প্রয়োজনীয় ল্যাব টেস্ট ও সনাক্তকরণ পরীক্ষার মাধ্যমে সঠিক চিকিৎসা পরিকল্পনা নির্ধারণ করা হয়।</p>',
                    ]
                ]
            );

            // Add standard Mayo Clinic & BMDC reference
            DiseaseReference::updateOrCreate(
                ['disease_id' => $d->id, 'source_name' => 'Mayo Clinic'],
                [
                    'source_url' => 'https://www.mayoclinic.org/diseases-conditions',
                    'notes' => 'Mayo Clinic International Health Library',
                    'order_index' => 1,
                ]
            );

            $seededDiseases[$item['slug']] = $d;
        }

        // ─────────────────────────────────────────────────────────────
        // 3. Comprehensive Master Symptoms (Adult & Child)
        // ─────────────────────────────────────────────────────────────
        $symptomsData = [
            // Head & Brain
            [
                'slug' => 'headache',
                'body_part' => 'head-brain',
                'name' => ['en' => 'Headache', 'bn' => 'মাথাব্যথা'],
                'first_letter' => 'H',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Pain, pressure, or throbbing in any region of the head or neck.',
                    'bn' => 'মাথার যেকোনো অংশে হালকা বা তীব্র ব্যথা, চাপ বা দপদপানি অনুভূতি।'
                ]
            ],
            [
                'slug' => 'dizziness-vertigo',
                'body_part' => 'head-brain',
                'name' => ['en' => 'Dizziness / Vertigo', 'bn' => 'মাথা ঘোরা বা ভার্টিগো'],
                'first_letter' => 'D',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'A feeling of being lightheaded, off-balance, or spinning surroundings.',
                    'bn' => 'মাথা হালকা লাগা, ভারসাম্যহীনতা বা চারপাশ বনবন করে ঘোরার অনুভূতি।'
                ]
            ],
            [
                'slug' => 'sudden-severe-headache',
                'body_part' => 'head-brain',
                'name' => ['en' => 'Thunderclap Severe Headache', 'bn' => 'হঠাৎ তীব্র বজ্রপাতের মতো মাথাব্যথা'],
                'first_letter' => 'T',
                'is_emergency' => true,
                'is_featured' => false,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Sudden and excruciating headache peaking within seconds (red flag for hemorrhage).',
                    'bn' => 'হঠাৎ শুরু হওয়া প্রচণ্ড অসহ্য মাথাব্যথা যা কয়েক সেকেন্ডের মধ্যে সর্বোচ্চ তীব্রতায় পৌঁছায়।'
                ]
            ],
            [
                'slug' => 'facial-numbness-weakness',
                'body_part' => 'head-brain',
                'name' => ['en' => 'Facial Drooping / Sudden Weakness', 'bn' => 'মুখ বেঁকে যাওয়া বা হাত-পা অবশ হওয়া'],
                'first_letter' => 'F',
                'is_emergency' => true,
                'is_featured' => true,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Sudden numbness, weakness, or drooping on one side of face or body (Stroke warning).',
                    'bn' => 'হঠাৎ মুখ বাঁকা হয়ে যাওয়া, একপাশের হাত বা পা নাড়াতে না পারা (স্ট্রোক নির্দেশক লক্ষণ)।'
                ]
            ],

            // Eyes & Vision
            [
                'slug' => 'blurred-vision',
                'body_part' => 'eyes-vision',
                'name' => ['en' => 'Blurred Vision', 'bn' => 'দৃষ্টি ঝাপসা হওয়া'],
                'first_letter' => 'B',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Loss of sharpness of eyesight, making objects appear out of focus and hazy.',
                    'bn' => 'চোখে স্পষ্ট দেখতে না পাওয়া বা দৃষ্টিশক্তি ঘোলাটে ও ঝাপসা লাগা।'
                ]
            ],
            [
                'slug' => 'eye-pain-redness',
                'body_part' => 'eyes-vision',
                'name' => ['en' => 'Eye Pain & Redness', 'bn' => 'চোখে তীব্র ব্যথা ও লালচে ভাব'],
                'first_letter' => 'E',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Burning, throbbing, or aching in the eyeball accompanied by bloodshot sclera.',
                    'bn' => 'চোখ লাল হয়ে যাওয়া, খচখচ করা বা চোখের ভেতর অস্বস্তিকর ব্যথা।'
                ]
            ],

            // Ears, Nose & Throat
            [
                'slug' => 'sore-throat',
                'body_part' => 'ears-nose-throat',
                'name' => ['en' => 'Sore Throat', 'bn' => 'গলাব্যথা বা খুসখুস করা'],
                'first_letter' => 'S',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Pain, scratchiness, or irritation of the throat that often worsens when you swallow.',
                    'bn' => 'গলায় অস্বস্তি, ব্যথা বা ঢোক গিলতে কষ্ট হওয়া।'
                ]
            ],
            [
                'slug' => 'earache-ear-discharge',
                'body_part' => 'ears-nose-throat',
                'name' => ['en' => 'Earache & Ear Discharge', 'bn' => 'কানে ব্যথা বা পুঁজ পড়া'],
                'first_letter' => 'E',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Sharp or dull pain inside the ear, possibly with fluid discharge or decreased hearing.',
                    'bn' => 'কানের ভেতরে তীব্র বা টনটনে ব্যথা এবং তরল বা পুঁজ নির্গমন।'
                ]
            ],
            [
                'slug' => 'nasal-congestion',
                'body_part' => 'ears-nose-throat',
                'name' => ['en' => 'Nasal Congestion & Runny Nose', 'bn' => 'নাক বন্ধ থাকা ও সর্দি'],
                'first_letter' => 'N',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Stuffy nose caused by inflamed blood vessels in the membranes lining the inside of nostrils.',
                    'bn' => 'নাকের রাস্তা বন্ধ হয়ে শ্বাস নিতে কষ্ট হওয়া এবং নাক দিয়ে পাতলা বা ঘন তরল ঝরা।'
                ]
            ],

            // Chest & Heart
            [
                'slug' => 'chest-pain',
                'body_part' => 'chest-heart',
                'name' => ['en' => 'Chest Pain or Pressure', 'bn' => 'বুকে ব্যথা বা ভারী চাপ'],
                'first_letter' => 'C',
                'is_emergency' => true,
                'is_featured' => true,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Discomfort, squeezing, or crushing pressure in chest radiating to arm, jaw, or neck.',
                    'bn' => 'বুকে ভারী পাথর চাপা অনুভূতি, নিংড়ানো ব্যথা যা হাত, চোয়াল বা পিঠে ছড়িয়ে পড়তে পারে।'
                ]
            ],
            [
                'slug' => 'palpitations',
                'body_part' => 'chest-heart',
                'name' => ['en' => 'Heart Palpitations', 'bn' => 'বুক ধড়ফড় করা'],
                'first_letter' => 'H',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Sensations of a racing, pounding, or fluttering heartbeat while resting.',
                    'bn' => 'হঠাৎ হৃদস্পন্দন খুব দ্রুত হওয়া, বুক ধড়াস ধড়াস করা বা লাফাচ্ছে মনে হওয়া।'
                ]
            ],

            // Lungs & Respiratory
            [
                'slug' => 'shortness-of-breath',
                'body_part' => 'lungs-respiratory',
                'name' => ['en' => 'Shortness of Breath (Dyspnea)', 'bn' => 'শ্বাসকষ্ট বা শ্বাস নিতে কষ্ট'],
                'first_letter' => 'S',
                'is_emergency' => true,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Difficult or labored breathing, or feeling unable to catch enough breath.',
                    'bn' => 'শ্বাস নিতে কষ্ট হওয়া বা বুক ভরে বাতাস টেনে নিতে না পারার হাঁসফাঁস অনুভূতি।'
                ]
            ],
            [
                'slug' => 'persistent-cough',
                'body_part' => 'lungs-respiratory',
                'name' => ['en' => 'Persistent Cough', 'bn' => 'দীর্ঘমেয়াদী কাশি'],
                'first_letter' => 'P',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'A cough lasting for several weeks, either dry and hacking or with thick mucus.',
                    'bn' => 'কয়েক সপ্তাহের বেশি স্থায়ী শুকনো বা কফযুক্ত খুসখুসে কাশি।'
                ]
            ],
            [
                'slug' => 'wheezing',
                'body_part' => 'lungs-respiratory',
                'name' => ['en' => 'Wheezing (Whistling Breath)', 'bn' => 'বুকে বাঁশির মতো শব্দ (হাঁপানি)'],
                'first_letter' => 'W',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'A high-pitched whistling sound produced during exhalation through narrowed airways.',
                    'bn' => 'শ্বাস ফেলার সময় শ্বাসনালী সরু হয়ে বুকের ভেতর থেকে বাঁশির মতো শাঁ শাঁ আওয়াজ।'
                ]
            ],

            // Abdomen & Digestive
            [
                'slug' => 'abdominal-pain',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Abdominal Pain', 'bn' => 'পেটে ব্যথা বা মোচড়'],
                'first_letter' => 'A',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Pain, cramp, or burning ache felt anywhere in the belly or stomach area.',
                    'bn' => 'পেটের যেকোনো অংশে মোচড়, জ্বালাপোড়া বা কামড়ানো ব্যথা।'
                ]
            ],
            [
                'slug' => 'severe-lower-right-pain',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Severe Lower Right Belly Pain', 'bn' => 'তলপেটের ডানদিকে তীব্র ব্যথা'],
                'first_letter' => 'S',
                'is_emergency' => true,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Acute, sharp pain localized in right lower abdomen (classic appendicitis warning).',
                    'bn' => 'নাভি থেকে তলপেটের ডানদিকে ছড়িয়ে পড়া অত্যন্ত তীব্র অসহ্য ব্যথা (অ্যাপেন্ডিসাইটিস সংকেত)।'
                ]
            ],
            [
                'slug' => 'nausea-vomiting',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Nausea and Vomiting', 'bn' => 'বমি বমি ভাব ও বমি'],
                'first_letter' => 'N',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Feeling sick to the stomach and forceful involuntary ejection of stomach contents.',
                    'bn' => 'পেটে অস্বস্তি, গা গোলানো এবং পাকস্থলীর খাবার অনিচ্ছাকৃতভাবে উগড়ে ফেলা।'
                ]
            ],
            [
                'slug' => 'diarrhea-loose-stools',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Diarrhea / Watery Stools', 'bn' => 'পাতলা পায়খানা বা ডায়রিয়া'],
                'first_letter' => 'D',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Frequent, loose, and watery bowel movements causing risk of dehydration.',
                    'bn' => 'স্বাভাবিকের চেয়ে বেশিবার পাতলা পানির মতো মলত্যাগ।'
                ]
            ],
            [
                'slug' => 'heartburn-acidity',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Heartburn & Acid Regurgitation', 'bn' => 'বুক জ্বালাপোড়া ও টক ঢেঁকুর'],
                'first_letter' => 'H',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'A burning feeling in the chest or throat caused by stomach acid regurgitating.',
                    'bn' => 'খাবারের পর বুকের মাঝে জ্বলুনি অনুভূতি এবং গলায় টক বা তেতো পানি উঠে আসা।'
                ]
            ],

            // Pelvic & Urinary
            [
                'slug' => 'painful-urination',
                'body_part' => 'pelvic-urinary',
                'name' => ['en' => 'Burning / Painful Urination', 'bn' => 'প্রস্রাবে জ্বালাপোড়া বা ব্যথা'],
                'first_letter' => 'B',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Discomfort, burning sting, or pain when passing urine.',
                    'bn' => 'প্রস্রাব ত্যাগের সময় মূত্রনালীতে তীব্র জ্বালাপোড়া বা ব্যথা অনুভব করা।'
                ]
            ],
            [
                'slug' => 'frequent-urination',
                'body_part' => 'pelvic-urinary',
                'name' => ['en' => 'Frequent Urination', 'bn' => 'ঘনঘন প্রস্রাবের বেগ'],
                'first_letter' => 'F',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Needing to urinate much more often than normal, especially throughout the night.',
                    'bn' => 'স্বাভাবিকের তুলনায় দিনে বা রাতে বারবার অতিরিক্ত প্রস্রাবের বেগ হওয়া।'
                ]
            ],
            [
                'slug' => 'pelvic-pain-lower-abdomen',
                'body_part' => 'pelvic-urinary',
                'name' => ['en' => 'Pelvic Pain / Lower Abdominal Cramps', 'bn' => 'তলপেটে দীর্ঘমেয়াদী ব্যথা বা টান'],
                'first_letter' => 'P',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Pain in the lowest part of the abdomen and pelvis, common with reproductive conditions.',
                    'bn' => 'তলপেট ও কুচকির সংযোগস্থলে ভারী অস্বস্তি বা মোচড়।'
                ]
            ],

            // Bones & Joints
            [
                'slug' => 'joint-pain-swelling',
                'body_part' => 'bones-joints',
                'name' => ['en' => 'Joint Pain & Swelling', 'bn' => 'হাড়ের জোড়ায় ব্যথা ও ফোলাভাব'],
                'first_letter' => 'J',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Discomfort, stiffness, or fluid swelling in knees, hips, hands, or feet.',
                    'bn' => 'হাঁটু, কোমর বা আঙুলের অস্থিসন্ধিতে টনটনে ব্যথা ও আড়ষ্টতা।'
                ]
            ],
            [
                'slug' => 'back-pain-lumbar',
                'body_part' => 'bones-joints',
                'name' => ['en' => 'Lower Back Pain', 'bn' => 'কোমর বা পিঠের তীব্র ব্যথা'],
                'first_letter' => 'L',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Ache, burning, or sharp pain in the lower spine that may radiate into legs.',
                    'bn' => 'কোমরের হাড়ে তীব্র ব্যথা যা দীর্ঘক্ষণ দাঁড়ালে বা ঝুঁকলে পায়ের দিকে ছড়িয়ে পড়ে।'
                ]
            ],

            // Skin & Hair
            [
                'slug' => 'skin-rash-itching',
                'body_part' => 'skin-hair',
                'name' => ['en' => 'Skin Rash & Itching', 'bn' => 'ত্বকে র্যাশ বা চুলকানি'],
                'first_letter' => 'S',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Red, irritated, bumpy, or scaly changes in skin texture with strong urge to scratch.',
                    'bn' => 'ত্বকের ওপর লালচে দানা, ফুসকুড়ি বা আঁশের মতো স্তর ও প্রচণ্ড চুলকানি।'
                ]
            ],

            // Whole Body & General
            [
                'slug' => 'high-fever',
                'body_part' => 'whole-body',
                'name' => ['en' => 'High Fever & Chills', 'bn' => 'উচ্চ জ্বর ও কাঁপুনি'],
                'first_letter' => 'H',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Body temperature above 100.4°F (38°C) accompanied by shivering or cold sensations.',
                    'bn' => 'শরীরের তাপমাত্রা ১০১° ফারেনহাইটের উপরে ওঠা এবং শীত শীত ভাব বা কাঁপুনি লাগা।'
                ]
            ],
            [
                'slug' => 'unexplained-fatigue',
                'body_part' => 'whole-body',
                'name' => ['en' => 'Severe Fatigue & Weakness', 'bn' => 'অতিরিক্ত ক্লান্তি ও দুর্বলতা'],
                'first_letter' => 'S',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'all',
                'description' => [
                    'en' => 'Extreme, lingering tiredness not relieved by rest that interferes with everyday life.',
                    'bn' => 'বিশ্রাম নেওয়ার পরও না কমা চরম অবসাদ, শারীরিক শক্তিহীনতা ও ঝিমুনি ভাব।'
                ]
            ],
            [
                'slug' => 'unexplained-weight-loss',
                'body_part' => 'whole-body',
                'name' => ['en' => 'Unexplained Rapid Weight Loss', 'bn' => 'অস্বাভাবিক দ্রুত ওজন কমে যাওয়া'],
                'first_letter' => 'U',
                'is_emergency' => false,
                'is_featured' => false,
                'age_group' => 'adult',
                'description' => [
                    'en' => 'Noticeable drop in body weight without dieting or deliberate exercise.',
                    'bn' => 'কোনো ডায়েট বা ব্যায়াম ছাড়া স্বল্প সময়ে নাটকীয়ভাবে ওজন কমে যাওয়া।'
                ]
            ],

            // Child Specific Symptoms
            [
                'slug' => 'child-high-fever-lethargy',
                'body_part' => 'whole-body',
                'name' => ['en' => 'Child High Fever with Lethargy', 'bn' => 'শিশুর উচ্চ জ্বর ও নেতিয়ে পড়া'],
                'first_letter' => 'C',
                'is_emergency' => true,
                'is_featured' => true,
                'age_group' => 'child',
                'description' => [
                    'en' => 'Spiking fever in infants or children accompanied by unresponsiveness or refusal to feed.',
                    'bn' => 'শিশুর তীব্র জ্বর, শরীর ঠান্ডা হয়ে যাওয়া, দুধ না খাওয়া বা অস্বাভাবিক নিস্তেজ হয়ে থাকা।'
                ]
            ],
            [
                'slug' => 'child-barking-cough-stridor',
                'body_part' => 'lungs-respiratory',
                'name' => ['en' => 'Child Barking Cough (Croup / Stridor)', 'bn' => 'শিশুর খুক খুক ঘেউ ঘেউ কাশি ও শ্বাসকষ্ট'],
                'first_letter' => 'C',
                'is_emergency' => true,
                'is_featured' => true,
                'age_group' => 'child',
                'description' => [
                    'en' => 'Harsh, barking seal-like cough or noisy whistling when breathing in (Croup alert).',
                    'bn' => 'শিশুর কাশি দিতে গিয়ে ঘেউ ঘেউ আওয়াজ হওয়া ও শ্বাস নিতে গিয়ে বুক দেবে যাওয়া।'
                ]
            ],
            [
                'slug' => 'child-severe-colic-crying',
                'body_part' => 'abdomen-digestive',
                'name' => ['en' => 'Child Inconsolable Crying & Colic', 'bn' => 'শিশুর পেটে তীব্র মোচড় ও অবিরাম কান্না'],
                'first_letter' => 'C',
                'is_emergency' => false,
                'is_featured' => true,
                'age_group' => 'child',
                'description' => [
                    'en' => 'Episodes of crying for more than three hours a day with clenched fists and pulled legs.',
                    'bn' => 'শিশু পা পেটের দিকে কুঁকড়ে অনবরত কান্নাকাটি করা এবং পেট শক্ত হওয়া (ইনফ্যান্টাইল কলিক)।'
                ]
            ],
        ];

        $seededSymptoms = [];
        foreach ($symptomsData as $s) {
            $bp = $bodyParts[$s['body_part']] ?? null;
            if (!$bp) continue;

            $sym = Symptom::updateOrCreate(
                ['slug' => $s['slug']],
                [
                    'body_part_id' => $bp->id,
                    'name' => $s['name'],
                    'first_letter' => $s['first_letter'],
                    'is_emergency' => $s['is_emergency'],
                    'is_featured' => $s['is_featured'],
                    'age_group' => $s['age_group'],
                    'urgency_level' => $s['is_emergency'] ? 'emergency' : 'routine',
                    'gender_scope' => 'all',
                    'description' => $s['description'],
                ]
            );
            $seededSymptoms[$s['slug']] = $sym;
        }

        // ─────────────────────────────────────────────────────────────
        // 4. Multi-Step Related Factor Questions (Mayo Clinic Step 3)
        // ─────────────────────────────────────────────────────────────
        $factorsData = [
            // Headache Factors
            'headache' => [
                [
                    'question' => [
                        'en' => 'What describes the nature or location of your headache?',
                        'bn' => 'আপনার মাথাব্যথার ধরন বা স্থানটি কেমন?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'h_throb_one_side', 'label' => ['en' => 'Throbbing or pulsing on one side of head', 'bn' => 'মাথার একপাশে তীব্র দপদপ করা ব্যথা'], 'is_red_flag' => false, 'condition' => 'migraine'],
                        ['id' => 'h_tight_band', 'label' => ['en' => 'Dull ache like a tight band around head', 'bn' => 'মাথার চারপাশে টানটান ফিতার মতো চেপে ধরা ব্যথা'], 'is_red_flag' => false, 'condition' => 'hypertension'],
                        ['id' => 'h_face_pressure', 'label' => ['en' => 'Pressure around forehead, cheeks, and eyes', 'bn' => 'কপাল, গাল ও চোখের চারপাশে ভারী চাপ'], 'is_red_flag' => false, 'condition' => 'sinusitis'],
                        ['id' => 'h_sudden_thunderclap', 'label' => ['en' => 'Sudden worst headache of your life (Thunderclap)', 'bn' => 'হঠাৎ জীবনে সবচেয়ে তীব্র অসহ্য মাথাব্যথা'], 'is_red_flag' => true, 'condition' => 'stroke'],
                    ]
                ],
                [
                    'question' => [
                        'en' => 'What other symptoms accompany your headache?',
                        'bn' => 'মাথাব্যথার সাথে অন্য কোন লক্ষণগুলো উপস্থিত আছে?'
                    ],
                    'order_index' => 2,
                    'options' => [
                        ['id' => 'h_nausea_light', 'label' => ['en' => 'Nausea, vomiting, and light or sound sensitivity', 'bn' => 'বমি বমি ভাব এবং আলো বা শব্দে অস্বস্তি'], 'is_red_flag' => false, 'condition' => 'migraine'],
                        ['id' => 'h_stiff_neck_fever', 'label' => ['en' => 'Stiff neck and high fever (cannot touch chin to chest)', 'bn' => 'ঘাড় শক্ত হয়ে যাওয়া ও উচ্চ জ্বর'], 'is_red_flag' => true, 'condition' => 'meningitis'],
                        ['id' => 'h_eye_pain_dengue', 'label' => ['en' => 'Severe pain behind the eyes and body ache', 'bn' => 'চোখের পেছনে ব্যথা ও প্রচণ্ড শরীর ব্যথা'], 'is_red_flag' => false, 'condition' => 'dengue-fever'],
                    ]
                ]
            ],

            // Chest Pain Factors
            'chest-pain' => [
                [
                    'question' => [
                        'en' => 'How does the chest discomfort feel?',
                        'bn' => 'বুকে অস্বস্তি বা ব্যথার তীব্রতা ও ধরন কেমন?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'cp_crushing_heavy', 'label' => ['en' => 'Crushing, squeezing heaviness or burning pressure', 'bn' => 'বুকে ভারী পাথর চাপা দেওয়া বা নিংড়ানোর মতো অনুভূতি'], 'is_red_flag' => true, 'condition' => 'heart-attack'],
                        ['id' => 'cp_burning_reflux', 'label' => ['en' => 'Burning sensation moving up toward throat after meals', 'bn' => 'খাওয়ার পর বুক জ্বালাপোড়া ও গলার দিকে টক পানি আসা'], 'is_red_flag' => false, 'condition' => 'acid-reflux-gerd'],
                        ['id' => 'cp_sharp_breathing', 'label' => ['en' => 'Sharp stabbing pain worse with deep breaths or coughing', 'bn' => 'গভীর নিঃশ্বাস নিলে বা কাশলে তীক্ষ্ণ ছুরির মতো ব্যথা'], 'is_red_flag' => false, 'condition' => 'pneumonia'],
                    ]
                ],
                [
                    'question' => [
                        'en' => 'Does the pain spread to other areas?',
                        'bn' => 'ব্যথা কি শরীরের অন্য কোথাও ছড়িয়ে পড়ে?'
                    ],
                    'order_index' => 2,
                    'options' => [
                        ['id' => 'cp_rad_arm_jaw', 'label' => ['en' => 'Radiates to left arm, shoulder, back, or jaw', 'bn' => 'বাম হাত, কাঁধ, পিঠ বা চোয়ালে ছড়িয়ে পড়ে'], 'is_red_flag' => true, 'condition' => 'heart-attack'],
                        ['id' => 'cp_local_chest', 'label' => ['en' => 'Remains strictly in center of chest', 'bn' => 'শুধুমাত্র বুকের মাঝখানে সীমাবদ্ধ থাকে'], 'is_red_flag' => false, 'condition' => 'angina-pectoris'],
                    ]
                ]
            ],

            // Abdominal Pain Factors
            'abdominal-pain' => [
                [
                    'question' => [
                        'en' => 'Where is the abdominal pain located?',
                        'bn' => 'পেটের কোন অংশে ব্যথার তীব্রতা সবচেয়ে বেশি?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'ap_lower_right', 'label' => ['en' => 'Sharp pain in lower right side of belly', 'bn' => 'তলপেটের ডানদিকে তীব্র ছুরিকাহত ব্যথা'], 'is_red_flag' => true, 'condition' => 'appendicitis'],
                        ['id' => 'ap_upper_right', 'label' => ['en' => 'Upper right abdomen under ribs, especially after fatty food', 'bn' => 'ডানদিকের পাঁজরের নিচে চর্বিযুক্ত খাবার খাওয়ার পর ব্যথা'], 'is_red_flag' => false, 'condition' => 'gallstones-cholelithiasis'],
                        ['id' => 'ap_upper_burning', 'label' => ['en' => 'Upper center burning pain on an empty stomach', 'bn' => 'পেটের উপরের অংশে খালি পেটে জ্বালাপোড়া ব্যথা'], 'is_red_flag' => false, 'condition' => 'peptic-ulcer-disease'],
                        ['id' => 'ap_crampy_lower', 'label' => ['en' => 'Cramping pain relieved after passing bowel motion', 'bn' => 'মোচড় দিয়ে ব্যথা যা মলত্যাগের পর কিছুটা কমে'], 'is_red_flag' => false, 'condition' => 'irritable-bowel-syndrome-ibs'],
                    ]
                ],
                [
                    'question' => [
                        'en' => 'Are there any of these warning signs present?',
                        'bn' => 'সাথে নিচের কোনো বিপজ্জনক লক্ষণ দেখা দিচ্ছে কি?'
                    ],
                    'order_index' => 2,
                    'options' => [
                        ['id' => 'ap_vomit_fever', 'label' => ['en' => 'Persistent vomiting, high fever, or rigid belly', 'bn' => 'অবিরাম বমি, উচ্চ জ্বর বা পেট কাঠের মতো শক্ত হয়ে যাওয়া'], 'is_red_flag' => true, 'condition' => 'appendicitis'],
                        ['id' => 'ap_loose_stools', 'label' => ['en' => 'Frequent watery bowel movements', 'bn' => 'ঘনঘন পাতলা পায়খানা বা ডায়রিয়া'], 'is_red_flag' => false, 'condition' => 'diarrhea'],
                    ]
                ]
            ],

            // Persistent Cough Factors
            'persistent-cough' => [
                [
                    'question' => [
                        'en' => 'What is the characteristic of your cough?',
                        'bn' => 'আপনার কাশির ধরন ও স্থায়িত্ব কেমন?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'c_wheeze_chest', 'label' => ['en' => 'Dry cough with wheezing and chest tightness', 'bn' => 'বুকে সাঁই সাঁই শব্দ ও টানসহ শুকনো কাশি'], 'is_red_flag' => false, 'condition' => 'asthma'],
                        ['id' => 'c_yellow_phlegm', 'label' => ['en' => 'Productive cough with thick yellow or greenish phlegm', 'bn' => 'ঘন হলুদ বা সবুজ কফযুক্ত কাশি'], 'is_red_flag' => false, 'condition' => 'bronchitis'],
                        ['id' => 'c_blood_3weeks', 'label' => ['en' => 'Cough lasting >3 weeks or coughing up traces of blood', 'bn' => '৩ সপ্তাহের বেশি কাশি অথবা কাশির সাথে রক্ত যাওয়া'], 'is_red_flag' => true, 'condition' => 'tuberculosis-tb'],
                    ]
                ]
            ],

            // High Fever Factors
            'high-fever' => [
                [
                    'question' => [
                        'en' => 'What pattern does your fever follow?',
                        'bn' => 'আপনার জ্বরের ধরন ও তীব্রতা কেমন?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'f_breakbone_dengue', 'label' => ['en' => 'Sudden 103°F+ with severe eye pain and joint aches', 'bn' => 'হঠাৎ ১০৩°+ তীব্র জ্বর, চোখের পেছনে ব্যথা ও হাড়ে তীব্র যন্ত্রণা'], 'is_red_flag' => false, 'condition' => 'dengue-fever'],
                        ['id' => 'f_stepladder_typhoid', 'label' => ['en' => 'Gradual step-ladder fever rising daily with stomach pain', 'bn' => 'প্রতিদিন ধাপে ধাপে বৃদ্ধি পাওয়া জ্বর ও পেটে অস্বস্তি'], 'is_red_flag' => false, 'condition' => 'typhoid-fever'],
                        ['id' => 'f_chills_rigor', 'label' => ['en' => 'Intermittent spiking fever with intense shaking chills', 'bn' => 'কাঁপুনি দিয়ে তীব্র জ্বর আসা এবং ঘাম দিয়ে ছেড়ে দেওয়া'], 'is_red_flag' => false, 'condition' => 'malaria'],
                    ]
                ]
            ],

            // Child High Fever Factors
            'child-high-fever-lethargy' => [
                [
                    'question' => [
                        'en' => 'How is the child responding and breathing?',
                        'bn' => 'শিশুর সক্রিয়তা ও শ্বাসপ্রশ্বাস কেমন?'
                    ],
                    'order_index' => 1,
                    'options' => [
                        ['id' => 'cf_unresponsive', 'label' => ['en' => 'Child is extremely limp, drowsy, or won\'t wake up', 'bn' => 'শিশু অস্বাভাবিক নিস্তেজ, চোখ খুলতে পারছে না বা অজ্ঞানের মতো'], 'is_red_flag' => true, 'condition' => 'pneumonia'],
                        ['id' => 'cf_rapid_chest_indraw', 'label' => ['en' => 'Breathing very fast with chest pulling inwards', 'bn' => 'খুব দ্রুত শ্বাস নিচ্ছে ও শ্বাস নেওয়ার সময় বুক দেবে যাচ্ছে'], 'is_red_flag' => true, 'condition' => 'pneumonia'],
                        ['id' => 'cf_rash_spots', 'label' => ['en' => 'Fever accompanied by sudden red skin rash or spots', 'bn' => 'জ্বরের সাথে সারা শরীরে লালচে ফুসকুড়ি বা র্যাশ দেখা দেওয়া'], 'is_red_flag' => false, 'condition' => 'measles'],
                    ]
                ]
            ],
        ];

        foreach ($factorsData as $symptomSlug => $questions) {
            $sym = $seededSymptoms[$symptomSlug] ?? null;
            if (!$sym) continue;

            foreach ($questions as $q) {
                SymptomFactor::updateOrCreate(
                    [
                        'symptom_id' => $sym->id,
                        'question->en' => $q['question']['en'],
                    ],
                    [
                        'factor_type' => $q['factor_type'] ?? 'quality',
                        'question' => $q['question'],
                        'order_index' => $q['order_index'],
                        'options' => $q['options'],
                    ]
                );
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 5. Connect Symptoms with Diseases (Disease-Symptom Pivot)
        // ─────────────────────────────────────────────────────────────
        $links = [
            'migraine' => ['headache', 'nausea-vomiting', 'dizziness-vertigo'],
            'hypertension' => ['headache', 'dizziness-vertigo', 'palpitations'],
            'dengue-fever' => ['high-fever', 'headache', 'unexplained-fatigue'],
            'asthma' => ['shortness-of-breath', 'wheezing', 'persistent-cough'],
            'pneumonia' => ['shortness-of-breath', 'high-fever', 'persistent-cough', 'chest-pain'],
            'heart-attack' => ['chest-pain', 'shortness-of-breath', 'palpitations'],
            'appendicitis' => ['severe-lower-right-pain', 'abdominal-pain', 'nausea-vomiting'],
            'diarrhea' => ['diarrhea-loose-stools', 'abdominal-pain', 'unexplained-fatigue'],
            'stroke' => ['facial-numbness-weakness', 'sudden-severe-headache', 'dizziness-vertigo'],
            'typhoid-fever' => ['high-fever', 'abdominal-pain', 'unexplained-fatigue'],
            'tuberculosis-tb' => ['persistent-cough', 'high-fever', 'unexplained-weight-loss'],
            'acid-reflux-gerd' => ['heartburn-acidity', 'chest-pain', 'nausea-vomiting'],
            'sinusitis' => ['headache', 'nasal-congestion', 'facial-numbness-weakness'],
            'urinary-tract-infection-uti' => ['painful-urination', 'frequent-urination', 'high-fever'],
            'osteoarthritis' => ['joint-pain-swelling'],
            'sciatica' => ['back-pain-lumbar'],
            'measles' => ['high-fever', 'skin-rash-itching', 'persistent-cough'],
        ];

        foreach ($links as $diseaseSlug => $symSlugs) {
            $disease = $seededDiseases[$diseaseSlug] ?? null;
            if (!$disease) continue;

            $symIds = [];
            foreach ($symSlugs as $ss) {
                if (isset($seededSymptoms[$ss])) {
                    $symIds[] = $seededSymptoms[$ss]->id;
                }
            }
            if (!empty($symIds)) {
                $disease->symptoms()->syncWithoutDetaching($symIds);
            }
        }

        echo "Successfully seeded " . count($seededDiseases) . " diseases spanning A to Z, along with Mayo Clinic symptoms and factor questions.\n";
    }
}
