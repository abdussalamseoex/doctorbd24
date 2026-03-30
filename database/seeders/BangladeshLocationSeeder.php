<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        // ── Divisions ────────────────────────────────────────────────
        $divData = [
            'dhaka'      => ['en' => 'Dhaka',      'bn' => 'ঢাকা'],
            'chittagong' => ['en' => 'Chittagong',  'bn' => 'চট্টগ্রাম'],
            'rajshahi'   => ['en' => 'Rajshahi',    'bn' => 'রাজশাহী'],
            'khulna'     => ['en' => 'Khulna',      'bn' => 'খুলনা'],
            'barishal'   => ['en' => 'Barishal',    'bn' => 'বরিশাল'],
            'sylhet'     => ['en' => 'Sylhet',      'bn' => 'সিলেট'],
            'rangpur'    => ['en' => 'Rangpur',     'bn' => 'রংপুর'],
            'mymensingh' => ['en' => 'Mymensingh',  'bn' => 'ময়মনসিংহ'],
        ];

        $divIds = [];
        foreach ($divData as $slug => $names) {
            $div = Division::firstOrCreate(
                ['slug' => $slug],
                ['name' => ['en' => $names['en'], 'bn' => $names['bn']]]
            );
            $divIds[$slug] = $div->id;
        }

        // ── Districts (all 64) ───────────────────────────────────────
        // Format: [division_slug, en_name, bn_name, slug]
        $districtData = [
            // Dhaka (13)
            ['dhaka', 'Dhaka',        'ঢাকা',          'dhaka-district'],
            ['dhaka', 'Gazipur',      'গাজীপুর',       'gazipur'],
            ['dhaka', 'Narayanganj',  'নারায়ণগঞ্জ',   'narayanganj'],
            ['dhaka', 'Narsingdi',    'নরসিংদী',        'narsingdi'],
            ['dhaka', 'Manikganj',    'মানিকগঞ্জ',     'manikganj'],
            ['dhaka', 'Munshiganj',   'মুন্সিগঞ্জ',    'munshiganj'],
            ['dhaka', 'Kishoreganj',  'কিশোরগঞ্জ',    'kishoreganj'],
            ['dhaka', 'Tangail',      'টাঙ্গাইল',      'tangail'],
            ['dhaka', 'Faridpur',     'ফরিদপুর',        'faridpur'],
            ['dhaka', 'Rajbari',      'রাজবাড়ী',       'rajbari'],
            ['dhaka', 'Gopalganj',    'গোপালগঞ্জ',     'gopalganj'],
            ['dhaka', 'Madaripur',    'মাদারীপুর',     'madaripur'],
            ['dhaka', 'Shariatpur',   'শরীয়তপুর',     'shariatpur'],
            // Chittagong (11)
            ['chittagong', 'Chittagong',   'চট্টগ্রাম',     'chittagong-district'],
            ['chittagong', "Cox's Bazar",  'কক্সবাজার',     'coxs-bazar'],
            ['chittagong', 'Comilla',      'কুমিল্লা',      'comilla'],
            ['chittagong', 'Feni',         'ফেনী',           'feni'],
            ['chittagong', 'Noakhali',     'নোয়াখালী',     'noakhali'],
            ['chittagong', 'Lakshmipur',   'লক্ষ্মীপুর',   'lakshmipur'],
            ['chittagong', 'Chandpur',     'চাঁদপুর',       'chandpur'],
            ['chittagong', 'Brahmanbaria', 'ব্রাহ্মণবাড়িয়া','brahmanbaria'],
            ['chittagong', 'Khagrachhari', 'খাগড়াছড়ি',   'khagrachhari'],
            ['chittagong', 'Rangamati',    'রাঙামাটি',      'rangamati'],
            ['chittagong', 'Bandarban',    'বান্দরবান',     'bandarban'],
            // Rajshahi (8)
            ['rajshahi', 'Rajshahi',  'রাজশাহী',   'rajshahi-district'],
            ['rajshahi', 'Chapainawabganj', 'চাঁপাইনবাবগঞ্জ', 'chapainawabganj'],
            ['rajshahi', 'Natore',    'নাটোর',     'natore'],
            ['rajshahi', 'Naogaon',   'নওগাঁ',     'naogaon'],
            ['rajshahi', 'Bogura',    'বগুড়া',     'bogura'],
            ['rajshahi', 'Joypurhat', 'জয়পুরহাট', 'joypurhat'],
            ['rajshahi', 'Sirajganj', 'সিরাজগঞ্জ', 'sirajganj'],
            ['rajshahi', 'Pabna',     'পাবনা',     'pabna'],
            // Khulna (10)
            ['khulna', 'Khulna',      'খুলনা',      'khulna-district'],
            ['khulna', 'Bagerhat',    'বাগেরহাট',  'bagerhat'],
            ['khulna', 'Satkhira',    'সাতক্ষীরা', 'satkhira'],
            ['khulna', 'Jessore',     'যশোর',       'jessore'],
            ['khulna', 'Narail',      'নড়াইল',     'narail'],
            ['khulna', 'Magura',      'মাগুরা',     'magura'],
            ['khulna', 'Jhenaidah',   'ঝিনাইদহ',   'jhenaidah'],
            ['khulna', 'Kushtia',     'কুষ্টিয়া',  'kushtia'],
            ['khulna', 'Chuadanga',   'চুয়াডাঙ্গা','chuadanga'],
            ['khulna', 'Meherpur',    'মেহেরপুর',  'meherpur'],
            // Barishal (6)
            ['barishal', 'Barishal',  'বরিশাল',     'barishal-district'],
            ['barishal', 'Bhola',     'ভোলা',       'bhola'],
            ['barishal', 'Pirojpur',  'পিরোজপুর',  'pirojpur'],
            ['barishal', 'Jhalokathi','ঝালকাঠি',   'jhalokathi'],
            ['barishal', 'Patuakhali','পটুয়াখালী', 'patuakhali'],
            ['barishal', 'Barguna',   'বরগুনা',     'barguna'],
            // Sylhet (4)
            ['sylhet', 'Sylhet',      'সিলেট',      'sylhet-district'],
            ['sylhet', 'Moulvibazar', 'মৌলভীবাজার','moulvibazar'],
            ['sylhet', 'Habiganj',    'হবিগঞ্জ',   'habiganj'],
            ['sylhet', 'Sunamganj',   'সুনামগঞ্জ', 'sunamganj'],
            // Rangpur (8)
            ['rangpur', 'Rangpur',    'রংপুর',      'rangpur-district'],
            ['rangpur', 'Gaibandha',  'গাইবান্ধা', 'gaibandha'],
            ['rangpur', 'Kurigram',   'কুড়িগ্রাম', 'kurigram'],
            ['rangpur', 'Lalmonirhat','লালমনিরহাট','lalmonirhat'],
            ['rangpur', 'Nilphamari', 'নীলফামারী', 'nilphamari'],
            ['rangpur', 'Panchagarh', 'পঞ্চগড়',   'panchagarh'],
            ['rangpur', 'Thakurgaon', 'ঠাকুরগাঁও', 'thakurgaon'],
            ['rangpur', 'Dinajpur',   'দিনাজপুর',   'dinajpur'],
            // Mymensingh (4)
            ['mymensingh', 'Mymensingh', 'ময়মনসিংহ',  'mymensingh-district'],
            ['mymensingh', 'Jamalpur',   'জামালপুর',  'jamalpur'],
            ['mymensingh', 'Sherpur',    'শেরপুর',    'sherpur'],
            ['mymensingh', 'Netrokona',  'নেত্রকোণা', 'netrokona'],
        ];

        $districtIds = [];
        foreach ($districtData as [$divSlug, $en, $bn, $slug]) {
            $dist = District::firstOrCreate(
                ['slug' => $slug],
                [
                    'division_id' => $divIds[$divSlug],
                    'name' => ['en' => $en, 'bn' => $bn],
                ]
            );
            $districtIds[$slug] = $dist->id;
        }

        // ── Areas / Upazilas ─────────────────────────────────────────
        // Format: [district_slug, en_name, bn_name]
        $areaData = [
            // Dhaka District
            'dhaka-district' => [
                ['Dhanmondi', 'ধানমন্ডি'], ['Gulshan', 'গুলশান'], ['Banani', 'বনানী'],
                ['Mirpur', 'মিরপুর'], ['Uttara', 'উত্তরা'], ['Mohammadpur', 'মোহাম্মদপুর'],
                ['Shyamoli', 'শ্যামলী'], ['Farmgate', 'ফার্মগেট'], ['Shahbagh', 'শাহবাগ'],
                ['Motijheel', 'মতিঝিল'], ['Paltan', 'পল্টন'], ['Tejgaon', 'তেজগাঁও'],
                ['Rampura', 'রামপুরা'], ['Badda', 'বাড্ডা'], ['Khilgaon', 'খিলগাঁও'],
                ['Jatrabari', 'যাত্রাবাড়ী'], ['Demra', 'ডেমরা'], ['Lalbagh', 'লালবাগ'],
                ['Hazaribagh', 'হাজারীবাগ'], ['Kamrangirchar', 'কামরাঙ্গীরচর'],
                ['Savar', 'সাভার'], ['Keraniganj', 'কেরাণীগঞ্জ'], ['Dohar', 'দোহার'],
                ['Nawabganj', 'নবাবগঞ্জ'], ['Kadamtali', 'কদমতলী'],
            ],
            // Gazipur
            'gazipur' => [
                ['Gazipur Sadar', 'গাজীপুর সদর'], ['Tongi', 'টঙ্গী'], ['Kaliakair', 'কালিয়াকৈর'],
                ['Kapasia', 'কাপাসিয়া'], ['Kaliganj', 'কালীগঞ্জ'], ['Sreepur', 'শ্রীপুর'],
            ],
            // Narayanganj
            'narayanganj' => [
                ['Narayanganj Sadar', 'নারায়ণগঞ্জ সদর'], ['Araihazar', 'আড়াইহাজার'],
                ['Bandar', 'বন্দর'], ['Rupganj', 'রূপগঞ্জ'], ['Sonargaon', 'সোনারগাঁও'],
            ],
            // Narsingdi
            'narsingdi' => [
                ['Narsingdi Sadar', 'নরসিংদী সদর'], ['Palash', 'পলাশ'], ['Shibpur', 'শিবপুর'],
                ['Belabo', 'বেলাবো'], ['Monohardi', 'মনোহরদী'], ['Raipura', 'রায়পুরা'],
            ],
            // Tangail
            'tangail' => [
                ['Tangail Sadar', 'টাঙ্গাইল সদর'], ['Basail', 'বাসাইল'], ['Bhuapur', 'ভূঞাপুর'],
                ['Delduar', 'দেলদুয়ার'], ['Ghatail', 'ঘাটাইল'], ['Gopalpur', 'গোপালপুর'],
                ['Kalihati', 'কালিহাতী'], ['Madhupur', 'মধুপুর'], ['Mirzapur', 'মির্জাপুর'],
                ['Nagarpur', 'নাগরপুর'], ['Sakhipur', 'সখিপুর'],
            ],
            // Kishoreganj
            'kishoreganj' => [
                ['Kishoreganj Sadar', 'কিশোরগঞ্জ সদর'], ['Bajitpur', 'বাজিতপুর'],
                ['Bhairab', 'ভৈরব'], ['Hossainpur', 'হোসেনপুর'], ['Itna', 'ইটনা'],
                ['Karimganj', 'করিমগঞ্জ'], ['Katiadi', 'কটিয়াদী'], ['Kuliarchar', 'কুলিয়ারচর'],
                ['Mithamain', 'মিঠামইন'], ['Nikli', 'নিকলী'], ['Pakundia', 'পাকুন্দিয়া'],
                ['Tarail', 'তাড়াইল'],
            ],
            // Manikganj
            'manikganj' => [
                ['Manikganj Sadar', 'মানিকগঞ্জ সদর'], ['Daulatpur', 'দৌলতপুর'],
                ['Ghior', 'ঘিওর'], ['Harirampur', 'হরিরামপুর'], ['Saturia', 'সাটুরিয়া'],
                ['Shivalaya', 'শিবালয়'], ['Singair', 'সিংগাইর'],
            ],
            // Munshiganj
            'munshiganj' => [
                ['Munshiganj Sadar', 'মুন্সিগঞ্জ সদর'], ['Gazaria', 'গজারিয়া'],
                ['Lohajang', 'লৌহজং'], ['Sirajdikhan', 'সিরাজদিখান'],
                ['Sreenagar', 'শ্রীনগর'], ['Tongibari', 'টংগীবাড়ী'],
            ],
            // Faridpur
            'faridpur' => [
                ['Faridpur Sadar', 'ফরিদপুর সদর'], ['Alfadanga', 'আলফাডাঙ্গা'],
                ['Bhanga', 'ভাঙ্গা'], ['Boalmari', 'বোয়ালমারী'], ['Char Bhadrasan', 'চরভদ্রাসন'],
                ['Madhukhali', 'মধুখালী'], ['Nagarkanda', 'নগরকান্দা'], ['Sadarpur', 'সদরপুর'],
                ['Saltha', 'সালথা'],
            ],
            // Gopalganj
            'gopalganj' => [
                ['Gopalganj Sadar', 'গোপালগঞ্জ সদর'], ['Kashiani', 'কাশিয়ানী'],
                ['Kotalipara', 'কোটালীপাড়া'], ['Muksudpur', 'মুকসুদপুর'], ['Tungipara', 'টুঙ্গিপাড়া'],
            ],
            // Madaripur
            'madaripur' => [
                ['Madaripur Sadar', 'মাদারীপুর সদর'], ['Kalkini', 'কালকিনি'],
                ['Rajoir', 'রাজৈর'], ['Shibchar', 'শিবচর'],
            ],
            // Shariatpur
            'shariatpur' => [
                ['Shariatpur Sadar', 'শরীয়তপুর সদর'], ['Bhedarganj', 'ভেদরগঞ্জ'],
                ['Damudya', 'ডামুড্যা'], ['Gosairhat', 'গোসাইরহাট'],
                ['Naria', 'নড়িয়া'], ['Zanjira', 'জাজিরা'],
            ],
            // Rajbari
            'rajbari' => [
                ['Rajbari Sadar', 'রাজবাড়ী সদর'], ['Baliakandi', 'বালিয়াকান্দি'],
                ['Goalanda', 'গোয়ালন্দ'], ['Pangsha', 'পাংশা'],
            ],
            // Chittagong District
            'chittagong-district' => [
                ['Agrabad', 'আগ্রাবাদ'], ['Nasirabad', 'নাসিরাবাদ'], ['Panchlaish', 'পাঁচলাইশ'],
                ['Halishahar', 'হালিশহর'], ['Pahartali', 'পাহাড়তলী'], ['Chandgaon', 'চান্দগাঁও'],
                ['Double Mooring', 'ডবলমুরিং'], ['Kotwali', 'কোতোয়ালী'],
                ['Rangunia', 'রাঙ্গুনিয়া'], ['Sitakunda', 'সীতাকুণ্ড'], ['Mirsharai', 'মিরসরাই'],
                ['Patiya', 'পটিয়া'], ['Chandanaish', 'চন্দনাইশ'], ['Satkania', 'সাতকানিয়া'],
                ['Lohagara', 'লোহাগাড়া'], ['Boalkhali', 'বোয়ালখালী'], ['Anwara', 'আনোয়ারা'],
                ['Sandwip', 'সন্দ্বীপ'], ['Fatikchhari', 'ফটিকছড়ি'],
            ],
            // Cox's Bazar
            'coxs-bazar' => [
                ["Cox's Bazar Sadar", 'কক্সবাজার সদর'], ['Chakaria', 'চকরিয়া'],
                ['Kutubdia', 'কুতুবদিয়া'], ['Maheshkhali', 'মহেশখালী'],
                ['Pekua', 'পেকুয়া'], ['Ramu', 'রামু'], ['Teknaf', 'টেকনাফ'], ['Ukhia', 'উখিয়া'],
            ],
            // Comilla
            'comilla' => [
                ['Comilla Sadar', 'কুমিল্লা সদর'], ['Barura', 'বরুড়া'], ['Brahmanpara', 'ব্রাহ্মণপাড়া'],
                ['Burichang', 'বুড়িচং'], ['Chandina', 'চান্দিনা'], ['Chauddagram', 'চৌদ্দগ্রাম'],
                ['Daudkandi', 'দাউদকান্দি'], ['Debidwar', 'দেবীদ্বার'], ['Homna', 'হোমনা'],
                ['Laksam', 'লাকসাম'], ['Manoharganj', 'মনোহরগঞ্জ'], ['Meghna', 'মেঘনা'],
                ['Muradnagar', 'মুরাদনগর'], ['Nangalkot', 'নাঙ্গলকোট'], ['Titas', 'তিতাস'],
            ],
            // Feni
            'feni' => [
                ['Feni Sadar', 'ফেনী সদর'], ['Chhagalnaiya', 'ছাগলনাইয়া'],
                ['Daganbhuiyan', 'দাগনভূঞা'], ['Parshuram', 'পরশুরাম'],
                ['Sonagazi', 'সোনাগাজী'], ['Fulgazi', 'ফুলগাজী'],
            ],
            // Noakhali
            'noakhali' => [
                ['Noakhali Sadar', 'নোয়াখালী সদর'], ['Begumganj', 'বেগমগঞ্জ'],
                ['Chatkhil', 'চাটখিল'], ['Companiganj', 'কোম্পানীগঞ্জ'],
                ['Hatiya', 'হাতিয়া'], ['Kabirhat', 'কবিরহাট'],
                ['Senbagh', 'সেনবাগ'], ['Subarnachar', 'সুবর্ণচর'],
            ],
            // Brahmanbaria
            'brahmanbaria' => [
                ['Brahmanbaria Sadar', 'ব্রাহ্মণবাড়িয়া সদর'], ['Akhaura', 'আখাউড়া'],
                ['Ashuganj', 'আশুগঞ্জ'], ['Bancharampur', 'বাঞ্ছারামপুর'],
                ['Bijoynagar', 'বিজয়নগর'], ['Kasba', 'কসবা'],
                ['Nabinagar', 'নবীনগর'], ['Nasirnagar', 'নাসিরনগর'], ['Sarail', 'সরাইল'],
            ],
            // Lakshmipur
            'lakshmipur' => [
                ['Lakshmipur Sadar', 'লক্ষ্মীপুর সদর'], ['Kamalnagar', 'কমলনগর'],
                ['Ramganj', 'রামগঞ্জ'], ['Ramgati', 'রামগতি'], ['Raipur', 'রায়পুর'],
            ],
            // Chandpur
            'chandpur' => [
                ['Chandpur Sadar', 'চাঁদপুর সদর'], ['Faridganj', 'ফরিদগঞ্জ'],
                ['Haimchar', 'হাইমচর'], ['Haziganj', 'হাজীগঞ্জ'],
                ['Kachua', 'কচুয়া'], ['Matlab North', 'মতলব উত্তর'],
                ['Matlab South', 'মতলব দক্ষিণ'], ['Shahrasti', 'শাহরাস্তি'],
            ],
            // Rajshahi District
            'rajshahi-district' => [
                ['Rajshahi City', 'রাজশাহী সিটি'], ['Paba', 'পবা'], ['Godagari', 'গোদাগাড়ী'],
                ['Tanore', 'তানোর'], ['Mohanpur', 'মোহনপুর'], ['Charghat', 'চারঘাট'],
                ['Durgapur', 'দুর্গাপুর'], ['Bagmara', 'বাগমারা'], ['Puthia', 'পুঠিয়া'],
            ],
            // Bogura
            'bogura' => [
                ['Bogura Sadar', 'বগুড়া সদর'], ['Adamdighi', 'আদমদীঘি'],
                ['Dhunat', 'ধুনট'], ['Dupchanchia', 'দুপচাঁচিয়া'],
                ['Gabtali', 'গাবতলী'], ['Kahaloo', 'কাহালু'],
                ['Nandigram', 'নন্দীগ্রাম'], ['Sariakandi', 'সারিয়াকান্দি'],
                ['Shajahanpur', 'শাজাহানপুর'], ['Sherpur', 'শেরপুর'],
                ['Shibganj', 'শিবগঞ্জ'], ['Sonatala', 'সোনাতলা'],
            ],
            // Sirajganj
            'sirajganj' => [
                ['Sirajganj Sadar', 'সিরাজগঞ্জ সদর'], ['Belkuchi', 'বেলকুচি'],
                ['Chauhali', 'চৌহালী'], ['Kamarkhanda', 'কামারখন্দ'],
                ['Kazipur', 'কাজীপুর'], ['Raiganj', 'রায়গঞ্জ'],
                ['Shahjadpur', 'শাহজাদপুর'], ['Tarash', 'তাড়াশ'], ['Ullahpara', 'উল্লাপাড়া'],
            ],
            // Pabna
            'pabna' => [
                ['Pabna Sadar', 'পাবনা সদর'], ['Atgharia', 'আটঘরিয়া'],
                ['Bera', 'বেড়া'], ['Bhangura', 'ভাঙ্গুড়া'],
                ['Chatmohar', 'চাটমোহর'], ['Faridpur', 'ফরিদপুর'],
                ['Ishwardi', 'ঈশ্বরদী'], ['Santhia', 'সাঁথিয়া'], ['Sujanagar', 'সুজানগর'],
            ],
            // Natore
            'natore' => [
                ['Natore Sadar', 'নাটোর সদর'], ['Bagatipara', 'বাগাতিপাড়া'],
                ['Baraigram', 'বড়াইগ্রাম'], ['Gurudaspur', 'গুরুদাসপুর'],
                ['Lalpur', 'লালপুর'], ['Singra', 'সিংড়া'],
            ],
            // Naogaon
            'naogaon' => [
                ['Naogaon Sadar', 'নওগাঁ সদর'], ['Atrai', 'আত্রাই'],
                ['Badalgachhi', 'বদলগাছী'], ['Dhamoirhat', 'ধামইরহাট'],
                ['Mahadebpur', 'মহাদেবপুর'], ['Manda', 'মান্দা'],
                ['Niamatpur', 'নিয়ামতপুর'], ['Patnitala', 'পত্নীতলা'],
                ['Porsha', 'পোরশা'], ['Raninagar', 'রানীনগর'], ['Sapahar', 'সাপাহার'],
            ],
            // Joypurhat
            'joypurhat' => [
                ['Joypurhat Sadar', 'জয়পুরহাট সদর'], ['Akkelpur', 'আক্কেলপুর'],
                ['Kalai', 'কালাই'], ['Khetlal', 'ক্ষেতলাল'], ['Panchbibi', 'পাঁচবিবি'],
            ],
            // Chapainawabganj
            'chapainawabganj' => [
                ['Chapainawabganj Sadar', 'চাঁপাইনবাবগঞ্জ সদর'], ['Bholahat', 'ভোলাহাট'],
                ['Gomastapur', 'গোমস্তাপুর'], ['Nachole', 'নাচোল'], ['Shibganj', 'শিবগঞ্জ'],
            ],
            // Khulna District
            'khulna-district' => [
                ['Khulna City', 'খুলনা সিটি'], ['Batiaghata', 'বটিয়াঘাটা'],
                ['Dacope', 'দাকোপ'], ['Daulatpur', 'দৌলতপুর'],
                ['Dumuria', 'ডুমুরিয়া'], ['Dighalia', 'দিঘলিয়া'],
                ['Koyra', 'কয়রা'], ['Paikgachha', 'পাইকগাছা'],
                ['Phultala', 'ফুলতলা'], ['Rupsha', 'রূপসা'], ['Terokhada', 'তেরখাদা'],
            ],
            // Jessore
            'jessore' => [
                ['Jessore Sadar', 'যশোর সদর'], ['Abhaynagar', 'অভয়নগর'],
                ['Bagherpar', 'বাঘারপাড়া'], ['Chaugachha', 'চৌগাছা'],
                ['Jhikargachha', 'ঝিকরগাছা'], ['Keshabpur', 'কেশবপুর'],
                ['Manirampur', 'মণিরামপুর'], ['Sharsha', 'শার্শা'],
            ],
            // Satkhira
            'satkhira' => [
                ['Satkhira Sadar', 'সাতক্ষীরা সদর'], ['Assasuni', 'আশাশুনি'],
                ['Debhata', 'দেবহাটা'], ['Kalaroa', 'কালারোয়া'],
                ['Kaliganj', 'কালিগঞ্জ'], ['Shyamnagar', 'শ্যামনগর'], ['Tala', 'তালা'],
            ],
            // Kushtia
            'kushtia' => [
                ['Kushtia Sadar', 'কুষ্টিয়া সদর'], ['Bheramara', 'ভেড়ামারা'],
                ['Daulatpur', 'দৌলতপুর'], ['Khoksa', 'খোকসা'], ['Kumarkhali', 'কুমারখালী'],
                ['Mirpur', 'মিরপুর'],
            ],
            // Jhenaidah
            'jhenaidah' => [
                ['Jhenaidah Sadar', 'ঝিনাইদহ সদর'], ['Harinakundu', 'হরিণাকুণ্ডু'],
                ['Kaliganj', 'কালীগঞ্জ'], ['Kotchandpur', 'কোটচাঁদপুর'],
                ['Maheshpur', 'মহেশপুর'], ['Shailkupa', 'শৈলকুপা'],
            ],
            // Bagerhat
            'bagerhat' => [
                ['Bagerhat Sadar', 'বাগেরহাট সদর'], ['Chitalmari', 'চিতলমারী'],
                ['Fakirhat', 'ফকিরহাট'], ['Kachua', 'কচুয়া'],
                ['Mollahat', 'মোল্লাহাট'], ['Mongla', 'মোংলা'],
                ['Morrelganj', 'মোড়েলগঞ্জ'], ['Rampal', 'রামপাল'], ['Sharankhola', 'শরণখোলা'],
            ],
            // Narail
            'narail' => [
                ['Narail Sadar', 'নড়াইল সদর'], ['Kalia', 'কালিয়া'], ['Lohagara', 'লোহাগড়া'],
            ],
            // Magura
            'magura' => [
                ['Magura Sadar', 'মাগুরা সদর'], ['Mohammadpur', 'মহম্মদপুর'],
                ['Shalikha', 'শালিখা'], ['Sreepur', 'শ্রীপুর'],
            ],
            // Chuadanga
            'chuadanga' => [
                ['Chuadanga Sadar', 'চুয়াডাঙ্গা সদর'], ['Alamdanga', 'আলমডাঙ্গা'],
                ['Damurhuda', 'দামুড়হুদা'], ['Jibannagar', 'জীবননগর'],
            ],
            // Meherpur
            'meherpur' => [
                ['Meherpur Sadar', 'মেহেরপুর সদর'], ['Gangni', 'গাংনী'], ['Mujibnagar', 'মুজিবনগর'],
            ],
            // Barishal District
            'barishal-district' => [
                ['Barishal City', 'বরিশাল সিটি'], ['Agailjhara', 'আগৈলঝাড়া'],
                ['Babuganj', 'বাবুগঞ্জ'], ['Bakerganj', 'বাকেরগঞ্জ'],
                ['Banaripara', 'বানারীপাড়া'], ['Gaurnadi', 'গৌরনদী'],
                ['Hizla', 'হিজলা'], ['Mehendiganj', 'মেহেন্দিগঞ্জ'],
                ['Muladi', 'মুলাদী'], ['Wazirpur', 'উজিরপুর'],
            ],
            // Bhola
            'bhola' => [
                ['Bhola Sadar', 'ভোলা সদর'], ['Borhanuddin', 'বোরহানউদ্দিন'],
                ['Char Fasson', 'চরফ্যাশন'], ['Daulatkhan', 'দৌলতখান'],
                ['Lalmohan', 'লালমোহন'], ['Manpura', 'মনপুরা'], ['Tazumuddin', 'তজুমদ্দিন'],
            ],
            // Pirojpur
            'pirojpur' => [
                ['Pirojpur Sadar', 'পিরোজপুর সদর'], ['Bhandaria', 'ভান্ডারিয়া'],
                ['Indurkani', 'ইন্দুরকানী'], ['Kawkhali', 'কাউখালী'],
                ['Mathbaria', 'মঠবাড়িয়া'], ['Nazirpur', 'নাজিরপুর'], ['Nesarabad', 'নেছারাবাদ'],
            ],
            // Jhalokathi
            'jhalokathi' => [
                ['Jhalokathi Sadar', 'ঝালকাঠি সদর'], ['Kanthalia', 'কাঁঠালিয়া'],
                ['Nalchity', 'নলছিটি'], ['Rajapur', 'রাজাপুর'],
            ],
            // Patuakhali
            'patuakhali' => [
                ['Patuakhali Sadar', 'পটুয়াখালী সদর'], ['Bauphal', 'বাউফল'],
                ['Dashmina', 'দশমিনা'], ['Dumki', 'দুমকি'],
                ['Galachipa', 'গলাচিপা'], ['Kalapara', 'কলাপাড়া'],
                ['Mirzaganj', 'মির্জাগঞ্জ'], ['Rangabali', 'রাঙ্গাবালী'],
            ],
            // Barguna
            'barguna' => [
                ['Barguna Sadar', 'বরগুনা সদর'], ['Amtali', 'আমতলী'],
                ['Bamna', 'বামনা'], ['Betagi', 'বেতাগী'],
                ['Patharghata', 'পাথরঘাটা'], ['Taltali', 'তালতলী'],
            ],
            // Sylhet District
            'sylhet-district' => [
                ['Sylhet City', 'সিলেট সিটি'], ['Balaganj', 'বালাগঞ্জ'],
                ['Beanibazar', 'বিয়ানীবাজার'], ['Bishwanath', 'বিশ্বনাথ'],
                ['Companiganj', 'কোম্পানীগঞ্জ'], ['Fenchuganj', 'ফেঞ্চুগঞ্জ'],
                ['Golapganj', 'গোলাপগঞ্জ'], ['Gowainghat', 'গোয়াইনঘাট'],
                ['Jaintiapur', 'জৈন্তাপুর'], ['Kanaighat', 'কানাইঘাট'],
                ['Osmani Nagar', 'ওসমানী নগর'], ['South Surma', 'দক্ষিণ সুরমা'],
                ['Zakiganj', 'জকিগঞ্জ'],
            ],
            // Moulvibazar
            'moulvibazar' => [
                ['Moulvibazar Sadar', 'মৌলভীবাজার সদর'], ['Barlekha', 'বড়লেখা'],
                ['Juri', 'জুড়ী'], ['Kamalganj', 'কমলগঞ্জ'],
                ['Kulaura', 'কুলাউড়া'], ['Rajnagar', 'রাজনগর'], ['Sreemangal', 'শ্রীমঙ্গল'],
            ],
            // Habiganj
            'habiganj' => [
                ['Habiganj Sadar', 'হবিগঞ্জ সদর'], ['Ajmiriganj', 'আজমিরীগঞ্জ'],
                ['Bahubal', 'বাহুবল'], ['Baniachong', 'বানিয়াচং'],
                ['Chunarughat', 'চুনারুঘাট'], ['Lakhai', 'লাখাই'],
                ['Madhabpur', 'মাধবপুর'], ['Nabiganj', 'নবীগঞ্জ'],
            ],
            // Sunamganj
            'sunamganj' => [
                ['Sunamganj Sadar', 'সুনামগঞ্জ সদর'], ['Bishwamvarpur', 'বিশ্বম্ভরপুর'],
                ['Chhatak', 'ছাতক'], ['Derai', 'দিরাই'],
                ['Dharampasha', 'ধর্মপাশা'], ['Dowarabazar', 'দোয়ারাবাজার'],
                ['Jagannathpur', 'জগন্নাথপুর'], ['Jamalganj', 'জামালগঞ্জ'],
                ['Shalla', 'শাল্লা'], ['Tahirpur', 'তাহিরপুর'],
            ],
            // Rangpur District
            'rangpur-district' => [
                ['Rangpur City', 'রংপুর সিটি'], ['Badarganj', 'বদরগঞ্জ'],
                ['Gangachhara', 'গঙ্গাচড়া'], ['Kaunia', 'কাউনিয়া'],
                ['Mithapukur', 'মিঠাপুকুর'], ['Pirgachha', 'পীরগাছা'],
                ['Pirganj', 'পীরগঞ্জ'], ['Taraganj', 'তারাগঞ্জ'],
            ],
            // Dinajpur
            'dinajpur' => [
                ['Dinajpur Sadar', 'দিনাজপুর সদর'], ['Birampur', 'বিরামপুর'],
                ['Birganj', 'বীরগঞ্জ'], ['Bochaganj', 'বোচাগঞ্জ'],
                ['Chirirbandar', 'চিরিরবন্দর'], ['Fulbari', 'ফুলবাড়ী'],
                ['Ghoraghat', 'ঘোড়াঘাট'], ['Hakimpur', 'হাকিমপুর'],
                ['Kaharole', 'কাহারোল'], ['Nawabganj', 'নবাবগঞ্জ'],
                ['Parbatipur', 'পার্বতীপুর'], ['Phulbari', 'ফুলবাড়ী'],
            ],
            // Gaibandha
            'gaibandha' => [
                ['Gaibandha Sadar', 'গাইবান্ধা সদর'], ['Fulchhari', 'ফুলছড়ি'],
                ['Gobindaganj', 'গোবিন্দগঞ্জ'], ['Palashbari', 'পলাশবাড়ী'],
                ['Sadullapur', 'সাদুল্লাপুর'], ['Saghata', 'সাঘাটা'], ['Sundarganj', 'সুন্দরগঞ্জ'],
            ],
            // Kurigram
            'kurigram' => [
                ['Kurigram Sadar', 'কুড়িগ্রাম সদর'], ['Bhurungamari', 'ভুরুঙ্গামারী'],
                ['Char Rajibpur', 'চর রাজিবপুর'], ['Chilmari', 'চিলমারী'],
                ['Nageshwari', 'নাগেশ্বরী'], ['Phulbari', 'ফুলবাড়ী'],
                ['Rajarhat', 'রাজারহাট'], ['Rowmari', 'রৌমারী'], ['Ulipur', 'উলিপুর'],
            ],
            // Lalmonirhat
            'lalmonirhat' => [
                ['Lalmonirhat Sadar', 'লালমনিরহাট সদর'], ['Aditmari', 'আদিতমারী'],
                ['Hatibandha', 'হাতীবান্ধা'], ['Kaliganj', 'কালীগঞ্জ'], ['Patgram', 'পাটগ্রাম'],
            ],
            // Nilphamari
            'nilphamari' => [
                ['Nilphamari Sadar', 'নীলফামারী সদর'], ['Dimla', 'ডিমলা'],
                ['Domar', 'ডোমার'], ['Jaldhaka', 'জলঢাকা'],
                ['Kishoreganj', 'কিশোরগঞ্জ'], ['Saidpur', 'সৈয়দপুর'],
            ],
            // Panchagarh
            'panchagarh' => [
                ['Panchagarh Sadar', 'পঞ্চগড় সদর'], ['Atwari', 'আটোয়ারী'],
                ['Boda', 'বোদা'], ['Debiganj', 'দেবীগঞ্জ'], ['Tetulia', 'তেঁতুলিয়া'],
            ],
            // Thakurgaon
            'thakurgaon' => [
                ['Thakurgaon Sadar', 'ঠাকুরগাঁও সদর'], ['Baliadangi', 'বালিয়াডাঙ্গী'],
                ['Haripur', 'হরিপুর'], ['Pirganj', 'পীরগঞ্জ'], ['Ranisankail', 'রাণীশংকৈল'],
            ],
            // Mymensingh District
            'mymensingh-district' => [
                ['Mymensingh City', 'ময়মনসিংহ সিটি'], ['Bhaluka', 'ভালুকা'],
                ['Dhobaura', 'ধোবাউড়া'], ['Fulbaria', 'ফুলবাড়িয়া'],
                ['Gaffargaon', 'গফরগাঁও'], ['Gauripur', 'গৌরীপুর'],
                ['Haluaghat', 'হালুয়াঘাট'], ['Ishwarganj', 'ঈশ্বরগঞ্জ'],
                ['Muktagachha', 'মুক্তাগাছা'], ['Nandail', 'নান্দাইল'],
                ['Phulpur', 'ফুলপুর'], ['Trishal', 'ত্রিশাল'],
            ],
            // Jamalpur
            'jamalpur' => [
                ['Jamalpur Sadar', 'জামালপুর সদর'], ['Bakshiganj', 'বকশীগঞ্জ'],
                ['Dewanganj', 'দেওয়ানগঞ্জ'], ['Islampur', 'ইসলামপুর'],
                ['Madarganj', 'মাদারগঞ্জ'], ['Melandaha', 'মেলান্দহ'], ['Sarishabari', 'সরিষাবাড়ী'],
            ],
            // Sherpur
            'sherpur' => [
                ['Sherpur Sadar', 'শেরপুর সদর'], ['Jhenaigati', 'ঝিনাইগাতী'],
                ['Nakla', 'নকলা'], ['Nalitabari', 'নালিতাবাড়ী'], ['Sreebardi', 'শ্রীবরদী'],
            ],
            // Netrokona
            'netrokona' => [
                ['Netrokona Sadar', 'নেত্রকোণা সদর'], ['Atpara', 'আটপাড়া'],
                ['Barhatta', 'বারহাট্টা'], ['Durgapur', 'দুর্গাপুর'],
                ['Kalmakanda', 'কলমাকান্দা'], ['Kendua', 'কেন্দুয়া'],
                ['Khaliajuri', 'খালিয়াজুরী'], ['Madan', 'মদন'],
                ['Mohanganj', 'মোহনগঞ্জ'], ['Purbadhala', 'পূর্বধলা'],
            ],
            // Khagrachhari
            'khagrachhari' => [
                ['Khagrachhari Sadar', 'খাগড়াছড়ি সদর'], ['Dighinala', 'দিঘীনালা'],
                ['Guimara', 'গুইমারা'], ['Lakshmichhari', 'লক্ষ্মীছড়ি'],
                ['Mahalchhari', 'মহালছড়ি'], ['Manikchhari', 'মানিকছড়ি'],
                ['Matiranga', 'মাটিরাঙ্গা'], ['Panchhari', 'পানছড়ি'], ['Ramgarh', 'রামগড়'],
            ],
            // Rangamati
            'rangamati' => [
                ['Rangamati Sadar', 'রাঙামাটি সদর'], ['Bagaichhari', 'বাঘাইছড়ি'],
                ['Barkal', 'বরকল'], ['Belaichhari', 'বেলাইছড়ি'],
                ['Juraichhari', 'জুরাইছড়ি'], ['Kaptai', 'কাপ্তাই'],
                ['Kawkhali', 'কাউখালী'], ['Langadu', 'লংগদু'],
                ['Nandane Chhari', 'নানিয়ারচর'], ['Rajasthali', 'রাজস্থলী'],
            ],
            // Bandarban
            'bandarban' => [
                ['Bandarban Sadar', 'বান্দরবান সদর'], ['Alikadam', 'আলীকদম'],
                ['Lama', 'লামা'], ['Naikhongchhari', 'নাইক্ষ্যংছড়ি'],
                ['Rowangchhari', 'রোয়াংছড়ি'], ['Ruma', 'রুমা'], ['Thanchi', 'থানচি'],
            ],
        ];

        foreach ($areaData as $districtSlug => $areas) {
            if (!isset($districtIds[$districtSlug])) continue;
            $distId = $districtIds[$districtSlug];

            foreach ($areas as [$en, $bn]) {
                // Prefix slug with district slug to ensure global uniqueness
                $slug = $districtSlug . '-' . Str::slug($en);
                Area::firstOrCreate(
                    [
                        'district_id' => $distId,
                        'name->en'    => $en
                    ],
                    [
                        'slug' => $slug,
                        'name' => ['en' => $en, 'bn' => $bn],
                    ]
                );
            }
        }

        $this->command->info('✅ Bangladesh: 8 divisions, 64 districts, and all major areas/upazilas seeded!');
    }
}
