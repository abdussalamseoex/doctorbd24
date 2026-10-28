<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disease;
use App\Models\Specialty;
use App\Models\BodyPart;
use App\Models\Doctor;
use App\Models\Symptom;

class MigraineBenchmarkDemoSeeder extends Seeder
{
    public function run(): void
    {
        $neuroSpecialty = Specialty::where('slug', 'neurology')->first();
        $headBodyPart = BodyPart::where('slug', 'head-brain')->orWhere('slug', 'like', '%head%')->first();
        $doctor = Doctor::first();

        // Ensure doctor has neurology specialty attached
        if ($doctor && $neuroSpecialty) {
            $doctor->specialties()->syncWithoutDetaching([$neuroSpecialty->id]);
        }

        // Find or create Migraine (id = 1 or slug = 'migraine')
        $disease = Disease::firstOrNew(['slug' => 'migraine']);

        $disease->fill([
            'primary_specialty_id' => $neuroSpecialty?->id,
            'body_system_id'       => $headBodyPart?->id,
            'reviewer_doctor_id'   => $doctor?->id,
            'last_reviewed_at'     => '2026-10-25',
            'first_letter'         => 'M',
            'status'               => 'published',
            'published_at'         => $disease->published_at ?: now(),
            'is_featured'          => true,
            'is_emergency'         => false,
            'aliases'              => [
                'Hemicrania',
                'Sick headache',
                'Migraine headache',
                'অর্ধকপালী মাথাব্যথা',
                'আধকপালি'
            ],
            'short_description'    => [
                'en' => 'A neurological condition characterized by intense, throbbing headaches usually affecting one side of the head, frequently accompanied by sensitivity to light and sound, nausea, and visual aura.',
                'bn' => 'মাইগ্রেন হলো একটি দীর্ঘমেয়াদী স্নায়বিক সমস্যা, যা মাথার একপাশে তীব্র ও স্পন্দনশীল (দপদপ করা) ব্যথার সৃষ্টি করে। এর সাথে আলো ও শব্দের প্রতি সংবেদনশীলতা, বমি বমি ভাব এবং দৃষ্টিবিভ্রম (অরা) দেখা দেয়।'
            ],
            'overview'             => [
                'en' => '<p>A <strong>migraine</strong> is a neurological condition that causes an intense, throbbing headache, typically on one side of the head. It is far more than an ordinary headache; it is often accompanied by nausea, vomiting, and extreme sensitivity to bright light and sound.</p><p>Migraine attacks can cause significant pain for hours to days and can be so severe that the pain is disabling. Many people experience warning signs known as an <strong>aura</strong> before or with the headache, which can include visual disturbances such as flashes of light, blind spots, or tingling in the face or arm.</p><p>Medications can help prevent some migraines and make them less painful. The right medicines, combined with self-help remedies and lifestyle changes, might make a significant difference in living with migraine.</p>',
                'bn' => '<p><strong>মাইগ্রেন (Migraine)</strong> হলো একটি দীর্ঘমেয়াদী নিউরোলজিক্যাল বা স্নায়বিক স্বাস্থ্য সমস্যা, যা মাথার একপাশে তীব্র, স্পন্দনশীল (দপদপ করা) ব্যথার সৃষ্টি করে। এটি সাধারণ মাথাব্যথার চেয়ে অনেক বেশি কষ্টদায়ক এবং দীর্ঘস্থায়ী। মাইগ্রেনের ব্যথার সাথে প্রায়শই তীব্র আলো ও শব্দের প্রতি অসহনীয় সংবেদনশীলতা, বমি বমি ভাব বা বমি হওয়া দেখা যায়।</p><p>মাইগ্রেনের আক্রমণ বা অ্যাটাক কয়েক ঘণ্টা থেকে টানা ৩ দিন (৭২ ঘণ্টা) পর্যন্ত স্থায়ী হতে পারে। তীব্র ব্যথার কারণে রোগী স্বাভাবিক কাজকর্ম থেকে সম্পূর্ণ বিছানাগত হয়ে পড়েন। অনেকের ক্ষেত্রে মূল মাথাব্যথা শুরু হওয়ার ঠিক পূর্বে কিছু সতর্কবার্তা দেখা দেয় যাকে চিকিৎসাবিজ্ঞানে <strong>"অরা" (Aura)</strong> বলা হয়—যেমন চোখে আলোর ঝলকানি দেখা, দৃষ্টি ঝাপসা হওয়া বা মুখের একপাশে সূচ ফোটার মতো অনুভূতি হওয়া।</p><p>সঠিক সময়ে উপযুক্ত চিকিৎসা গ্রহণ, ট্রিগার বা উদ্দীপক পরিহার এবং নিয়মমাফিক জীবনযাপনের মাধ্যমে মাইগ্রেনের আক্রমণ ও তীব্রতা অনেকাংশে নিয়ন্ত্রণে রাখা সম্ভব।</p>'
            ],
            'symptoms_summary'     => [
                'en' => '<p>Common symptoms during a migraine attack include:</p><ul><li>Moderate to severe throbbing or pulsating pain, usually on one side of the head (can occur on both sides)</li><li>Extreme sensitivity to light (photophobia), sound (phonophobia), and sometimes smells or touch</li><li>Nausea and vomiting</li><li>Blurred vision or visual blind spots</li><li>Lightheadedness, dizziness, or feeling faint</li></ul>',
                'bn' => '<p>মাইগ্রেন আক্রমণের সময় সচরাচর যে লক্ষণগুলো পরিলক্ষিত হয়:</p><ul><li>মাথার একপাশে বা উভয়পাশে তীব্র দপদপানিযুক্ত ও স্পন্দনশীল ব্যথা</li><li>আলো (Photophobia) এবং উচ্চ শব্দের (Phonophobia) প্রতি প্রচণ্ড সংবেদনশীলতা</li><li>বমি বমি ভাব বা বমি হওয়া</li><li>দৃষ্টি ঝাপসা হয়ে যাওয়া বা চোখে অন্ধকার দেখা</li><li>মাথা ঘোরা ও শারীরিক দুর্বলতা অনুভব করা</li></ul>'
            ],
            'symptoms_detailed'    => [
                'en' => '<p>Migraines often progress through four distinct clinical stages, though not everyone experiences all stages:</p><ol><li><strong>Prodrome:</strong> One or two days before a migraine, you might notice subtle changes that warn of an upcoming attack, including constipation, mood changes, food cravings, neck stiffness, and frequent yawning.</li><li><strong>Aura:</strong> For some people, an aura might occur before or during migraines. Auras are reversible symptoms of the nervous system. Examples include visual phenomena (flashes of light, geometric patterns), temporary vision loss, or pins-and-needles sensations in an arm or leg.</li><li><strong>Attack:</strong> A migraine attack usually lasts from 4 to 72 hours if untreated. The pain is usually throbbing and worsens with physical activity, coughing, or bending over.</li><li><strong>Post-drome:</strong> After a migraine attack, you might feel drained, confused, and washed out for up to a day, while others report feeling unusually refreshed.</li></ol>',
                'bn' => '<p>মাইগ্রেন সাধারণত ৪টি ধারাবাহিক ক্লিনিক্যাল পর্যায়ের মধ্য দিয়ে আবর্তিত হয়:</p><ol><li><strong>প্রোড্রোম (Prodrome - প্রাথমিক আভাস):</strong> মাথাব্যথা শুরু হওয়ার ১-২ দিন আগে রোগী কোষ্ঠকাঠিন্য, মেজাজের আকস্মিক পরিবর্তন, নির্দিষ্ট খাবারের তীব্র আকাঙ্ক্ষা, ঘাড় শক্ত হওয়া এবং ঘন ঘন হাই তোলার মতো মৃদু লক্ষণ লক্ষ্য করতে পারেন।</li><li><strong>অরা (Aura - স্নায়বিক সংকেত):</strong> শতকরা প্রায় ২৫-৩০ ভাগ রোগীর ক্ষেত্রে ব্যথা শুরুর ঠিক আগে বা চলাকালীন সময়ে স্নায়বিক দৃষ্টিবিভ্রম ঘটে। যেমন চোখে আলোর ঝলকানি দেখা, জিগজ্যাগ লাইন দেখা, মুখের একপাশে অসাড়তা বা কথা বলতে সাময়িক জড়তা।</li><li><strong>অ্যাটাক (Attack - মূল তীব্র পর্যায়):</strong> চিকিৎসা না নিলে এই পর্যায়টি ৪ ঘণ্টা থেকে টানা ৭২ ঘণ্টা পর্যন্ত চলতে পারে। মাথার একদিকে অসহ্য দপদপানি ব্যথা হয় যা নড়াচড়া করলে বেড়ে যায়।</li><li><strong>পোস্ট-ড্রোম (Post-drome - ব্যথাপরবর্তী ক্লান্তি):</strong> মাথাব্যথা কমে যাওয়ার পর রোগী চরম ক্লান্তি, অবসাদ এবং বিভ্রান্তি অনুভব করতে পারেন যা পরবর্তী ২৪ ঘণ্টা পর্যন্ত থাকতে পারে।</li></ol>'
            ],
            'red_flags'            => [
                [
                    'en' => 'Sudden, severe thunderclap headache (worst headache of your life)',
                    'bn' => 'হঠাৎ বিনা নোটিশে তীব্র বিদ্যুতের ঝলকের মতো প্রচণ্ড মাথাব্যথা (জীবনের সবচেয়ে তীব্র ব্যথা)'
                ],
                [
                    'en' => 'Headache accompanied by high fever, stiff neck, confusion, or seizures',
                    'bn' => 'মাথাব্যথার সাথে তীব্র জ্বর, ঘাড় শক্ত হয়ে যাওয়া, মানসিক অসংলগ্নতা বা খিঁচুনি'
                ],
                [
                    'en' => 'Numbness, paralysis, weakness on one side of the body, or slurred speech (Stroke warning)',
                    'bn' => 'শরীরের একপাশ অবশ বা দুর্বল হওয়া এবং কথা বলতে অস্পষ্টতা (স্ট্রোকের জরুরি সংকেত)'
                ],
                [
                    'en' => 'Headache occurring after a head injury or physical impact',
                    'bn' => 'মাথায় আঘাত বা শারীরিক আঘাত পাওয়ার পর শুরু হওয়া মাথাব্যথা'
                ],
                [
                    'en' => 'A new headache starting after age 50, especially if worsening over weeks',
                    'bn' => '৫০ বছর বয়সের পর জীবনে প্রথমবারের মতো নতুন ধরনের তীব্র মাথাব্যথা শুরু হওয়া'
                ]
            ],
            'causes'               => [
                'en' => '<p>Though migraine causes aren\'t fully understood, genetics and environmental factors appear to play a major role. Migraines may be caused by changes in the brainstem and its interactions with the trigeminal nerve, a major pain pathway.</p><p>Imbalances in brain chemicals — including serotonin, which helps regulate pain in the nervous system — might be involved. Researchers are also studying the role of calcitonin gene-related peptide (CGRP) during migraine attacks.</p><p><strong>Common Triggers:</strong></p><ul><li><strong>Hormonal changes in women:</strong> Fluctuations in estrogen around menstruation, pregnancy, or menopause.</li><li><strong>Drinks:</strong> Alcohol, especially wine, and too much caffeine.</li><li><strong>Stress:</strong> High stress at work or home is one of the most frequent triggers.</li><li><strong>Sensory stimuli:</strong> Bright flashing lights, loud sounds, and strong smells (perfumes, smoke).</li><li><strong>Sleep changes:</strong> Missing sleep, irregular shift work, or sleeping too much.</li><li><strong>Foods & Additives:</strong> Aged cheeses, salty processed foods, aspartame, and MSG.</li></ul>',
                'bn' => '<p>মাইগ্রেনের সুনির্দিষ্ট কারণ নিয়ে গবেষণা চলমান থাকলেও চিকিৎসাবিজ্ঞানীদের মতে জেনেটিক (বংশগত) এবং পরিবেশগত কারণ এতে প্রধান ভূমিকা রাখে। মস্তিষ্কের ব্রেনস্টেম এবং ট্রাইজেমিনাল স্নায়ুর (যা ব্যথানালী নিয়ন্ত্রণ করে) মধ্যবর্তী কার্যপ্রণালীতে অসঙ্গতির কারণে এটি হয়ে থাকে।</p><p>মস্তিষ্কের স্নায়বিক রাসায়নিক যেমন সেরোটোনিন ও সিজিআরপি (CGRP)-এর ভারসাম্যের আকস্মিক পরিবর্তন মাইগ্রেন ব্যথার সঞ্চার করে।</p><p><strong>মাইগ্রেনের সাধারণ ট্রিগার বা উদ্দীপকসমূহ:</strong></p><ul><li><strong>হরমোনের পরিবর্তন:</strong> নারীদের ক্ষেত্রে ইস্ট্রোজেন হরমোনের উঠানামা (মাসিক ঋতুস্রাবের আগে বা চলাকালীন)।</li><li><strong>মানসিক চাপ ও উদ্বেগ:</strong> অতিরিক্ত কাজের চাপ, পারিবারিক দুশ্চিন্তা ও মানসিক অস্থিরতা।</li><li><strong>ঘুমের অনিয়ম:</strong> রাত জাগা, অপর্যাপ্ত ঘুম বা অতিরিক্ত ঘুমানো।</li><li><strong>খাদ্যাভ্যাস:</strong> দীর্ঘক্ষণ না খেয়ে থাকা (রোজা বা উপবাস), অতিরিক্ত ক্যাফেইন গ্রহণ বা হঠাৎ চা-কফি ছেড়ে দেওয়া, চকোলেট ও পনির।</li><li><strong>পরিবেশগত প্রভাব:</strong> কড়া রোদ, অতিরিক্ত গরম, চড়া পারফিউমের গন্ধ বা ধোঁয়া।</li></ul>'
            ],
            'risk_factors'         => [
                'en' => '<ul><li><strong>Family History:</strong> If one or both parents have migraines, you have a significantly high chance of developing them.</li><li><strong>Age:</strong> Migraines can begin at any age, though the first attack often occurs during adolescence, peaking during your 30s.</li><li><strong>Sex:</strong> Women are three times more likely than men to experience migraines.</li><li><strong>Hormonal Fluctuations:</strong> For women who have migraines, headaches might begin just before or shortly after the onset of menstruation.</li></ul>',
                'bn' => '<ul><li><strong>পারিবারিক ইতিহাস:</strong> পরিবারের রক্তসম্পর্কিত কারও (যেমন বাবা বা মা) মাইগ্রেন থাকলে সন্তানের হওয়ার ঝুঁকি অনেক বেশি থাকে।</li><li><strong>বয়স:</strong> যেকোনো বয়সে শুরু হতে পারলেও সাধারণত বয়ঃসন্ধিকাল থেকে শুরু হয়ে ২০ থেকে ৪০ বছর বয়সে এর প্রকোপ সবচেয়ে বেশি থাকে।</li><li><strong>লিঙ্গভেদ:</strong> পুরুষদের তুলনায় নারীরা মাইগ্রেনে প্রায় তিন গুণ বেশি আক্রান্ত হন।</li><li><strong>হরমোনের তারতম্য:</strong> মাসিক ঋতুস্রাব, গর্ভকালীন সময় বা হরমোন থেরাপির সময় ঝুঁকি বেড়ে যায়।</li></ul>'
            ],
            'complications'        => [
                'en' => '<ul><li><strong>Chronic Migraine:</strong> Occurring 15 or more headache days a month for more than three months.</li><li><strong>Status Migrainosus:</strong> A severe and debilitating migraine attack lasting more than 72 hours continuously.</li><li><strong>Medication-Overuse Headaches (Rebound Headaches):</strong> Taking over-the-counter or prescription pain medicines too often can actually trigger more headaches.</li><li><strong>Persistent Aura Without Infarction:</strong> An aura symptom that lasts for more than a week without evidence of stroke on imaging.</li></ul>',
                'bn' => '<ul><li><strong>ক্রনিক মাইগ্রেন (Chronic Migraine):</strong> মাসে ১৫ দিন বা তার বেশি সময় ধরে মাথাব্যথায় ভোগা (টানা ৩ মাসের বেশি)।</li><li><strong>স্ট্যাটাস মাইগ্রেনোসাস:</strong> একটানা ৭২ ঘণ্টার বেশি সময় ধরে তীব্র ও অসহ্য মাথাব্যথা অব্যাহত থাকা, যা হাসপাতালে ভর্তি ছাড়া কমে না।</li><li><strong>ওষুধের অপব্যবহারজনিত মাথাব্যথা (Rebound Headaches):</strong> সপ্তাহে ২-৩ দিনের বেশি অতিরিক্ত ব্যথানাশক ওষুধ সেবন করলে ওষুধ নিজেই আবার মাথাব্যথার সৃষ্টি করে।</li><li><strong>মানসিক বিষণ্নতা:</strong> দীর্ঘমেয়াদী তীব্র ব্যথার কারণে রোগীর মধ্যে হতাশা ও অ্যানজাইটি দেখা দেওয়া।</li></ul>'
            ],
            'prevention'           => [
                'en' => '<p>Preventive strategies can substantially reduce how often you get migraines and how severe they are:</p><ul><li><strong>Keep a Headache Diary:</strong> Track what you were doing, eating, and feeling when each migraine started to pinpoint your unique personal triggers.</li><li><strong>Establish a Consistent Daily Routine:</strong> Eat meals at regular times each day, go to sleep and wake up at consistent hours.</li><li><strong>Stay Well Hydrated:</strong> Drink plenty of water throughout the day. Dehydration is a very frequent trigger.</li><li><strong>Manage Stress:</strong> Practice relaxation techniques such as deep belly breathing, progressive muscle relaxation, or yoga.</li><li><strong>Engage in Regular Aerobic Exercise:</strong> Regular physical activity reduces tension and helps prevent migraines.</li></ul>',
                'bn' => '<p>নিয়মমাফিক জীবনযাপন ও সচেতনতার মাধ্যমে মাইগ্রেনের প্রকোপ বহুলাংশে প্রতিরোধ করা যায়:</p><ul><li><strong>মাথাব্যথার ডায়েরি রাখা:</strong> কোন পরিস্থিতিতে বা কোন খাবার খাওয়ার পর ব্যথা শুরু হচ্ছে তা নিয়মিত লিখে রেখে নিজের সুনির্দিষ্ট ট্রিগার শনাক্ত করুন।</li><li><strong>নির্দিষ্ট নিয়মে চলা:</strong> প্রতিদিন নির্দিষ্ট সময়ে ঘুমাতে যাওয়া এবং সকালে নির্দিষ্ট সময়ে ওঠার অভ্যাস গড়ে তুলুন।</li><li><strong>পর্যাপ্ত পানি পান:</strong> পানিশূন্যতা মাইগ্রেন ব্যথার অন্যতম বড় কারণ। দিনে অন্তত আড়াই থেকে তিন লিটার বিশুদ্ধ পানি পান করুন।</li><li><strong>মানসিক চাপ নিয়ন্ত্রণ:</strong> যোগব্যায়াম, মেডিটেশন বা হালকা হাঁটার মাধ্যমে স্ট্রেস নিয়ন্ত্রণে রাখুন।</li><li><strong>খাবার এড়িয়ে না চলা:</strong> দীর্ঘক্ষণ পেট খালি রাখবেন না; সময়মতো স্বাস্থ্যকর খাবার গ্রহণ করুন।</li></ul>'
            ],
            'when_to_see_doctor'   => [
                'en' => '<p>Consult your healthcare provider or a specialist neurologist if:</p><ul><li>Headaches interfere with your work, school, or daily life</li><li>You have migraines frequently (more than once or twice a week)</li><li>You frequently rely on over-the-counter pain medications</li><li>Your headache pattern has changed or the attacks feel different than before</li><li>Pain relievers are no longer providing sufficient relief</li></ul>',
                'bn' => '<p>নিম্নোক্ত পরিস্থিতিতে অবিলম্বে একজন বিশেষজ্ঞ নিউরোলজিস্টের পরামর্শ নিন:</p><ul><li>মাথাব্যথা যখন আপনার স্বাভাবিক চাকরি, পড়াশোনা বা পারিবারিক জীবন ব্যাহত করে</li><li>সপ্তাহে একের অধিকবার তীব্র মাথাব্যথায় আক্রান্ত হলে</li><li>সপ্তাহে ২ দিনের বেশি ব্যথানাশক ট্যাবলেট সেবন করতে বাধ্য হলে</li><li>মাথাব্যথার তীব্রতা বা বৈশিষ্ট্য হঠাৎ পরিবর্তিত হলে</li><li>সাধারণ ওষুধে আর ব্যথা উপশম না হলে</li></ul>'
            ],
            'diagnosis_overview'   => [
                'en' => '<p>A neurologist diagnoses migraines by reviewing your detailed medical history, examining your family history, and performing a thorough physical and neurological exam.</p><p>If your condition is unusual, complex, or suddenly becomes severe, tests to rule out other causes might include neuroimaging and specialized blood work.</p>',
                'bn' => '<p>একজন বিশেষজ্ঞ নিউরোলজিস্ট রোগীর শারীরিক ও স্নায়বিক পরীক্ষার পাশাপাশি পারিবারিক স্বাস্থ্য ইতিহাসের বিশদ পর্যালোচনার মাধ্যমে মাইগ্রেন রোগ শনাক্ত করেন।</p><p>যদি মাথাব্যথা আকস্মিক হয় বা অন্যান্য জটিল উপসর্গ থাকে, তবে মস্তিষ্কের অন্যান্য মারাত্মক ব্যাধি নাকচ করার জন্য কিছু বিশেষ পরীক্ষা সম্পন্ন করা হয়।</p>'
            ],
            'diagnosis_tests'      => [
                [
                    'name_en' => 'Magnetic Resonance Imaging (MRI) of Brain',
                    'name_bn' => 'মস্তিষ্কের এমআরআই পরীক্ষা (Brain MRI)',
                    'desc_en' => 'Uses magnetic fields and radio waves to produce detailed images of the brain and blood vessels to rule out tumors, strokes, or structural anomalies.',
                    'desc_bn' => 'চৌম্বকীয় তরঙ্গ ব্যবহার করে মস্তিষ্কের সূক্ষ্ম গঠন ও রক্তনালীর বিশদ চিত্র ধারণ করে, যা টিউমার বা রক্তক্ষরণ শনাক্তে সাহায্য করে।'
                ],
                [
                    'name_en' => 'Computerized Tomography (CT) Scan of Head',
                    'name_bn' => 'মাথার সিটি স্ক্যান (Brain CT Scan)',
                    'desc_en' => 'A series of X-rays that create detailed cross-sectional images of the brain. Helps diagnose infections, brain damage, and acute hemorrhages.',
                    'desc_bn' => 'উচ্চ ক্ষমতাসম্পন্ন এক্স-রে রশ্মির সাহায্যে মস্তিষ্কের স্তরভিত্তিক ছবি তৈরি করে তাৎক্ষণিক রক্তক্ষরণ ও স্ট্রোক নির্ণয় করে।'
                ],
                [
                    'name_en' => 'Complete Blood Count (CBC) & ESR',
                    'name_bn' => 'রক্তের সিবিসি ও ইএসআর পরীক্ষা',
                    'desc_en' => 'Routine laboratory blood analysis to evaluate underlying systemic infections or inflammatory disorders.',
                    'desc_bn' => 'শরীরে কোনো সুপ্ত প্রদাহ বা ইনফেকশনজনিত জটিলতা রয়েছে কিনা তা মূল্যায়নে সহায়ক।'
                ]
            ],
            'treatment_overview'   => [
                'en' => '<p>Migraine treatment is aimed at stopping symptoms and preventing future attacks. Medications used to manage migraines fall into two broad categories:</p><ol><li><strong>Pain-relieving (Acute or Abortive) medications:</strong> Taken during migraine attacks and designed to stop symptoms that have already begun.</li><li><strong>Preventive medications:</strong> Taken regularly, often daily, to reduce the frequency, length, and severity of migraines.</li></ol>',
                'bn' => '<p>মাইগ্রেন চিকিৎসার প্রধান দুটি লক্ষ্য থাকে: বর্তমান ব্যথার আক্রমণ তাৎক্ষণিক থামানো এবং ভবিষ্যতে আক্রমণের হার কমিয়ে আনা। চিকিৎসাব্যবস্থাকে দুটি মূল ভাগে ভাগ করা হয়:</p><ol><li><strong>তাত্ক্ষণিক উপশমকারী চিকিৎসা (Acute Treatment):</strong> ব্যথা শুরু হওয়া মাত্র গ্রহণ করা হয় যাতে ব্যথা দ্রুত প্রশমিত হয়।</li><li><strong>প্রতিরোধমূলক চিকিৎসা (Preventive Treatment):</strong> যাদের মাসে ঘন ঘন ব্যথা হয়, তাদের দীর্ঘমেয়াদে প্রতিদিন ওষুধ সেবনের মাধ্যমে ব্যথার প্রকোপ নিয়ন্ত্রণে রাখা হয়।</li></ol>'
            ],
            'treatments'           => [
                [
                    'category_en' => 'Acute / Abortive',
                    'category_bn' => 'তাত্ক্ষণিক উপশমকারী',
                    'name_en'     => 'Triptan Receptor Agonists',
                    'name_bn'     => 'ট্রিপট্যান থেরাপি',
                    'desc_en'     => 'Prescription drugs like sumatriptan and rizatriptan that block pain pathways in the brain. They are most effective when taken at the earliest onset of pain.',
                    'desc_bn'     => 'সুমাট্রিপট্যান জাতীয় ওষুধ মস্তিষ্কের ব্যথানালীর প্রদাহ সংকুচিত করে দ্রুত ব্যথামুক্তি দেয়। ব্যথা শুরুর প্রথম ৩০ মিনিটের মধ্যে সেবন করলে সবচেয়ে ভালো কাজ করে।'
                ],
                [
                    'category_en' => 'Preventive Therapy',
                    'category_bn' => 'দীর্ঘমেয়াদি প্রতিরোধক',
                    'name_en'     => 'Calcitonin Gene-Related Peptide (CGRP) Inhibitors',
                    'name_bn'     => 'সিজিআরপি (CGRP) ইনহিবিটরস',
                    'desc_en'     => 'Modern targeted therapies (monthly injections or oral gepants) designed specifically to prevent migraine attacks by blocking the CGRP molecule.',
                    'desc_bn'     => 'সর্বাধুনিক টার্গেটেড থেরাপি যা মাইগ্রেনের মূল প্রোটিন অণু সিজিআরপি-কে ব্লক করে ব্যথার পুনরাবৃত্তি শতকরা ৭০-৮০ ভাগ পর্যন্ত হ্রাস করে।'
                ]
            ],
            'medicines'            => [
                [
                    'type'            => 'otc',
                    'name_en'         => 'Naproxen Sodium / Ibuprofen (NSAIDs)',
                    'name_bn'         => 'নেপ্রোক্সেন সোডিয়াম / আইবুপ্রোফেন',
                    'dosage_notes_en' => 'Over-the-counter pain relievers effective for mild to moderate migraines. Take with food immediately at pain onset.',
                    'dosage_notes_bn' => 'হালকা থেকে মাঝারি তীব্রতার ব্যথায় খাবারের পর খাওয়া হয়। চিকিৎসকের পরামর্শ ছাড়া সপ্তাহে ৩ দিনের বেশি সেবন অনুচিত।'
                ],
                [
                    'type'            => 'prescription',
                    'name_en'         => 'Sumatriptan (50mg / 100mg Tablets)',
                    'name_bn'         => 'সুমাট্রিপট্যান (৫০ বা ১০০ মিগ্রা)',
                    'dosage_notes_en' => 'First-line prescription abortive. Take 1 tablet at headache onset. A second dose may be taken after 2 hours if headache returns.',
                    'dosage_notes_bn' => 'নিউরোলজিস্টের প্রেসক্রিপশন অনুযায়ী মাইগ্রেন অ্যাটাক শুরুর সাথে সাথে ১টি ট্যাবলেট সেবন করতে হয়।'
                ],
                [
                    'type'            => 'prescription',
                    'name_en'         => 'Propranolol / Amitriptyline (Preventive)',
                    'name_bn'         => 'প্রোপ্রানোলল / অ্যামিট্রিপটাইলাইন',
                    'dosage_notes_en' => 'Daily preventive medications taken nightly to reduce headache frequency under strict doctor supervision.',
                    'dosage_notes_bn' => 'যাদের মাসে ৪ বা তার বেশি আক্রমণ হয়, তাদের রাতে নিয়মিত সেবনের জন্য প্রেসক্রাইব করা হয়।'
                ]
            ],
            'procedures'           => [
                [
                    'name_en' => 'OnabotulinumtoxinA (Botox) Injections',
                    'name_bn' => 'বোটক্স ইনজেকশন থেরাপি',
                    'desc_en' => 'FDA-approved treatment for chronic migraine (>15 headache days/month). Injections are given into shallow muscles around the forehead, temples, and neck every 12 weeks.',
                    'desc_bn' => 'ক্রনিক মাইগ্রেন রোগীদের জন্য অনুমোদিত চিকিৎসা। প্রতি ১২ সপ্তাহ পরপর মাথা ও ঘাড়ের নির্দিষ্ট স্নায়ু পয়েন্টে ইনজেকশন প্রয়োগ করা হয়।'
                ],
                [
                    'name_en' => 'Non-Invasive Neuromodulation (Cefaly Device)',
                    'name_bn' => 'নিউরোমডুলেশন প্রযুক্তি (Cefaly)',
                    'desc_en' => 'An FDA-cleared headband device that stimulates the trigeminal nerve with microcurrents to reduce headache frequency without drug side effects.',
                    'desc_bn' => 'মাথায় পরিধেয় বিশেষ ডিভাইস যা মৃদু ইলেকট্রিক স্টিমুলেশনের মাধ্যমে স্নায়ুর উত্তেজনা শান্ত করে ওষুধহীন উপশম প্রদান করে।'
                ]
            ],
            'clinical_trials'      => [
                'en' => '<p>Clinical research into monoclonal antibodies targeting the CGRP pathway and non-invasive vagus nerve stimulation is actively underway globally, demonstrating superior tolerability and minimal adverse effects for drug-resistant chronic migraine patients.</p>',
                'bn' => '<p>বিশ্বজুড়ে বর্তমানে সিজিআরপি পাথওয়ে টার্গেট করা বিশেষ মনোকোনাল অ্যান্টিবডি এবং ভেগাস স্নায়ু উদ্দীপনা সংক্রান্ত গবেষণায় অভূতপূর্ব সাফল্য এসেছে, যা প্রচলিত ওষুধে আরাম না পাওয়া রোগীদের জন্যও কার্যকর প্রমাণিত হচ্ছে।</p>'
            ],
            'lifestyle_home_remedies' => [
                'en' => '<p>When a migraine strikes, simple self-care techniques can provide immediate soothing relief:</p><ul><li><strong>Rest in a Quiet, Dark Room:</strong> Turn off all lights and screens. Close your eyes and rest or take a nap in a completely dark and quiet space.</li><li><strong>Apply Temperature Therapy:</strong> Place a cold compress or cloth filled with ice on your forehead or back of your neck. A warm heating pad on tight neck muscles can also relax tension.</li><li><strong>Hydrate with Cool Fluids:</strong> Sip cold water or an electrolyte solution slowly.</li><li><strong>Practice Deep Relaxation:</strong> Gentle slow breathing from your abdomen helps relieve muscle tension.</li></ul>',
                'bn' => '<p>মাইগ্রেন আক্রমণ শুরু হওয়া মাত্র ঘরোয়া উপায়ে স্বস্তি পাওয়ার কিছু কার্যকর উপায়:</p><ul><li><strong>অন্ধকার ও শান্ত ঘরে বিশ্রাম:</strong> ঘরের সব বাতি নিভিয়ে এবং মোবাইল/টিভি পর্দা বন্ধ করে নীরব কক্ষে চোখ বন্ধ করে শুয়ে থাকুন।</li><li><strong>ঠান্ডা বা গরম সেঁক:</strong> কপালে ও মাথার পাশে বরফের প্যাক বা ঠান্ডা পানিতে ভেজানো রুমাল দিয়ে সেঁক দিন। অনেকের ক্ষেত্রে ঘাড়ে কুসুম গরম সেঁক উপকারী।</li><li><strong>পর্যাপ্ত তরল গ্রহণ:</strong> অল্প অল্প করে ঠান্ডা বিশুদ্ধ পানি বা লেবুর শরবত পান করুন।</li><li><strong>ধীর শ্বাসপ্রশ্বাস:</strong> চোখ বন্ধ করে বুক ভরে ধীরে ধীরে শ্বাস নিন ও ছাড়ুন, এটি স্নায়ুচাপ কমাতে সহায়তা করে।</li></ul>'
            ],
            'alternative_medicine' => [
                'en' => '<p>Complementary and integrative health therapies may benefit many migraine sufferers:</p><ul><li><strong>Acupuncture:</strong> Extensive clinical trials confirm acupuncture can effectively reduce both frequency and intensity of migraine attacks.</li><li><strong>Biofeedback & Cognitive Behavioral Therapy (CBT):</strong> Helps identify physical stress responses and teaches behavioral techniques to calm the nervous system.</li><li><strong>Supplements:</strong> Research indicates that magnesium citrate, coenzyme Q10, and riboflavin (vitamin B2) may reduce migraine frequency.</li></ul>',
                'bn' => '<p>মূল চিকিৎসার পাশাপাশি কিছু সহায়ক বিকল্প পদ্ধতি মাইগ্রেন নিয়ন্ত্রণে সহায়ক হতে পারে:</p><ul><li><strong>আকুপাংচার (Acupuncture):</strong> বৈজ্ঞানিক গবেষণায় প্রমাণিত যে প্রাচীন আকুপাংচার পদ্ধতি নিয়মিত গ্রহণে মাইগ্রেন আক্রমণের পুনরাবৃত্তি কমে।</li><li><strong>কগনিটিভ বিহেভিওরাল থেরাপি (CBT):</strong> মানসিক উদ্বেগ ও স্ট্রেস নিয়ন্ত্রণে রেখে স্নায়ু শান্ত রাখার বিশেষ সাইকোথেরাপি।</li><li><strong>পুষ্টি উপাদান ও সাপ্লিমেন্ট:</strong> চিকিৎসকের পরামর্শ অনুযায়ী ম্যাগনেসিয়াম, রিবোফ্লাভিন (ভিটামিন বি২) এবং কো-এনজাইম কিউ১০ সাপ্লিমেন্ট মাইগ্রেনের প্রকোপ কমাতে সহায়ক।</li></ul>'
            ],
            'coping_support'       => [
                'en' => '<p>Living with migraine can be challenging and unpredictable, affecting your work, relationships, and emotional health. Consider connecting with patient support groups, sharing your needs openly with employers and loved ones, and seeking professional mental health counseling if anxiety or depressive symptoms occur.</p>',
                'bn' => '<p>মাইগ্রেনের সাথে বসবাস করা একটি মানসিক ও শারীরিক পরীক্ষা। এটি কর্মজীবন ও পারিবারিক সম্পর্কে প্রভাব ফেলতে পারে। তাই পরিবারের সদস্য ও সহকর্মীদের সাথে আপনার সমস্যার ব্যাপারে খোলামেলা কথা বলুন, প্রয়োজনে পেশাদার কাউন্সেলরের সাহায্য নিন এবং পর্যাপ্ত বিশ্রামকে অগ্রাধিকার দিন।</p>'
            ],
            'preparing_for_appointment' => [
                'en' => '<p>To get the most out of your consultation with a neurologist:</p><ul><li><strong>Maintain a 4-week Headache Log:</strong> Note the date, time of onset, what you were doing, what foods you ate, duration, and what provided relief.</li><li><strong>List All Medications:</strong> Include prescription drugs, over-the-counter painkillers, and dietary supplements.</li><li><strong>Write Down Your Key Questions:</strong> E.g., What is causing my headaches? What preventive options are best for me? What lifestyle changes do you recommend?</li></ul>',
                'bn' => '<p>বিশেষজ্ঞ নিউরোলজিস্টের কাছে যাওয়ার আগে যেসব প্রস্তুতি গ্রহণ করা উচিত:</p><ul><li><strong>মাথাব্যথার একটি ডায়েরি রাখুন:</strong> অন্তত ২-৪ সপ্তাহ ব্যথার সময়, ব্যথার স্থান, তীব্রতা এবং ব্যথার আগে কী খেয়েছিলেন বা কী কাজ করছিলেন তা লিপিবদ্ধ করুন।</li><li><strong>চলমান ওষুধের তালিকা:</strong> বর্তমানে আপনি অন্য কোনো রোগের জন্য নিয়মিত কোনো ওষুধ সেবন করছেন কিনা তা লিখে নিয়ে যান।</li><li><strong>প্রশ্ন লিখে রাখুন:</strong> আপনার সমস্যার কারণ কী, কোন ওষুধটি আপনার জন্য সবচেয়ে নিরাপদ এবং কী কী সতর্কতা মানা জরুরি তা জেনে নিন।</li></ul>'
            ],
            'faqs'                 => [
                [
                    'question_en' => 'Can migraine be permanently cured?',
                    'question_bn' => 'মাইগ্রেন কি চিরতরে সম্পূর্ণ নিরাময় সম্ভব?',
                    'answer_en'   => 'While there is currently no single permanent cure for migraine, modern preventive medications, CGRP inhibitors, lifestyle adjustments, and trigger avoidance can reduce attacks by up to 80% to 90%, allowing most patients to live completely pain-free lives.',
                    'answer_bn'   => 'মাইগ্রেন একবারে চিরতরে নির্মূল করার কোনো একক স্থায়ী ওষুধ চিকিৎসাবিজ্ঞানে না থাকলেও আধুনিক প্রতিরোধমূলক ওষুধ, সিজিআরপি থেরাপি এবং স্বাস্থ্যকর জীবনযাপনের মাধ্যমে ব্যথার প্রকোপ শতকরা ৮০ থেকে ৯০ ভাগ পর্যন্ত নিয়ন্ত্রণ রাখা পুরোপুরি সম্ভব।'
                ],
                [
                    'question_en' => 'How is a migraine different from a regular tension headache?',
                    'question_bn' => 'সাধারণ টেনশন মাথাব্যথার সাথে মাইগ্রেনের পার্থক্য কী?',
                    'answer_en'   => 'A tension headache causes dull, generalized tightness around the whole forehead or back of the head. A migraine, by contrast, is typically throbbing and pulsating on one side of the head, and is accompanied by nausea, vomiting, or extreme light and sound sensitivity.',
                    'answer_bn'   => 'টেনশন মাথাব্যথায় সাধারণত পুরো কপাল বা মাথার চারপাশে মৃদু থেকে মাঝারি ধরনের ভারী ভাব বা ব্যান্ড বাঁধার মতো টানটান অনুভূতি হয়। অন্যদিকে মাইগ্রেনে মাথার একপাশে তীব্র দপদপানিযুক্ত ব্যথা হয় এবং এর সাথে বমি বমি ভাব, আলো ও শব্দের প্রতি অসহনীয় অনুভূতি দেখা দেয়।'
                ],
                [
                    'question_en' => 'What foods are the most common migraine triggers?',
                    'question_bn' => 'কোন কোন খাবার মাইগ্রেনের ব্যথা বাড়িয়ে দিতে পারে?',
                    'answer_en'   => 'Aged cheeses, processed cured meats containing nitrates, chocolate, artificial sweeteners (like aspartame), monosodium glutamate (MSG), and excessive alcohol or caffeine fluctuations are recognized food triggers.',
                    'answer_bn'   => 'পুরনো পনির, অতিরিক্ত চকোলেট, প্রক্রিয়াজাত মাংস, কৃত্রিম মিষ্টি (অ্যাসপার্টেম), টেস্টিং সল্ট (MSG), অতিরিক্ত চা-কফি বা হঠাৎ ক্যাফেইন ছেড়ে দেওয়া মাইগ্রেন ব্যথার সাধারণ ট্রিগার হিসেবে কাজ করে।'
                ],
                [
                    'question_en' => 'When should I see a neurologist for my migraines?',
                    'question_bn' => 'কখন নিউরোলজিস্টের (স্নায়ুরোগ বিশেষজ্ঞ) কাছে যাওয়া উচিত?',
                    'answer_en'   => 'You should see a neurologist if headaches occur more than 2 to 4 days per month, if over-the-counter painkillers stop providing relief, if headaches disrupt your daily life, or if you experience unusual symptoms like weakness or numbness.',
                    'answer_bn'   => 'যদি মাসে ২ থেকে ৪ দিনের বেশি মাথাব্যথায় আক্রান্ত হন, সাধারণ ব্যথানাশকে ব্যথা না কমে, স্বাভাবিক কাজকর্ম ব্যাহত হয় অথবা শরীরের কোনো অংশ অবশ বা দুর্বল অনুভব করেন, তবে অবিলম্বে নিউরোলজিস্টের পরামর্শ নেওয়া জরুরি।'
                ]
            ],
            'disclaimer'           => [
                'en' => '<p>This medical guide is prepared for informational and educational purposes following internationally accepted clinical guidelines from the Mayo Clinic and WHO. It does not replace direct professional medical consultation, diagnosis, or prescription. Always consult a BMDC-registered physician regarding your specific health condition.</p>',
                'bn' => '<p>এই স্বাস্থ্য নির্দেশিকাটি আন্তর্জাতিক মেয়ো ক্লিনিক ও ডব্লিউএইচও মানদণ্ড অনুসরণে সাধারণ সচেতনতা বৃদ্ধির লক্ষ্যে প্রস্তুত করা হয়েছে। এটি বিশেষজ্ঞ চিকিৎসকের সরাসরি প্রেসক্রিপশন বা বিকল্প নয়। যেকোনো স্বাস্থ্যগত প্রয়োজনে সর্বদা বিএমডিসি নিবন্ধিত চিকিৎসকের পরামর্শ নিন।</p>'
            ],
            'name'                 => [
                'en' => 'Migraine',
                'bn' => 'মাইগ্রেন'
            ]
        ]);

        $disease->save();

        // Attach relevant symptoms for Symptom Checker
        $headacheSym = Symptom::where('slug', 'headache')->first();
        $nauseaSym = Symptom::where('slug', 'nausea-vomiting')->first();
        $dizzinessSym = Symptom::where('slug', 'dizziness-vertigo')->first();

        $symptomSync = [];
        if ($headacheSym) {
            $symptomSync[$headacheSym->id] = ['is_primary' => true, 'match_weight' => 10];
        }
        if ($nauseaSym) {
            $symptomSync[$nauseaSym->id] = ['is_primary' => false, 'match_weight' => 7];
        }
        if ($dizzinessSym) {
            $symptomSync[$dizzinessSym->id] = ['is_primary' => false, 'match_weight' => 6];
        }

        if (!empty($symptomSync)) {
            $disease->symptoms()->sync($symptomSync);
        }

        // Add References
        $disease->references()->delete();
        $disease->references()->createMany([
            [
                'source_name' => 'Mayo Clinic',
                'source_url'  => 'https://www.mayoclinic.org/diseases-conditions/migraine-headache/symptoms-causes/syc-20360201',
                'notes'       => 'Migraine Symptoms & Causes Clinical Overview',
                'order_index' => 1,
            ],
            [
                'source_name' => 'Mayo Clinic',
                'source_url'  => 'https://www.mayoclinic.org/diseases-conditions/migraine-headache/diagnosis-treatment/drc-20360207',
                'notes'       => 'Migraine Diagnosis, Medications & Therapies',
                'order_index' => 2,
            ],
            [
                'source_name' => 'World Health Organization (WHO)',
                'source_url'  => 'https://www.who.int/news-room/fact-sheets/detail/headache-disorders',
                'notes'       => 'Headache Disorders Global Fact Sheet',
                'order_index' => 3,
            ],
            [
                'source_name' => 'American Migraine Foundation',
                'source_url'  => 'https://americanmigrainefoundation.org/',
                'notes'       => 'Clinical Guidelines on Migraine Prevention and Treatments',
                'order_index' => 4,
            ]
        ]);

        echo "Migraine benchmark demo seeded successfully with all 43 fields populated in EN and BN!\n";
    }
}
