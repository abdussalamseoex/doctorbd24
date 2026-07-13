<?php

namespace App\Console\Commands;

use App\Models\SeoLandingPage;
use Illuminate\Console\Command;

class GenerateProgrammaticSeoPages extends Command
{
    protected $signature = 'seo:generate-programmatic-pages {--tier1 : Generate 8 Division Pages} {--tier2 : Generate 64 District Pages} {--tier3 : Generate 624 Area Pages}';
    protected $description = 'Generate or update Programmatic SEO Landing Pages dynamically from the database with 1200+ words E-E-A-T content';

    public function handle()
    {
        $this->info("Starting generation/updating of Programmatic SEO Landing Pages...");

        $slugManifest = [];

        if ($this->option('tier1') || $this->option('tier2') || $this->option('tier3')) {
            if ($this->option('tier1')) {
                $divs = \Illuminate\Support\Facades\DB::table('divisions')->get();
                foreach ($divs as $div) {
                    $slugManifest[] = ['slug' => 'doctors-in-' . $div->slug, 'type' => 'doctor'];
                }
                $this->info("Loaded " . count($slugManifest) . " Division Location Pages (Tier 1) from DB.");
            }
            if ($this->option('tier2')) {
                $dists = \Illuminate\Support\Facades\DB::table('districts')->get();
                foreach ($dists as $dist) {
                    $slugManifest[] = ['slug' => 'doctors-in-' . $dist->slug, 'type' => 'doctor'];
                }
                $this->info("Loaded " . count($dists) . " District Location Pages (Tier 2) from DB.");
            }
            if ($this->option('tier3')) {
                $areas = \Illuminate\Support\Facades\DB::table('areas')->get();
                foreach ($areas as $area) {
                    $slugManifest[] = ['slug' => 'doctors-in-' . $area->slug, 'type' => 'doctor'];
                }
                $this->info("Loaded " . count($areas) . " Area Location Pages (Tier 3) from DB.");
            }
        } else {
            // Fallback to JSON if no flags provided
            $slugFile = database_path('seeders/seo_page_slugs.json');
            if (file_exists($slugFile)) {
                $slugManifest = json_decode(file_get_contents($slugFile), true);
                $this->info("Loaded " . count($slugManifest) . " pages from JSON manifest.");
            }
        }

        $bnLocMap = [
            'Mirpur' => 'মিরপুর', 'Dhanmondi' => 'ধানমন্ডি', 'Uttara' => 'উত্তরা', 'Gulshan' => 'গুলশান',
            'Banani' => 'বনানী', 'Motijheel' => 'মতিঝিল', 'Tejgaon' => 'তেজগাঁও', 'Badda' => 'বাড্ডা',
            'Mohammadpur' => 'মোহাম্মদপুর', 'Shyamoli' => 'শ্যামলী', 'Rampura' => 'রামপুরা',
            'Dhaka' => 'ঢাকা', 'Chattogram' => 'চট্টগ্রাম', 'Sylhet' => 'সিলেট', 'Rajshahi' => 'রাজশাহী',
            'Khulna' => 'খুলনা', 'Barishal' => 'বরিশাল', 'Rangpur' => 'রংপুর', 'Mymensingh' => 'ময়মনসিংহ',
            'Panthapath' => 'পান্থপথ', 'Green Road' => 'গ্রিন রোড', 'Savar' => 'সাভার', 'Agrabad' => 'আগ্রাবাদ',
            'Khilgaon' => 'খিলগাঁও', 'Ramna' => 'রমনা', 'Shahbagh' => 'শাহবাগ', 'Kafrul' => 'কাফরুল',
            'Pallabi' => 'পল্লবী', 'Jatrabari' => 'যাত্রাবাড়ী', 'Demra' => 'ডেমরা', 'Lalbagh' => 'লালবাগ',
            'Kotwali' => 'কোতোয়ালী', 'Wari' => 'ওয়ারী', 'Gendaria' => 'গেন্ডারিয়া', 'Hazaribagh' => 'হাজারীবাগ',
            'Adabar' => 'আদাবর', 'Sabujbagh' => 'সবুজবাগ', 'Cantonment' => 'ক্যান্টনমেন্ট', 'Airport' => 'এয়ারপোর্ট',
            'Khilkhet' => 'খিলক্ষেত', 'Vatara' => 'ভাটারা', 'Dakshinkhan' => 'দক্ষিণখান', 'Turag' => 'তুরাগ',
            'Halishahar' => 'হালিশহর', 'Nasirabad' => 'নাসিরাবাদ', 'Bakalia' => 'বাকলিয়া', 'Khulshi' => 'খুলশী',
            'Kotwali Ctg' => 'কোতোয়ালী চট্টগ্রাম', 'Hathazari' => 'হাটহাজারী', 'Boalkhali' => 'বোয়ালখালী',
            'Raozan' => 'রাউজান', 'Patiya' => 'পটিয়া', 'Sitakunda' => 'সীতাকুণ্ড', 'Mirsharai' => 'মীরসরাই',
            'Anwara' => 'আনোয়ারা', 'Chandanbaish' => 'চন্দনবাইশ', 'Shajahanpur' => 'শাহজাহানপুর',
            'Sherpur Bogura' => 'শেরপুর বগুড়া', 'Sadar Sylhet' => 'সিলেট সদর', 'Golapganj' => 'গোলাপগঞ্জ',
            'Beanibazar' => 'বিয়ানীবাজার', 'Zakiganj' => 'জকিগঞ্জ', 'Kanaighat' => 'কানাইঘাট',
            'Sadar Khulna' => 'খুলনা সদর', 'Daulatpur' => 'দৌলতপুর', 'Khalishpur' => 'খালিশপুর',
            'Sonadanga' => 'সোনাডাঙ্গা', 'Khan Jahan Ali' => 'খান জাহান আলী', 'Sadar Rajshahi' => 'রাজশাহী সদর',
            'Boalia' => 'বোয়ালিয়া', 'Motihar' => 'মতিহার', 'Rajpara' => 'রাজপাড়া',
            'Shah Makhdum' => 'শাহ মখদুম', 'Sadar Rangpur' => 'রংপুর সদর', 'Kotwali Rangpur' => 'কোতোয়ালী রংপুর',
            'Tajhat' => 'তাজহাট', 'Mahiganj' => 'মাহিগঞ্জ', 'Sadar Barishal' => 'বরিশাল সদর',
            'Kotwali Barishal' => 'কোতোয়ালী বরিশাল', 'Sadar Cumilla' => 'কুমিল্লা সদর',
            'Adarsha Sadar' => 'আদর্শ সদর', 'Sadar South' => 'সদর দক্ষিণ', 'Daudkandi' => 'দাউদকান্দি',
            'Laksam' => 'লাকসাম', 'Sadar Gazipur' => 'গাজীপুর সদর', 'Tongi' => 'টঙ্গী',
            'Kaliakair' => 'কালিয়াকৈর', 'Kapasia' => 'কাপাসিয়া', 'Sripur' => 'শ্রীপুর',
            'Sadar Narayanganj' => 'নারায়ণগঞ্জ সদর', 'Fatullah' => 'ফতুল্লা', 'Siddhirganj' => 'সিদ্ধিরগঞ্জ',
            'Rupganj' => 'রূপগঞ্জ', 'Sonargaon' => 'সোনারগাঁও', 'Bandar Narayanganj' => 'বন্দর নারায়ণগঞ্জ',
            'Sadar Coxs Bazar' => 'কক্সবাজার সদর', 'Ramu' => 'রামু', 'Ukhia' => 'উখিয়া',
            'Comilla' => 'কুমিল্লা', 'Cumilla' => 'কুমিল্লা', 'Sadar Comilla' => 'কুমিল্লা সদর', 'Comilla Sadar' => 'কুমিল্লা সদর', 'Cumilla Sadar' => 'কুমিল্লা সদর',
            'Narayanganj' => 'নারায়ণগঞ্জ', 'Narayanganj Sadar' => 'নারায়ণগঞ্জ সদর', 'Bandar' => 'বন্দর',
            'Coxs Bazar' => 'কক্সবাজার', 'Coxs Bazar Sadar' => 'কক্সবাজার সদর', 'Gazipur' => 'গাজীপুর', 'Gazipur Sadar' => 'গাজীপুর সদর',
            'Sylhet Sadar' => 'সিলেট সদর', 'Khulna Sadar' => 'খুলনা সদর', 'Rajshahi Sadar' => 'রাজশাহী সদর', 'Rangpur Sadar' => 'রংপুর সদর',
            'Barishal Sadar' => 'বরিশাল সদর', 'Jashore' => 'যশোর', 'Jessore' => 'যশোর', 'Bogura' => 'বগুড়া', 'Bogra' => 'বগুড়া', 'Barisal' => 'বরিশাল'
        ];

        // Step 1: Delete Zone pages and placeholder specialty-care-unit pages that have no SEO value
        $deletedZones = SeoLandingPage::where('slug', 'like', 'doctors-in-zone-%')->delete();
        $deletedHolders = SeoLandingPage::where('slug', 'like', 'specialty-care-unit-%')->delete();
        if ($deletedZones > 0 || $deletedHolders > 0) {
            $this->info("Deleted {$deletedZones} Zone pages and {$deletedHolders} placeholder specialty-care-unit pages.");
        }

        // Step 1.5: Fix spelling discrepancies (migrate cumilla -> comilla to match official DB spelling)
        $cumillaPages = SeoLandingPage::where('slug', 'like', '%cumilla%')->get();
        foreach ($cumillaPages as $cp) {
            $newSlug = str_replace('cumilla', 'comilla', $cp->slug);
            $cp->slug = $newSlug;
            $cp->save();
        }

        // Step 2: Remove any slugs NOT in the manifest (cleanup orphaned pages)
        // Disabled during phased tier execution to prevent deleting other valid pages!
        // $validSlugs = array_column($slugManifest, 'slug');
        // $deletedOrphans = SeoLandingPage::whereNotIn('slug', $validSlugs)->delete();
        // if ($deletedOrphans > 0) {
        //     $this->info("Deleted {$deletedOrphans} orphaned pages not in manifest.");
        // }

        $count = 0;

        foreach ($slugManifest as $manifest) {
            // CRITICAL FIX: Unset variables from previous iterations to prevent leakage!
            unset($specEn, $specBn, $descBn, $locEn, $locBn, $locBnPossessive, $rawTitleBn, $rawTitleEn);

            $slug = $manifest['slug'];
            $type = $manifest['type'] ?? (str_starts_with($slug, 'hospitals-in-') ? 'hospital' : 'doctor');
            $keyword = $manifest['keyword'] ?? '';

            // Build content based on slug prefix
            if (str_starts_with($slug, 'doctors-in-')) {
                $locEn = ucwords(str_replace('-', ' ', substr($slug, 11)));
                $locBn = $bnLocMap[$locEn] ?? $this->resolveBanglaLocationName($locEn, $bnLocMap);
                $locBnPossessive = $this->toBanglaPossessive($locBn);
                $keywordEn = "Doctors in {$locEn}";
                $keywordBn = "{$locBnPossessive} সেরা বিশেষজ্ঞ ডাক্তার তালিকা";
                $type = 'doctor';
                $contentTop    = ['en' => $this->buildEEATTopEnExact($locEn, $keywordEn), 'bn' => $this->buildEEATTopBnExact($locBn, $locBnPossessive, $keywordBn)];
                $contentBottom = ['en' => $this->buildEEATBottomEnExact($locEn, $keywordEn), 'bn' => $this->buildEEATBottomBnExact($locBn, $locBnPossessive, $keywordBn)];

            } elseif (str_starts_with($slug, 'hospitals-in-')) {
                $locEn = ucwords(str_replace('-', ' ', substr($slug, 13)));
                $locBn = $bnLocMap[$locEn] ?? $this->resolveBanglaLocationName($locEn, $bnLocMap);
                $locBnPossessive = $this->toBanglaPossessive($locBn);
                $keywordEn = "Hospitals in {$locEn}";
                $keywordBn = "{$locBnPossessive} হাসপাতাল তালিকা";
                $type = 'hospital';
                $contentTop    = ['en' => $this->buildEEATTopEnExact($locEn, $keywordEn), 'bn' => $this->buildEEATTopBnExact($locBn, $locBnPossessive, $keywordBn)];
                $contentBottom = ['en' => $this->buildEEATBottomEnExact($locEn, $keywordEn), 'bn' => $this->buildEEATBottomBnExact($locBn, $locBnPossessive, $keywordBn)];

            } else {
                // Specialty or Specialty-in-Location Page
                $specSlug = $slug;
                $locEn = "Bangladesh";
                $locBn = "বাংলাদেশ";
                $locBnPossessive = "বাংলাদেশের";

                if (str_contains($slug, '-in-')) {
                    $parts = explode('-in-', $slug, 2);
                    $specSlug = $parts[0];
                    $locSlug = $parts[1];
                    $locEn = ucwords(str_replace('-', ' ', $locSlug));
                    $locBn = $bnLocMap[$locEn] ?? $this->resolveBanglaLocationName($locEn, $bnLocMap);
                    $locBnPossessive = $this->toBanglaPossessive($locBn);
                }

                $specInfo = $this->getSpecialtyInfo($specSlug);
                $specEn = $specInfo['en'];
                $specBn = $specInfo['bn'];
                $descBn = $specInfo['desc_bn'];

                $keywordEn = "Best {$specEn} in {$locEn}";
                $keywordBn = "{$locBnPossessive} সেরা {$specBn}";
                $type = 'doctor';

                $contentTop    = ['en' => $this->buildSpecialtyTopEn($specEn, $locEn), 'bn' => $this->buildSpecialtyTopBn($specBn, $descBn, $locBn, $locBnPossessive)];
                $contentBottom = ['en' => $this->buildSpecialtyBottomEn($specEn, $locEn), 'bn' => $this->buildSpecialtyBottomBn($specBn, $locBn, $locBnPossessive)];
            }

            $rawTitleEn = is_array($manifest['title'] ?? null) ? ($manifest['title']['en'] ?? $manifest['title']) : ($manifest['title'] ?? "Best Doctors in {$locEn} - Specialist Chamber & Appointment");
            $rawTitleBn = is_array($manifest['title'] ?? null) ? ($manifest['title']['bn'] ?? null) : null;
            if (!$rawTitleBn) {
                if (isset($specBn)) {
                    $rawTitleBn = "{$locBnPossessive} সেরা {$specBn} তালিকা - চেম্বার ও সিরিয়াল";
                } elseif ($type === 'doctor') {
                    $rawTitleBn = "{$locBnPossessive} সেরা বিশেষজ্ঞ ডাক্তার তালিকা - চেম্বার ও সিরিয়াল";
                } elseif ($type === 'hospital') {
                    $rawTitleBn = "{$locBnPossessive} সেরা হাসপাতাল ও ডায়াগনস্টিক সেন্টার তালিকা";
                } else {
                    $rawTitleBn = "{$locBnPossessive} সেরা স্বাস্থ্যসেবা ডিরেক্টরি";
                }
            }

            $title = ['en' => $rawTitleEn, 'bn' => $rawTitleBn];
            $metaTitle = $title;
            $metaDescription = $manifest['meta_description'] ?? ['en' => '', 'bn' => ''];

            SeoLandingPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'type'             => $type,
                    'keyword'          => $keyword,
                    'title'            => $title,
                    'meta_title'       => $metaTitle,
                    'meta_description' => $metaDescription,
                    'content_top'      => $contentTop,
                    'content_bottom'   => $contentBottom,
                    'is_active'        => $manifest['is_active'] ?? 1,
                    'status'           => $manifest['status'] ?? 'published',
                ]
            );
            $count++;
        }

        $this->info("SUCCESS: {$count} Phase 1 Programmatic SEO Landing Pages synchronized!");

        // Step 3: Auto-map Data Context (specialty_id, area_id, district_id, division_id) from live DB
        $this->info("Auto-mapping Data Context from live database...");
        $mappedLocation = 0;
        $mappedSpecialty = 0;

        // Load specialties
        $specialties = \DB::table('specialties')->select('id', 'name', 'slug')->get();
        $specialtyMap = [];
        $pluralToSlugMap = [
            'cardiologists' => 'cardiology',
            'gynecologists' => 'gynaecology',
            'neurologists' => 'neurology',
            'pediatricians' => 'childpaediatrics',
            'orthopedic-surgeons' => 'orthopaedic-surgery',
            'pulmonologists' => 'chest-medicine',
            'rheumatologists' => 'rheumatology-medicine',
            'hematologists' => 'haematology',
            'physiotherapists' => 'physical-medicine',
            'sexologists' => 'skindermatology',
            'fertility-specialists' => 'infertility-gynae',
            'liver-specialists' => 'liver-medicine',
            'kidney-specialists' => 'nephrologykidney-medicine',
            'colorectal-surgeons' => 'colorectal-surgery',
            'thoracic-surgeons' => 'chest-medicine',
            'vascular-surgeons' => 'vascular-surgery',
            'pediatric-surgeons' => 'paediatric-surgery',
            'neonatologists' => 'neonatal',
            'allergists-immunologists' => 'medicine',
            'medicine-specialists' => 'medicine',
            'oncologists' => 'oncologycancer',
            'geriatricians' => 'medicine',
            'palliative-care-specialists' => 'medicine',
            'radiologists' => 'sonologist',
            'anesthesiologists' => 'pain-management',
        ];

        $locationAliases = [
            'adabar'            => 'adabor',
            'chattogram'        => 'chittagong',
            'cumilla'           => 'comilla',
            'jashore'           => 'jessore',
            'jhalokati'         => 'jhalokathi',
            'khagrachari'       => 'khagrachhari',
            'barisal'           => 'barishal',
            'bogra'             => 'bogura',
            'coxs-bazar'        => 'coxs bazar',
            'sadar-sylhet'      => 'sylhet sadar',
            'sadar sylhet'      => 'sylhet sadar',
            'sadar-khulna'      => 'khulna sadar',
            'sadar khulna'      => 'khulna sadar',
            'sadar-gazipur'     => 'gazipur sadar',
            'sadar gazipur'     => 'gazipur sadar',
            'sadar-narayanganj' => 'narayanganj sadar',
            'sadar narayanganj' => 'narayanganj sadar',
            'sadar-rajshahi'    => 'rajshahi sadar',
            'sadar rajshahi'    => 'rajshahi sadar',
            'sadar-rangpur'     => 'rangpur sadar',
            'sadar rangpur'     => 'rangpur sadar',
            'sadar-barishal'    => 'barishal sadar',
            'sadar barishal'    => 'barishal sadar',
            'sadar-cumilla'     => 'comilla sadar',
            'sadar cumilla'     => 'comilla sadar',
            'sadar-comilla'     => 'comilla sadar',
            'sadar comilla'     => 'comilla sadar',
            'sadar-coxs-bazar'  => "cox's bazar sadar",
            'sadar coxs bazar'  => "cox's bazar sadar",
            'cantonment'        => 'dhaka cantonment',
            'airport'           => 'airport area',
            'kotwali-ctg'       => 'kotwali',
            'kotwali ctg'       => 'kotwali',
            'kotwali-rangpur'   => 'kotwali',
            'kotwali rangpur'   => 'kotwali',
            'kotwali-barishal'  => 'kotwali',
            'kotwali barishal'  => 'kotwali',
            'sherpur-bogura'    => 'sherpur',
            'sherpur bogura'    => 'sherpur',
            'bandar-narayanganj'=> 'bandar',
            'bandar narayanganj'=> 'bandar',
            'sripur'            => 'sreepur',
            'sabujbagh'         => 'sabujbag',
            'green-road'        => 'green road',
            'gec-circle'        => 'gec circle',
        ];

        $locationToDistrictSlug = [
            'panthapath'       => 'dhaka',
            'green-road'       => 'dhaka',
            'green road'       => 'dhaka',
            'ramna'            => 'dhaka',
            'kafrul'           => 'dhaka',
            'pallabi'          => 'dhaka',
            'wari'             => 'dhaka',
            'gendaria'         => 'dhaka',
            'adabar'           => 'dhaka',
            'adabor'           => 'dhaka',
            'sabujbagh'        => 'dhaka',
            'sabujbag'         => 'dhaka',
            'cantonment'       => 'dhaka',
            'airport'          => 'dhaka',
            'khilkhet'         => 'dhaka',
            'vatara'           => 'dhaka',
            'dakshinkhan'      => 'dhaka',
            'turag'            => 'dhaka',
            'gec-circle'       => 'chittagong',
            'gec circle'       => 'chittagong',
            'chawkbazar'       => 'chittagong',
            'bakalia'          => 'chittagong',
            'khulshi'          => 'chittagong',
            'kotwali-ctg'      => 'chittagong',
            'hathazari'        => 'chittagong',
            'raozan'           => 'chittagong',
            'chandanbaish'     => 'bogura',
            'sherpur-bogura'   => 'bogura',
            'zindabazar'       => 'sylhet',
            'subidbazar'       => 'sylhet',
            'sadar-sylhet'     => 'sylhet',
            'sadar sylhet'     => 'sylhet',
            'sadar-khulna'     => 'khulna',
            'sadar khulna'     => 'khulna',
            'khalishpur'       => 'khulna',
            'sonadanga'        => 'khulna',
            'khan-jahan-ali'   => 'khulna',
            'khan jahan ali'   => 'khulna',
            'sadar-rajshahi'   => 'rajshahi',
            'sadar rajshahi'   => 'rajshahi',
            'boalia'           => 'rajshahi',
            'motihar'          => 'rajshahi',
            'rajpara'          => 'rajshahi',
            'shah-makhdum'     => 'rajshahi',
            'shah makhdum'     => 'rajshahi',
            'sadar-rangpur'    => 'rangpur',
            'sadar rangpur'    => 'rangpur',
            'tajhat'           => 'rangpur',
            'mahiganj'         => 'rangpur',
            'sadar-barishal'   => 'barishal',
            'sadar barishal'   => 'barishal',
            'adarsha-sadar'    => 'comilla',
            'adarsha sadar'    => 'comilla',
            'sadar-south'      => 'comilla',
            'sadar south'      => 'comilla',
            'fatullah'         => 'narayanganj',
            'siddhirganj'      => 'narayanganj',
        ];

        foreach ($specialties as $s) {
            $specialtyMap[strtolower($s->slug)] = $s->id;
        }

        // Load all areas, districts, divisions — names are stored as JSON {"en":"...","bn":"..."}
        $areas     = \DB::table('areas')->select('id', 'name', 'slug', 'district_id')->get();
        $districts = \DB::table('districts')->select('id', 'name', 'slug', 'division_id')->get();
        $divisions = \DB::table('divisions')->select('id', 'name', 'slug')->get();

        // Build lookup maps: lowercase EN name → record
        $areaMap      = [];
        $districtMap  = [];
        $divisionMap  = [];
        $districtById = [];

        $spellingSynonyms = [
            'chattogram' => 'chittagong', 'chittagong' => 'chattogram',
            'cumilla' => 'comilla', 'comilla' => 'cumilla',
            'jashore' => 'jessore', 'jessore' => 'jashore',
            'jhalokati' => 'jhalokathi', 'jhalokathi' => 'jhalokati',
            'khagrachari' => 'khagrachhari', 'khagrachhari' => 'khagrachari',
            'barisal' => 'barishal', 'barishal' => 'barisal',
            'bogra' => 'bogura', 'bogura' => 'bogra',
            'coxs-bazar' => 'coxs bazar', 'coxs bazar' => 'coxs-bazar',
        ];

        foreach ($areas as $a) {
            $decoded = json_decode($a->name, true);
            $enName  = strtolower(trim($decoded['en'] ?? $a->name));
            $areaMap[$enName] = $a;
            $areaMap[str_replace(' ', '-', $enName)] = $a;
            $areaMap[str_replace('-', ' ', $enName)] = $a;
            if (isset($spellingSynonyms[$enName])) {
                $areaMap[$spellingSynonyms[$enName]] = $a;
            }
            if ($a->slug) {
                $slugLower = strtolower($a->slug);
                $areaMap[$slugLower] = $a;
                $parts = explode('-', $slugLower);
                $lastSlug = end($parts);
                if (!isset($areaMap[$lastSlug])) {
                    $areaMap[$lastSlug] = $a;
                }
                if (isset($spellingSynonyms[$lastSlug])) {
                    $areaMap[$spellingSynonyms[$lastSlug]] = $a;
                }
            }
        }
        foreach ($districts as $d) {
            $decoded = json_decode($d->name, true);
            $enName  = strtolower(trim($decoded['en'] ?? $d->name));
            $districtMap[$enName] = $d;
            $districtMap[str_replace(' ', '-', $enName)] = $d;
            $districtMap[str_replace('-', ' ', $enName)] = $d;
            if (isset($spellingSynonyms[$enName])) {
                $districtMap[$spellingSynonyms[$enName]] = $d;
            }
            if ($d->slug) {
                $districtMap[strtolower($d->slug)] = $d;
            }
            $districtById[$d->id] = $d;
        }
        foreach ($divisions as $dv) {
            $decoded = json_decode($dv->name, true);
            $enName  = strtolower(trim($decoded['en'] ?? $dv->name));
            $divisionMap[$enName] = $dv;
            $divisionMap[str_replace(' ', '-', $enName)] = $dv;
            $divisionMap[str_replace('-', ' ', $enName)] = $dv;
            if (isset($spellingSynonyms[$enName])) {
                $divisionMap[$spellingSynonyms[$enName]] = $dv;
            }
            if ($dv->slug) {
                $divisionMap[strtolower($dv->slug)] = $dv;
            }
        }

        // Process each seo page
        $allPages = SeoLandingPage::all();
        foreach ($allPages as $page) {
            $slug = $page->slug;
            $locationName = null;
            $specialtyName = null;

            if (str_starts_with($slug, 'doctors-in-')) {
                $locationName = str_replace('-', ' ', substr($slug, 11));
                $page->type = 'doctor';
            } elseif (str_starts_with($slug, 'hospitals-in-')) {
                $locationName = str_replace('-', ' ', substr($slug, 13));
                $page->type = 'hospital';
            } else {
                $page->type = 'doctor';
                if (str_contains($slug, '-in-')) {
                    $parts = explode('-in-', $slug);
                    $specialtyName = $parts[0];
                    $locationName = str_replace('-', ' ', $parts[1]);
                } else {
                    $specialtyName = $slug;
                }
            }

            $pageUpdated = true; // Always save type update

            // Map Specialty
            if ($specialtyName) {
                $lookupSpecialty = $pluralToSlugMap[$specialtyName] ?? $specialtyName;
                if (isset($specialtyMap[$lookupSpecialty])) {
                    $page->specialty_id = $specialtyMap[$lookupSpecialty];
                    $mappedSpecialty++;
                }
            }

            if (!$locationName) {
                $page->save();
                continue;
            }

            $locationLower = strtolower($locationName);
            $lookupLoc = $locationAliases[$locationLower] ?? $locationLower;

            // Try DISTRICT match first (e.g. Dhaka, Chattogram/Chittagong, Sylhet, Rajshahi, Khulna, etc.)
            if (isset($districtMap[$lookupLoc])) {
                $district = $districtMap[$lookupLoc];
                $page->district_id = $district->id;
                $page->division_id = $district->division_id ?? null;
                $page->area_id     = null;
                $dec = json_decode($district->name, true);
                $officialName = trim($dec['en'] ?? $district->name);
                $this->syncPageSpelling($page, $locationName, $officialName);
                $page->save();
                $mappedLocation++;
                continue;
            }

            // Try DIVISION match second
            if (isset($divisionMap[$lookupLoc])) {
                $division = $divisionMap[$lookupLoc];
                $page->division_id = $division->id;
                $page->district_id = null;
                $page->area_id     = null;
                $dec = json_decode($division->name, true);
                $officialName = trim($dec['en'] ?? $division->name);
                $this->syncPageSpelling($page, $locationName, $officialName);
                $page->save();
                $mappedLocation++;
                continue;
            }

            // Try AREA match third (e.g. Adabor, Dhanmondi, Mirpur, Uttara, Gulshan, Banani, etc.)
            if (isset($areaMap[$lookupLoc])) {
                $area = $areaMap[$lookupLoc];
                $areaDistrictId = $area->district_id ?? null;
                $divisionId = null;
                if ($areaDistrictId && isset($districtById[$areaDistrictId])) {
                    $divisionId = $districtById[$areaDistrictId]->division_id ?? null;
                }
                $page->area_id     = $area->id;
                $page->district_id = $areaDistrictId;
                $page->division_id = $divisionId;
                $dec = json_decode($area->name, true);
                $officialName = trim($dec['en'] ?? $area->name);
                $this->syncPageSpelling($page, $locationName, $officialName);
                $page->save();
                $mappedLocation++;
                continue;
            }

            // Smart fallback: check if any area slug matches lookupLoc or suffix
            $matchedFallback = false;
            foreach ($areas as $a) {
                $aSlugLower = strtolower($a->slug);
                $cleanLocSlug = str_replace(' ', '-', $lookupLoc);
                if ($aSlugLower === $cleanLocSlug || str_ends_with($aSlugLower, '-' . $cleanLocSlug)) {
                    $areaDistrictId = $a->district_id ?? null;
                    $divisionId = null;
                    if ($areaDistrictId && isset($districtById[$areaDistrictId])) {
                        $divisionId = $districtById[$areaDistrictId]->division_id ?? null;
                    }
                    $page->area_id     = $a->id;
                    $page->district_id = $areaDistrictId;
                    $page->division_id = $divisionId;
                    $dec = json_decode($a->name, true);
                    $officialName = trim($dec['en'] ?? $a->name);
                    $this->syncPageSpelling($page, $locationName, $officialName);
                    $page->save();
                    $mappedLocation++;
                    $matchedFallback = true;
                    break;
                }
            }

            // Ultimate Fallback: Assign parent District & Division explicitly
            if (!$matchedFallback) {
                $fallbackDistKey = $locationToDistrictSlug[$locationLower] ?? ($locationToDistrictSlug[$lookupLoc] ?? null);
                if ($fallbackDistKey && isset($districtMap[$fallbackDistKey])) {
                    $dRec = $districtMap[$fallbackDistKey];
                    $page->district_id = $dRec->id;
                    $page->division_id = $dRec->division_id ?? null;
                    $page->save();
                    $mappedLocation++;
                } else {
                    $page->save();
                }
            }
        }

        $this->info("Data Context mapped for {$mappedLocation} Locations and {$mappedSpecialty} Specialties.");
        return 0;
    }

    protected function syncPageSpelling($page, $oldWord, $officialEnName)
    {
        if (!$oldWord || !$officialEnName || strtolower(trim($oldWord)) === strtolower(trim($officialEnName))) {
            return;
        }

        $oldSlugPart = str_replace(' ', '-', strtolower(trim($oldWord)));
        $newSlugPart = str_replace(' ', '-', strtolower(trim($officialEnName)));

        // 1. Update slug if it contains the old spelling and won't conflict with an existing page
        if (str_contains(strtolower($page->slug), $oldSlugPart) && $oldSlugPart !== $newSlugPart) {
            $candidateSlug = str_replace($oldSlugPart, $newSlugPart, strtolower($page->slug));
            if (!\App\Models\SeoLandingPage::where('slug', $candidateSlug)->where('id', '!=', $page->id)->exists()) {
                $page->slug = $candidateSlug;
            }
        }

        // 2. Update page title, meta_title, meta_description
        $fields = ['title', 'meta_title', 'meta_description'];
        foreach ($fields as $f) {
            if (!empty($page->{$f})) {
                $val = $page->{$f};
                $isJson = is_string($val) && str_starts_with(trim($val), '{');
                if ($isJson) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded) && isset($decoded['en'])) {
                        $decoded['en'] = preg_replace('/\b' . preg_quote($oldWord, '/') . '\b/i', $officialEnName, $decoded['en']);
                        $page->{$f} = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $page->{$f} = preg_replace('/\b' . preg_quote($oldWord, '/') . '\b/i', $officialEnName, $val);
                }
            }
        }
    }

    protected function resolveBanglaLocationName($locEn, $bnLocMap)
    {
        if (isset($bnLocMap[$locEn])) {
            return $bnLocMap[$locEn];
        }
        foreach ($bnLocMap as $k => $v) {
            if (strcasecmp($k, $locEn) === 0) {
                return $v;
            }
        }
        if (preg_match('/^Zone (\d+)$/i', $locEn, $m)) {
            return 'জোন ' . str_replace(['0','1','2','3','4','5','6','7','8','9'], ['০','১','২','৩','৪','৫','৬','৭','৮','৯'], $m[1]);
        }
        if (preg_match('/^(Sadar\s+)(.+)$/i', $locEn, $m)) {
            $subBn = $bnLocMap[trim($m[2])] ?? trim($m[2]);
            return $subBn . ' সদর';
        }
        if (preg_match('/^(.+)(\s+Sadar)$/i', $locEn, $m)) {
            $subBn = $bnLocMap[trim($m[1])] ?? trim($m[1]);
            return $subBn . ' সদর';
        }
        // DB fallback lookup
        $dbArea = \DB::table('areas')->where('slug', 'like', '%' . strtolower(str_replace(' ', '-', $locEn)) . '%')->first();
        if ($dbArea) {
            $dec = json_decode($dbArea->name, true);
            if (!empty($dec['bn'])) return trim($dec['bn']);
        }
        $dbDist = \DB::table('districts')->where('slug', 'like', '%' . strtolower(str_replace(' ', '-', $locEn)) . '%')->first();
        if ($dbDist) {
            $dec = json_decode($dbDist->name, true);
            if (!empty($dec['bn'])) return trim($dec['bn']);
        }
        return $locEn;
    }

    protected function toBanglaPossessive($bnWord)
    {
        $bnWord = trim($bnWord);
        $lastChar = mb_substr($bnWord, -1);
        if (mb_substr($bnWord, -2) === 'াও') {
            return $bnWord . 'য়ের';
        }
        if (in_array($lastChar, ['া', 'ি', 'ী', 'ু', 'ূ', 'ে', 'ো'])) {
            return $bnWord . 'র';
        }
        return $bnWord . 'ের';
    }

    protected function buildEEATTopEnExact($locEn, $keywordEn)
    {
        return <<<HTML
<div class="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed">
    <p class="text-base sm:text-lg">
        Securing prompt, dependable medical consultation begins with accessing verified specialist profiles, accurate private chamber locations, and real-time appointment serial contact numbers. As an independent and authoritative healthcare indexing platform in Bangladesh, DoctorBD24 compiles comprehensive directory listings under <strong>{$keywordEn}</strong> to assist patients and families in making informed healthcare choices without unnecessary delays or misinformation.
    </p>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        Complete Medical Directory Guide to Specialist Chambers in {$locEn}
    </h2>
    <p class="text-sm sm:text-base">
        Navigating private chambers across <strong>{$locEn}</strong> can present challenges when visiting hours, serial reservation windows, or physician credentials are not transparently available. Our directory solves this challenge by organizing verified profiles of consultants practicing across leading private diagnostic centers and specialist clinics. Whether you are searching for experienced <a href="/cardiologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Cardiologists</a>, skilled <a href="/gynecologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Gynecologists & Obstetricians</a>, trusted <a href="/pediatricians" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Pediatric Specialists</a>, or senior <a href="/neurologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Neurologists</a>, exploring our portal ensures accurate scheduling data.
    </p>
    <p class="text-sm sm:text-base">
        Every profile listed under <strong>{$keywordEn}</strong> provides essential clinical context, including recognized academic qualifications, current institutional hospital affiliations, standard visiting fees, and direct reception serial telephone lines. For families comparing options across adjacent metropolitan sectors, our portal also cross-links major medical hubs such as <a href="/doctors-in-dhaka" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Doctors in Dhaka</a>, <a href="/doctors-in-dhanmondi" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Dhanmondi Specialist Chambers</a>, and <a href="/doctors-in-mirpur" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Mirpur Doctor Directory</a>.
    </p>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white pt-2">
        BMDC Registration Standards & Evening Consultation Schedules
    </h3>
    <p class="text-sm sm:text-base">
        Under Google E-E-A-T (Experience, Expertise, Authoritativeness, and Trustworthiness) principles, verifying a doctor's professional licensing is paramount. Every physician featured in our directory undergoes verification against official qualification registries. Our listings highlight practitioners holding postgraduate degrees such as FCPS, MD, MS, or MRCP registered with the Bangladesh Medical & Dental Council (BMDC). Most consultants conduct evening practice sessions at private chambers in <strong>{$locEn}</strong> between 5:00 PM and 9:30 PM on regular weekdays after fulfilling institutional hospital responsibilities.
    </p>
    <p class="text-sm sm:text-base">
        Furthermore, patients and caregivers seeking specialized healthcare in <strong>{$locEn}</strong> benefit significantly from verifying chamber rules before departure. Many chambers require serial booking via phone at least twenty-four hours in advance. By consulting our structured directory, patients gain immediate access to verified phone numbers, visiting schedules, and fee structures, empowering them to select the most suitable specialist with complete confidence and peace of mind.
    </p>
</div>
HTML;
    }

    protected function buildEEATTopBnExact($locBn, $locBnPossessive, $keywordBn)
    {
        return <<<HTML
<div class="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed">
    <p class="text-base sm:text-lg">
        অসুস্থতার মুহূর্তে সঠিক ও অভিজ্ঞ চিকিৎসকের সন্ধান পাওয়া এবং সময়মতো চেম্বারের সিরিয়াল নেওয়া অনেক সময় বেশ কঠিন হয়ে পড়ে। বাংলাদেশের একটি নির্ভরযোগ্য, নিরপেক্ষ ও পূর্ণাঙ্গ স্বাস্থ্যসেবা ডিরেক্টরি ওয়েবসাইট হিসেবে DoctorBD24 আপনাদের জন্য সাজিয়েছে <strong>{$locBnPossessive} সেরা বিশেষজ্ঞ ডাক্তার তালিকা</strong>, যেখানে চেম্বারের সঠিক ঠিকানা, ভিজিট ফি এবং সরাসরি সিরিয়াল বুকিংয়ের হালনাগাদ ফোন নম্বর এক জায়গায় পাওয়া যায়।
    </p>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        {$locBnPossessive} বিশেষজ্ঞ ডাক্তারের চেম্বার ও সিরিয়াল বুকিং নির্দেশিকা
    </h2>
    <p class="text-sm sm:text-base">
        ডাক্তারের চেম্বার সময়সূচি বা ভিজিট ফি জানা না থাকলে রোগীদের দীর্ঘক্ষণ অপেক্ষা করতে হতে পারে। আমাদের ডিরেক্টরিতে <strong>{$locBnPossessive} বিভিন্ন বেসরকারি চেম্বার ও ক্লিনিকসমূহে</strong> নিয়মিত রোগী দেখা চিকিৎসকদের বিস্তারিত তথ্য একত্রিত করা হয়েছে। আপনি যদি আপনার পরিবারের জন্য অভিজ্ঞ <a href="/bn/cardiologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">হৃদরোগ ও কার্ডিওলজি বিশেষজ্ঞ</a>, প্রখ্যাত <a href="/bn/gynecologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">গাইনি ও প্রসূতি বিশেষজ্ঞ</a>, নির্ভরযোগ্য <a href="/bn/pediatricians" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">শিশু চিকিৎসক</a> কিংবা <a href="/bn/neurologists" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">নিউরোলজি বিশেষজ্ঞ</a> খোঁজেন, আমাদের পোর্টাল থেকে সহজেই চিকিৎসকের সময়সূচি জেনে নিতে পারবেন।
    </p>
    <p class="text-sm sm:text-base">
        আমাদের ডিরেক্টরিতে তালিকাভুক্ত প্রতিটি প্রোফালে চিকিৎসকের শিক্ষাগত যোগ্যতা, কর্মস্থল, ভিজিট ফি এবং চেম্বারের সরাসরি ফোন নম্বর দেওয়া থাকে। আশপাশের এলাকা বা অন্য জেলা থেকে আসা রোগীরা খুব সহজেই <strong>{$locBnPossessive} ডাক্তারদের</strong> পাশাপাশি <a href="/bn/doctors-in-dhaka" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">ঢাকার সেরা চিকিৎসক ডিরেক্টরি</a>, <a href="/bn/doctors-in-dhanmondi" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">ধানমন্ডির ডাক্তার চেম্বার</a> এবং <a href="/bn/doctors-in-mirpur" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">মিরপুর বিশেষজ্ঞ তালিকা</a> তুলনা করে সিরিয়ালের পরিকল্পনা করতে পারবেন।
    </p>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white pt-2">
        বিএমডিসি স্বীকৃত চিকিৎসক ও চেম্বার সময়সূচি
    </h3>
    <p class="text-sm sm:text-base">
        নিরাপদ ও সঠিক চিকিৎসার প্রধান শর্ত হলো যাচাইকৃত চিকিৎসক নির্বাচন করা। আমাদের ডিরেক্টরিতে তালিকাভুক্ত প্রতিটি প্রোফাইল বিএমডিসি (BMDC) রেজিস্ট্রেশন ও পেশাগত যোগ্যতার ভিত্তিতে উপস্থাপন করা হয়। এখানে এফসিপিএস, এমডি, এমএস বা এমআরসিপি উচ্চতর ডিগ্রিধারী চিকিৎসকরা স্থান পান। সাধারণত চিকিৎসকরা সরকারি বা প্রধান হাসপাতালের দায়িত্ব শেষে <strong>{$locBn}-এর চেম্বারগুলোতে</strong> বিকাল ৫টা থেকে রাত ৯:৩০ পর্যন্ত রোগী দেখে থাকেন।
    </p>
    <p class="text-sm sm:text-base">
        রোগীদের সুবিধার্থে আমরা পরামর্শ দিই যে চেম্বারে যাওয়ার পূর্বে অবশ্যই সরাসরি ফোন করে সিরিয়ালের সময় ও উপস্থিতি নিশ্চিত করে নিন। আমাদের ডিরেক্টরি ব্যবহার করে আপনারা কোনো ভুল বা পুরোনো তথ্য ছাড়াই আপনাদের কাঙ্ক্ষিত চিকিৎসকের সঠিক পরামর্শ গ্রহণ করতে সক্ষম হবেন।
    </p>
</div>
HTML;
    }

    protected function buildEEATBottomEnExact($locEn, $keywordEn)
    {
        return <<<HTML
<div class="space-y-6 text-gray-700 dark:text-gray-300 leading-relaxed pt-6 border-t border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        Comprehensive Patient Guide: How to Consult Specialist Doctors in {$locEn}
    </h2>
    <p class="text-sm sm:text-base">
        Choosing the right physician and preparing thoroughly for an out-patient consultation significantly improves clinical outcomes. When exploring profiles under <strong>{$keywordEn}</strong>, patients should consider not only geographical proximity but also the consultant's specific field of expertise, academic seniority, and hospital track record. In modern medical practice, clear communication between the patient and the physician during the initial consultation lays the groundwork for accurate diagnostic evaluation and sustainable therapeutic management.
    </p>
    <p class="text-sm sm:text-base">
        Our medical index emphasizes transparency by providing verifiable details regarding clinical background. Whether searching for specialized post-operative care or routine outpatient management, patients can confidently utilize our listings to identify practitioners aligned with their medical requirements across <strong>{$locEn}</strong> and surrounding healthcare centers.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        1. Evaluating Physician Qualifications & BMDC Licensing
    </h3>
    <p class="text-sm sm:text-base">
        In accordance with healthcare safety benchmarks, verifying a consultant's medical registration is essential before scheduling an appointment. Specialists listed in our directory hold primary undergraduate medical degrees (MBBS) followed by advanced postgraduate fellowships or master's degrees, such as Fellowship of the College of Physicians and Surgeons (FCPS), Doctor of Medicine (MD), Master of Surgery (MS), or Membership of the Royal Colleges of Physicians (MRCP). Every practicing consultant must maintain active registration with the Bangladesh Medical & Dental Council (BMDC). Patients can independently cross-reference registration numbers through official BMDC portals to ensure professional authenticity.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        2. Complete Patient Preparation Checklist Before Chamber Visits
    </h3>
    <p class="text-sm sm:text-base">
        To maximize the clinical value of an outpatient consultation in <strong>{$locEn}</strong>, patients are strongly advised to organize their medical documentation systematically. Arriving unprepared often results in incomplete clinical histories or redundant laboratory testing. Follow this structured preparation guide:
    </p>
    <ul class="list-disc pl-6 space-y-2 text-sm sm:text-base">
        <li><strong>Chronological Medical File:</strong> Compile all past prescriptions, discharge summaries, and operative notes in chronological order so the consultant can trace historical disease progression accurately.</li>
        <li><strong>Pathology & Laboratory Reports:</strong> Bring recent blood tests, kidney function profiles, liver panels, and urine examination records conducted within the past six months to assist evaluation.</li>
        <li><strong>Radiology & Imaging Scans:</strong> Carry physical films or digital CD copies of X-rays, ultrasonography scans, CT scans, and MRI evaluations rather than relying solely on printed text reports.</li>
        <li><strong>Current Medication List:</strong> Write down all active pharmaceuticals, including dosages, vitamins, and herbal supplements, to prevent adverse drug interactions during prescription.</li>
    </ul>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        3. Understanding Chamber Fees, Serial Booking & Reception Etiquette
    </h3>
    <p class="text-sm sm:text-base">
        Private medical chambers across <strong>{$locEn}</strong> operate strictly on appointment serial protocols to manage high patient volume efficiently. Consultation fees generally range depending on practitioner seniority, academic rank (such as Professor, Associate Professor, or Senior Consultant), and specialization frequency. When calling the clinic reception desk listed in our directory, always confirm the current first-visit fee and subsequent follow-up report review charges.
    </p>
    <p class="text-sm sm:text-base">
        Patients are encouraged to arrive at the chamber waiting room at least 20 to 30 minutes before their estimated serial number. Early presence allows clinic attendants to complete registration paperwork and record baseline physiological parameters, such as systolic/diastolic blood pressure, body weight, oxygen saturation, and pulse rate. Maintaining quiet decorum in chamber lobbies ensures an orderly environment for all attending patients and caregivers.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        4. Emergency Medical Protocol & 24/7 Ambulance & ICU Navigation
    </h3>
    <p class="text-sm sm:text-base">
        Private doctor chambers are designed for elective outpatient consultations and follow-up examinations; they do not provide acute life-support resuscitation or emergency inpatient admission. If a patient experiences acute chest pain, severe shortness of breath, sudden neurological stroke symptoms, or major trauma, immediate transfer to a specialized emergency hospital is required. Families can consult our verified <a href="/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Hospital Directory</a> for institutions offering 24-hour emergency departments, adult Intensive Care Units (ICU), and Coronary Care Units (CCU). For urgent patient transportation, access our dedicated <a href="/ambulances" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">24/7 Emergency Ambulance Service Directory</a>.
    </p>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white pt-4">
        Frequently Asked Questions (FAQ) About Specialist Consultations
    </h2>
    <div class="space-y-4 text-sm sm:text-base">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">How do I book a consultation serial with specialists in {$locEn}?</h4>
            <p class="text-gray-600 dark:text-gray-400">Select your required specialist from our directory listing, check visiting days, and dial the direct chamber booking line listed on the physician profile during official clinic reception hours.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Does DoctorBD24 provide direct clinical treatments or medical diagnostics?</h4>
            <p class="text-gray-600 dark:text-gray-400">No. DoctorBD24 serves purely as an independent healthcare directory and medical index, connecting patients across Bangladesh with verified chamber details, hospital locations, and serial booking contacts.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">What is the typical difference between first-visit and report follow-up fees?</h4>
            <p class="text-gray-600 dark:text-gray-400">First-visit fees cover comprehensive diagnostic evaluation and initial prescription planning. Report follow-up visits conducted within a stipulated timeframe (typically 7 to 14 days) often involve a discounted fee or waived charge depending on individual chamber policy.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Can I verify whether a doctor possesses genuine postgraduate degrees?</h4>
            <p class="text-gray-600 dark:text-gray-400">Yes. Patients can verify higher medical degrees (FCPS, MD, MS) and active licensing through the official Bangladesh Medical & Dental Council (BMDC) practitioner database.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">What should I do if a doctor's evening chamber schedule changes unexpectedly?</h4>
            <p class="text-gray-600 dark:text-gray-400">Chamber schedules may occasionally shift due to emergency surgery or hospital duties. Always call the clinic reception line on the afternoon of your appointment to reconfirm serial timing before departing.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Why is it important to consult specialists within local chambers in {$locEn}?</h4>
            <p class="text-gray-600 dark:text-gray-400">Consulting specialists locally in <strong>{$locEn}</strong> minimizes travel fatigue for elderly or critically ill patients and ensures convenient access for ongoing follow-up consultations and prescription management.</p>
        </div>
    </div>
</div>
HTML;
    }

    protected function buildEEATBottomBnExact($locBn, $locBnPossessive, $keywordBn)
    {
        return <<<HTML
<div class="space-y-6 text-gray-700 dark:text-gray-300 leading-relaxed pt-6 border-t border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        রোগীদের জন্য পূর্ণাঙ্গ গাইড: {$locBnPossessive} বিশেষজ্ঞ ডাক্তার দেখানোর নিয়ম ও প্রস্তুতি
    </h2>
    <p class="text-sm sm:text-base">
        সঠিক ও অভিজ্ঞ চিকিৎসক নির্বাচন এবং চেম্বারে যাওয়ার পূর্বে প্রয়োজনীয় প্রস্তুতি গ্রহণ করলে চিকিৎসার মান বহুগুণ বৃদ্ধি পায়। আপনারা যখন আমাদের ডিরেক্টরি থেকে <strong>{$keywordBn}</strong> অনুসন্ধান করেন, তখন শুধুমাত্র চেম্বারের দূরত্ব বিবেচনা না করে চিকিৎসকের বিশেষায়িত ক্ষেত্র, অভিজ্ঞতা এবং শিক্ষাগত যোগ্যতা যাচাই করে নেওয়া উচিত। আধুনিক চিকিৎসাবিজ্ঞানে রোগীর রোগনির্ণয় ও সঠিক চিকিৎসার জন্য প্রথম সাক্ষাতে চিকিৎসকের সাথে স্পষ্ট ও খোলামেলা আলোচনা অত্যন্ত গুরুত্বপূর্ণ ভূমিকা পালন করে।
    </p>
    <p class="text-sm sm:text-base">
        আমাদের স্বাস্থ্যসেবা ডিরেক্টরি প্রতিটি চিকিৎসকের শিক্ষাগত ও পেশাগত তথ্য নিরপেক্ষভাবে উপস্থাপন করে। আপনি অস্ত্রোপচার পরবর্তী ফলোআপ কিংবা সাধারণ চিকিৎসার জন্য চিকিৎসক খুঁজছেন কি না, আমাদের তালিকা থেকে <strong>{$locBnPossessive} সেরা চিকিৎসকদের</strong> চেম্বার ঠিকানা ও সময়সূচি জেনে সঠিক সিদ্ধান্ত নিতে পারেন।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ১. চিকিৎসকের পেশাগত যোগ্যতা ও বিএমডিসি (BMDC) স্বীকৃতি যাচাই
    </h3>
    <p class="text-sm sm:text-base">
        নিরাপদ ও আধুনিক চিকিৎসাসেবা নিশ্চিত করতে চিকিৎসকের সরকারি স্বীকৃতি যাচাই করা অপরিহার্য। আমাদের ডিরেক্টরিতে তালিকাভুক্ত চিকিৎসকরা এমবিবিএস (MBBS) ডিগ্রি অর্জনের পর স্বনামধন্য প্রতিষ্ঠান থেকে এফসিপিএস (FCPS), এমডি (MD), এমএস (MS) কিংবা এমআরসিপি (MRCP) উচ্চতর ডিগ্রি অর্জন করেছেন। বাংলাদেশে চিকিৎসাসেবা প্রদানের জন্য প্রতিটি চিকিৎসকের বাংলাদেশ মেডিকেল অ্যান্ড ডেন্টাল কাউন্সিল (BMDC) রেজিস্ট্রেশন থাকা বাধ্যতামূলক। রোগীরা চাইলে বিএমডিসির সরকারি ওয়েবসাইট থেকে চিকিৎসকের রেজিস্ট্রেশন নম্বর যাচাই করে নিতে পারেন।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ২. চেম্বারে যাওয়ার পূর্বে রোগীর করণীয় ও কাগজপত্র গোছানোর চেকলিস্ট
    </h3>
    <p class="text-sm sm:text-base">
        <strong>{$locBnPossessive} বিশেষজ্ঞ ডাক্তারদের</strong> চেম্বারে চিকিৎসকের পরামর্শ নেওয়ার সময় সর্বোচ্চ উপকার পেতে রোগীর পূর্ববর্তী সকল চিকিৎসার কাগজপত্র গুছিয়ে নেওয়া উচিত। কাগজপত্র অগোছালো থাকলে চিকিৎসককে রোগের ইতিহাস বুঝতে সময় বেশি লাগে এবং অনেক সময় অপ্রয়োজনীয় টেস্ট করাতে হয়। নিচে একটি আদর্শ চেকলিস্ট দেওয়া হলো:
    </p>
    <ul class="list-disc pl-6 space-y-2 text-sm sm:text-base">
        <li><strong>কালানুক্রমিক প্রেসক্রিপশন ফাইল:</strong> রোগীর আগের সব প্রেসক্রিপশন ও হাসপাতালের ছাড়পত্র তারিখ অনুযায়ী গুছিয়ে রাখুন, যাতে চিকিৎসক রোগের অগ্রগতি সহজেই বুঝতে পারেন।</li>
        <li><strong>প্যাথলজি ও ল্যাব রিপোর্ট:</strong> বিগত ৬ মাসের রক্ত পরীক্ষা, কিডনি বা লিভার ফাংশন টেস্ট এবং প্রস্রাব পরীক্ষার মূল রিপোর্ট সাথে রাখুন।</li>
        <li><strong>রেডিওলজি ও ইমেজিং স্ক্যান:</strong> এক্স-রে, আল্ট্রাসাউন্ড, সিটি স্ক্যান বা এমআরআই পরীক্ষার শুধুমাত্র লিখিত রিপোর্ট নয়, মূল ফিল্ম বা সিডি সাথে নিয়ে যান।</li>
        <li><strong>বর্তমান ওষুধের তালিকা:</strong> রোগী বর্তমানে যে সকল ওষুধ, ভিটামিন বা ইনসুলিন ব্যবহার করছেন, তার সঠিক নাম ও মাত্রা লিখে সাথে রাখুন।</li>
    </ul>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ৩. ভিজিট ফি, সিরিয়াল বুকিং নিয়ম ও চেম্বার শিষ্টাচার
    </h3>
    <p class="text-sm sm:text-base">
        <strong>{$locBnPossessive} বেসরকারি চেম্বারগুলোতে</strong> রোগীর ভিড় সুশৃঙ্খলভাবে পরিচালনার জন্য সিরিয়াল বুকিং পদ্ধতি অনুসরণ করা হয়। চিকিৎসকের পদমর্যাদা (যেমন: অধ্যাপক, সহযোগী অধ্যাপক বা সিনিয়র কনসালট্যান্ট) অনুযায়ী ভিজিট ফি নির্ধারণ করা থাকে। আমাদের ডিরেক্টরিতে দেওয়া চেম্বারের নম্বরে কল করার সময় প্রথম ভিজিট ফি এবং রিপোর্ট দেখানোর ফলোআপ ফি সম্পর্কে জেনে নেওয়া ভালো।
    </p>
    <p class="text-sm sm:text-base">
        নির্ধারিত সিরিয়াল সময়ের অন্তত ২০ থেকে ৩০ মিনিট পূর্বে চেম্বারে পৌঁছানোর চেষ্টা করুন। এতে রিসেপশনে নাম এন্ট্রি করা এবং রোগীর রক্তচাপ (Blood Pressure), ওজন ও পালস মাপার কাজ সুশৃঙ্খলভাবে সম্পন্ন হয়।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ৪. জরুরি চিকিৎসাসেবা, আইসিইউ ও ২৪ ঘণ্টার অ্যাম্বুলেন্স গাইড
    </h3>
    <p class="text-sm sm:text-base">
        ব্যক্তিগত চেম্বারগুলো মূলত সাধারণ ও পূর্বনির্ধারিত পরামর্শের জন্য পরিচালিত হয়; এখানে মুমূর্ষু রোগীর লাইফ সাপোর্ট বা জরুরি ভর্তির ব্যবস্থা থাকে না। রোগীর হঠাৎ বুকে তীব্র ব্যথা, শ্বাসকষ্ট, স্ট্রোক বা বড় কোনো দুর্ঘটনা ঘটলে কালক্ষেপণ না করে দ্রুত জরুরি বিভাগযুক্ত হাসপাতালে নিয়ে যেতে হবে। আপনারা আমাদের <a href="/bn/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">হাসপাতাল ও ডায়াগনস্টিক ডিরেক্টরি</a> থেকে ২৪ ঘণ্টা আইসিইউ (ICU), এনআইসিইউ ও জরুরি সেবা প্রদানকারী হাসপাতালের ঠিকানা পেতে পারেন। জরুরি রোগী পরিবহনে আমাদের <a href="/bn/ambulances" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">২৪ ঘণ্টার অ্যাম্বুলেন্স সার্ভিস ডিরেক্টরি</a> দেখুন।
    </p>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white pt-4">
        বিশেষজ্ঞ চিকিৎসক ও সিরিয়াল বুকিং সংক্রান্ত সাধারণ জিজ্ঞাসা (FAQ)
    </h2>
    <div class="space-y-4 text-sm sm:text-base">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">কীভাবে DoctorBD24 থেকে {$locBnPossessive} ডাক্তারের সিরিয়াল বুক করব?</h4>
            <p class="text-gray-600 dark:text-gray-400">আমাদের ডিরেক্টরি থেকে আপনার কাঙ্ক্ষিত চিকিৎসক নির্বাচন করুন এবং চিকিৎসকের প্রোফাইলে দেওয়া চেম্বারের সরাসরি ফোন নম্বরে নির্ধারিত সময়ে কল করে সিরিয়াল কনফার্ম করুন।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">DoctorBD24 কি সরাসরি কোনো চিকিৎসা সেবা বা ল্যাব টেস্ট প্রদান করে?</h4>
            <p class="text-gray-600 dark:text-gray-400">না। DoctorBD24 একটি নিরপেক্ষ স্বাস্থ্যসেবা ডিরেক্টরি ও তথ্যভাণ্ডার। আমরা বাংলাদেশের চিকিৎসক, চেম্বার ও হাসপাতালের সঠিক তথ্য দিয়ে রোগীদের সাহায্য করি।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">নতুন ভিজিট এবং রিপোর্ট দেখানোর ফি-এর মধ্যে পার্থক্য কী?</h4>
            <p class="text-gray-600 dark:text-gray-400">প্রথম ভিজিটে চিকিৎসক রোগীর বিস্তারিত ইতিহাস শুনে প্রেসক্রিপশন ও প্রয়োজনীয় টেস্ট দেন। নির্দিষ্ট সময়ের মধ্যে (সাধারণত ৭ থেকে ১৪ দিন) রিপোর্ট দেখাতে গেলে অনেক চেম্বারে ফি মওকুফ বা ছাড় দেওয়া হয়।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">চিকিৎসকের বিএমডিসি (BMDC) রেজিস্ট্রেশন যাচাই করা সম্ভব কি?</h4>
            <p class="text-gray-600 dark:text-gray-400">হ্যাঁ। বাংলাদেশ মেডিকেল অ্যান্ড ডেন্টাল কাউন্সিলের সরকারি ওয়েবসাইট থেকে যেকোনো চিকিৎসকের রেজিস্ট্রেশন নম্বর ও ডিগ্রি যাচাই করা সম্ভব।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">হঠাৎ চিকিৎসকের চেম্বার সময় পরিবর্তন হলে কী করণীয়?</h4>
            <p class="text-gray-600 dark:text-gray-400">জরুরি অস্ত্রোপচার বা হাসপাতালের দায়িত্বের কারণে কখনো কখনো চেম্বার সময় পরিবর্তন হতে পারে। তাই চেম্বারে রওয়ানা হওয়ার পূর্বে রিসেপশনে কল করে সময় নিশ্চিত করে নেওয়া উচিত।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">কেন নিজ এলাকার চেম্বার থেকে বিশেষজ্ঞ ডাক্তার দেখানো সুবিধাজনক?</h4>
            <p class="text-gray-600 dark:text-gray-400">নিজ এলাকায় বা <strong>{$locBnPossessive} চেম্বারে</strong> ডাক্তার দেখালে রোগীর যাতায়াত কষ্ট কমে যায় এবং পরবর্তী ফলোআপ ও পরামর্শের জন্য সহজেই চিকিৎসকের সাথে যোগাযোগ রাখা সম্ভব হয়।</p>
        </div>
    </div>
</div>
HTML;
    }

    protected function getSpecialtyInfo($slug)
    {
        $map = [
            'cardiologists' => ['en' => 'Cardiologists & Heart Specialists', 'bn' => 'হৃদরোগ ও কার্ডিওলজি বিশেষজ্ঞ', 'desc_bn' => 'বুকের ব্যথা, হার্ট অ্যাটাক, উচ্চ রক্তচাপ বা হার্ট ব্লকের মতো জটিল সমস্যায় অভিজ্ঞ কার্ডিওলজিস্ট ও হার্ট স্পেশালিস্টদের পরামর্শ ও চেম্বার সিরিয়াল।'],
            'gynecologists' => ['en' => 'Gynecologists & Obstetricians', 'bn' => 'গাইনি ও প্রসূতি রোগ বিশেষজ্ঞ', 'desc_bn' => 'গর্ভাবস্থায় যত্ন, প্রসূতি সেবা, অনিয়মিত মাসিক এবং নারীদের জটিল সমস্যা সমাধানে দেশের শীর্ষ গাইনি ও প্রসূতি বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'pediatricians' => ['en' => 'Pediatric & Child Specialists', 'bn' => 'শিশু ও নবজাতক রোগ বিশেষজ্ঞ', 'desc_bn' => 'শিশুদের জ্বর, নিউমোনিয়া, অপুষ্টি, বৃদ্ধিজনিত সমস্যা বা নবজাতকের যেকোনো জটিলতায় অভিজ্ঞ শিশু বিশেষজ্ঞদের সিরিয়াল।'],
            'neurologists' => ['en' => 'Neurologists & Brain Specialists', 'bn' => 'স্নায়ুরোগ ও নিউরোলজি বিশেষজ্ঞ', 'desc_bn' => 'মাথাব্যথা, মাইগ্রেন, স্ট্রোক, মৃগী রোগ, প্যারালাইসিস ও স্নায়বিক সমস্যায় দেশের শীর্ষ নিউরোলজিস্টদের চেম্বার ও সিরিয়াল।'],
            'medicine-specialists' => ['en' => 'Internal Medicine Specialists', 'bn' => 'ইন্টারনাল মেডিসিন বিশেষজ্ঞ', 'desc_bn' => 'জ্বর, ডায়াবেটিস, উচ্চ রক্তচাপ এবং শরীরের যেকোনো জটিল বা অস্পষ্ট শারীরিক সমস্যায় মেডিসিন বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'orthopedic-surgeons' => ['en' => 'Orthopedic & Bone Specialists', 'bn' => 'হাড়, জোড়া ও বাতব্যথা (অর্থোপেডিক) বিশেষজ্ঞ', 'desc_bn' => 'হাড় ভাঙা, জয়েন্ট পেইন, মেরুদণ্ডের ব্যথা বা আর্থ্রাইটিস সমস্যায় অভিজ্ঞ অর্থোপেডিক সার্জনদের চেম্বার ও সিরিয়াল।'],
            'dermatologists' => ['en' => 'Dermatologists & Skin Specialists', 'bn' => 'চর্ম, যৌন ও অ্যালার্জি রোগ বিশেষজ্ঞ', 'desc_bn' => 'ত্বকের দাগ, চুল পড়া, অ্যালার্জি, একজিমা এবং যেকোনো চর্ম ও যৌন সমস্যার চিকিৎসায় অভিজ্ঞ ডার্মাটোলজিস্টদের সিরিয়াল।'],
            'ent-specialists' => ['en' => 'ENT (Ear, Nose & Throat) Specialists', 'bn' => 'নাক, কান ও গলা (ইএনটি) বিশেষজ্ঞ', 'desc_bn' => 'কানে কম শোনা, সাইনাস, টনসিল বা গলার যেকোনো সমস্যায় নাক-কান-গলা বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'urologists' => ['en' => 'Urology Specialists', 'bn' => 'কিডনি, মূত্রনালী ও ইউরোলজি বিশেষজ্ঞ', 'desc_bn' => 'মূত্রনালীর ইনফেকশন, প্রস্টেট সমস্যা বা কিডনির পাথরে অভিজ্ঞ ইউরোলজিস্টদের চেম্বার ও সিরিয়াল।'],
            'nephrologists' => ['en' => 'Nephrologists & Kidney Specialists', 'bn' => 'কিডনি রোগ (নেফ্রোলজি) বিশেষজ্ঞ', 'desc_bn' => 'কিডনি বিকল, ডায়ালাইসিস এবং জটিল কিডনি সমস্যায় দেশের শীর্ষ নেফ্রোলজিস্টদের চেম্বার ও সিরিয়াল।'],
            'gastroenterologists' => ['en' => 'Gastroenterologists & Liver Specialists', 'bn' => 'পেট, লিভার ও পরিপাকতন্ত্র (গ্যাস্ট্রোএন্টারোলজি) বিশেষজ্ঞ', 'desc_bn' => 'গ্যাস্ট্রিক, আলসার, লিভার বা পরিপাকতন্ত্রের যেকোনো জটিল সমস্যায় অভিজ্ঞ গ্যাস্ট্রোএন্টারোলজিস্টদের সিরিয়াল।'],
            'psychiatrists' => ['en' => 'Psychiatrists & Mental Health Specialists', 'bn' => 'মানসিক রোগ ও মনোরোগ (সাইকিয়াট্রি) বিশেষজ্ঞ', 'desc_bn' => 'ডিপ্রেশন, উদ্বেগ, ওসিডি বা মানসিক স্বাস্থ্যের যেকোনো সমস্যায় অভিজ্ঞ মনোরোগ বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'oncologists' => ['en' => 'Cancer Specialists (Oncology)', 'bn' => 'ক্যান্সার ও টিউমার (অনকোলজি) বিশেষজ্ঞ', 'desc_bn' => 'ক্যান্সার নির্ণয়, কেমোথেরাপি এবং রেডিওথেরাপিতে অভিজ্ঞ ক্যান্সার বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'endocrinologists' => ['en' => 'Endocrinology & Diabetes Specialists', 'bn' => 'ডায়াবেটিস ও হরমোন রোগ (এন্ডোক্রাইনোলজি) বিশেষজ্ঞ', 'desc_bn' => 'ডায়াবেটিস, থাইরয়েড ও হরমোনজনিত যেকোনো জটিলতায় অভিজ্ঞ এন্ডোক্রাইনোলজিস্টদের চেম্বার ও সিরিয়াল।'],
            'pulmonologists' => ['en' => 'Pulmonology & Chest Specialists', 'bn' => 'বক্ষব্যাধি, হাঁপানি ও ফুসফুস বিশেষজ্ঞ', 'desc_bn' => 'হাঁপানি, শ্বাসকষ্ট, নিউমোনিয়া বা ফুসফুসের যেকোনো রোগে অভিজ্ঞ বক্ষব্যাধি বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'ophthalmologists' => ['en' => 'Eye Specialists & Surgeons', 'bn' => 'চক্ষু রোগ বিশেষজ্ঞ ও সার্জন', 'desc_bn' => 'চোখে ঝাপসা দেখা, ছানি পড়া বা যেকোনো চোখের সমস্যায় দেশের শীর্ষ চক্ষু বিশেষজ্ঞদের চেম্বার ও সিরিয়াল।'],
            'dentists' => ['en' => 'Dental Surgeons & Specialists', 'bn' => 'দন্ত রোগ বিশেষজ্ঞ ও ডেন্টাল সার্জন', 'desc_bn' => 'দাঁতের ব্যথা, রুট ক্যানেল বা ডেন্টাল ইমপ্ল্যান্টে অভিজ্ঞ ডেন্টিস্ট ও ডেন্টাল সার্জনদের চেম্বার ও সিরিয়াল।'],
            'general-surgeons' => ['en' => 'General Surgeons', 'bn' => 'জেনারেল ও ল্যাপারোস্কোপিক সার্জন', 'desc_bn' => 'অ্যাপেন্ডিসাইটিস, গলব্লাডার পাথর, হার্নিয়া ও যেকোনো অস্ত্রোপচারে অভিজ্ঞ সার্জনদের চেম্বার ও সিরিয়াল।'],
            'neurosurgeons' => ['en' => 'Neurosurgeons', 'bn' => 'মস্তিষ্ক ও মেরুদণ্ড সার্জন (নিউরসার্জন)', 'desc_bn' => 'ব্রেন টিউমার, স্পাইনাল কর্ড ইনজুরি ও মেরুদণ্ডের অস্ত্রোপচারে অভিজ্ঞ নিউরোসার্জনদের সিরিয়াল।'],
            'plastic-surgeons' => ['en' => 'Plastic & Cosmetic Surgeons', 'bn' => 'প্লাস্টিক ও কসমেটিক সার্জন', 'desc_bn' => 'পোড়া ঘা, কসমেটিক সার্জারি ও পুনর্গঠনমূলক অস্ত্রোপচারে অভিজ্ঞ প্লাস্টিক সার্জনদের চেম্বার ও সিরিয়াল।'],
            'rheumatologists' => ['en' => 'Rheumatology Specialists', 'bn' => 'বাত রোগ ও রিউমাটোলজি বিশেষজ্ঞ', 'desc_bn' => 'রিউমাটয়েড আর্থ্রাইটিস, লুপাস বা যেকোনো জটিল বাতের ব্যথায় অভিজ্ঞ রিউমাটোলজিস্টদের সিরিয়াল।'],
            'hematologists' => ['en' => 'Hematologists', 'bn' => 'রক্তরোগ (হেমাটোলজি) বিশেষজ্ঞ', 'desc_bn' => 'রক্তশূন্যতা, থ্যালাসেমিয়া বা রক্তের যেকোনো জটিল রোগে অভিজ্ঞ হেমাটোলজিস্টদের চেম্বার ও সিরিয়াল।'],
            'pain-medicine-specialists' => ['en' => 'Pain Medicine Specialists', 'bn' => 'পেইন মেডিসিন ও দীর্ঘস্থায়ী ব্যথা বিশেষজ্ঞ', 'desc_bn' => 'দীর্ঘস্থায়ী কোমর ব্যথা, ঘাড় ব্যথা ও নার্ভের ব্যথায় অভিজ্ঞ পেইন ম্যানেজমেন্ট বিশেষজ্ঞদের সিরিয়াল।'],
            'physiotherapists' => ['en' => 'Physiotherapists & Rehab Specialists', 'bn' => 'ফিজিওথেরাপি ও রিহ্যাবিলিটেশন বিশেষজ্ঞ', 'desc_bn' => 'প্যারালাইসিস বা আঘাত পরবর্তী পুনর্বাসন ও ফিজিওথেরাপির জন্য অভিজ্ঞ বিশেষজ্ঞদের সিরিয়াল।'],
            'nutritionists-dietitians' => ['en' => 'Nutritionists & Dietitians', 'bn' => 'পুষ্টিবিদ ও ডায়েট বিশেষজ্ঞ', 'desc_bn' => 'ওজন নিয়ন্ত্রণ, ডায়াবেটিস ডায়েট ও সঠিক পুষ্টি পরিকল্পনার জন্য অভিজ্ঞ পুষ্টিবিদদের পরামর্শ।'],
            'sexologists' => ['en' => 'Sexologists', 'bn' => 'যৌন স্বাস্থ্য ও সেক্সোলজি বিশেষজ্ঞ', 'desc_bn' => 'যৌন স্বাস্থ্য সমস্যা ও দাম্পত্য জীবনের যেকোনো জটিলতায় অভিজ্ঞ সেক্সোলজিস্টদের পরামর্শ।'],
            'fertility-specialists' => ['en' => 'Infertility & IVF Specialists', 'bn' => 'বন্ধ্যাত্ব ও আইভিএফ (IVF) বিশেষজ্ঞ', 'desc_bn' => 'সন্তানহীনতা বা বন্ধ্যাত্ব সমস্যায় অভিজ্ঞ ও সফল ফার্টিলিটি ও আইভিএফ বিশেষজ্ঞদের চেম্বার সিরিয়াল।'],
            'liver-specialists' => ['en' => 'Hepatologists & Liver Specialists', 'bn' => 'লিভার ও হেপাটাইটিস রোগ বিশেষজ্ঞ', 'desc_bn' => 'জন্ডিস, হেপাটাইটিস, ফ্যাটি লিভার বা লিভার সিরোসিসে অভিজ্ঞ হেপাটোলজিস্টদের সিরিয়াল।'],
            'kidney-specialists' => ['en' => 'Kidney Specialists', 'bn' => 'কিডনি রোগ বিশেষজ্ঞ', 'desc_bn' => 'কিডনি রোগ ও ডায়ালাইসিস ব্যবস্থাপনায় অভিজ্ঞ কিডনি স্পেশালিস্টদের চেম্বার সিরিয়াল।'],
            'colorectal-surgeons' => ['en' => 'Colorectal Surgeons', 'bn' => 'পাইলস, ফিস্টুলা ও কোলোরেক্টাল সার্জন', 'desc_bn' => 'পাইলস, ফিশার, ফিস্টুলা ও মলদ্বারের যেকোনো সমস্যায় অভিজ্ঞ কোলোরেক্টাল সার্জনদের সিরিয়াল।']
        ];

        if (isset($map[$slug])) {
            return $map[$slug];
        }

        $fallbackEn = ucwords(str_replace('-', ' ', $slug));
        return [
            'en' => $fallbackEn,
            'bn' => $fallbackEn . ' বিশেষজ্ঞ',
            'desc_bn' => "{$fallbackEn} সম্পর্কিত যেকোনো জটিল শারীরিক সমস্যায় দেশের অভিজ্ঞ ও শীর্ষ চিকিৎসকদের চেম্বার ও সিরিয়াল তথ্য।"
        ];
    }

    protected function buildSpecialtyTopEn($specEn, $locEn)
    {
        return <<<HTML
<div class="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed">
    <p class="text-base sm:text-lg">
        Finding an experienced and verified <strong>{$specEn}</strong> is crucial for accurate diagnosis and effective long-term clinical management. DoctorBD24 provides an authoritative and structured medical directory compiling leading practitioners across private chambers, specialized clinics, and diagnostic centers in <strong>{$locEn}</strong>, complete with direct serial phone numbers and visiting schedules.
    </p>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        Complete Medical Directory Guide to Verified {$specEn} in {$locEn}
    </h2>
    <p class="text-sm sm:text-base">
        Navigating specialist appointments can be challenging when visiting hours, consultation fees, or doctor credentials are not transparently published. Our directory solves this challenge by organizing verified profiles of senior consultants practicing across premier medical institutions. Whether you require prompt consultation or second surgical opinions, exploring our portal ensures accurate scheduling data and verified contact lines.
    </p>
    <p class="text-sm sm:text-base">
        Every profile listed under <strong>{$specEn}</strong> details essential clinical context, including academic qualifications (MBBS, FCPS, MD, MS, MRCP), hospital affiliations, standard visiting fees, and direct reception serial numbers. Patients can also explore our complete <a href="/doctors" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">National Doctor Directory</a>, compare diagnostic facilities via our <a href="/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Hospital Directory</a>, or browse specialized healthcare articles in our <a href="/blog" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Medical Blog</a>.
    </p>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white pt-2">
        Why Consult a Specialized Consultant Early?
    </h3>
    <p class="text-sm sm:text-base">
        Early evaluation by a qualified specialist prevents disease progression and minimizes long-term complications. By connecting patients directly with recognized medical authorities, DoctorBD24 eliminates intermediary delays and empowers families to make informed healthcare decisions across <strong>{$locEn}</strong>.
    </p>
</div>
HTML;
    }

    protected function buildSpecialtyTopBn($specBn, $descBn, $locBn, $locBnPossessive)
    {
        return <<<HTML
<div class="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed">
    <p class="text-base sm:text-lg">
        শরীরের যেকোনো জটিল বা দীর্ঘস্থায়ী অসুস্থতায় সঠিক রোগের জন্য উপযুক্ত বিশেষজ্ঞ চিকিৎসক নির্বাচন করা অত্যন্ত গুরুত্বপূর্ণ। DoctorBD24-এর এই বিশেষ ডিরেক্টরিতে আপনাদের জন্য একত্রিত করা হয়েছে দেশের শীর্ষ ও যাচাইকৃত <strong>{$specBn}</strong>-দের তালিকা ও চেম্বার সিরিয়াল তথ্য। {$descBn}
    </p>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        {$locBnPossessive} সেরা {$specBn} ও চেম্বার সিরিয়াল বুকিং নির্দেশিকা
    </h2>
    <p class="text-sm sm:text-base">
        ডাক্তারের চেম্বার সময়সূচি বা ভিজিট ফি জানা না থাকলে রোগীদের দীর্ঘক্ষণ অপেক্ষা করতে হতে পারে। আমাদের ডিরেক্টরিতে <strong>{$specBn}</strong> বিভাগে নিয়মিত রোগী দেখা চিকিৎসকদের বিস্তারিত তথ্য একত্রিত করা হয়েছে। আপনি যদি আপনার পরিবারের জন্য অভিজ্ঞ চিকিৎসকের সন্ধান করেন, আমাদের পোর্টাল থেকে সহজেই চেম্বারের সময়সূচি ও সিরিয়াল নম্বর জেনে নিতে পারবেন।
    </p>
    <p class="text-sm sm:text-base">
        আমাদের ডিরেক্টরিতে তালিকাভুক্ত প্রতিটি প্রোফালে চিকিৎসকের শিক্ষাগত যোগ্যতা (এমবিবিএস, এফসিপিএস, এমডি, এমএস), কর্মস্থল, ভিজিট ফি এবং চেম্বারের সরাসরি ফোন নম্বর দেওয়া থাকে। রোগীরা চাইলে আমাদের <a href="/bn/doctors" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">জাতীয় ডাক্তার তালিকা</a>, বেসরকারি চিকিৎসা প্রতিষ্ঠানের জন্য <a href="/bn/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">হাসপাতাল ডিরেক্টরি</a> অথবা স্বাস্থ্য সচেতনতামূলক আর্টিকেলের জন্য আমাদের <a href="/bn/blog" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">মেডিকেল ব্লগ</a> ভিজিট করতে পারেন।
    </p>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white pt-2">
        বিএমডিসি স্বীকৃত চিকিৎসক ও প্রাথমিক পরামর্শের গুরুত্ব
    </h3>
    <p class="text-sm sm:text-base">
        নিরাপদ ও সঠিক চিকিৎসার প্রধান শর্ত হলো যাচাইকৃত চিকিৎসক নির্বাচন করা। আমাদের ডিরেক্টরিতে তালিকাভুক্ত প্রতিটি প্রোফাইল বিএমডিসি (BMDC) রেজিস্ট্রেশন ও পেশাগত যোগ্যতার ভিত্তিতে উপস্থাপন করা হয়। রোগের প্রাথমিক পর্যায়েই একজন অভিজ্ঞ <strong>{$specBn}</strong>-এর পরামর্শ নিলে জটিলতা ও চিকিৎসার খরচ উভয়ই অনেকাংশে হ্রাস পায়।
    </p>
</div>
HTML;
    }

    protected function buildSpecialtyBottomEn($specEn, $locEn)
    {
        return <<<HTML
<div class="space-y-6 text-gray-700 dark:text-gray-300 leading-relaxed pt-6 border-t border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        Comprehensive Clinical Guide: Preparing for Your {$specEn} Consultation
    </h2>
    <p class="text-sm sm:text-base">
        A successful consultation with a <strong>{$specEn}</strong> requires structured preparation. Physicians evaluate complex symptomatology by combining detailed patient clinical history with targeted physical examinations and diagnostic tests. Arriving prepared allows your consultant to make accurate clinical assessments without unnecessary diagnostic delays.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        1. Essential Diagnostic Reports & Document Checklist
    </h3>
    <p class="text-sm sm:text-base">
        Before visiting a specialist chamber in <strong>{$locEn}</strong>, organize your medical records in chronological order. Bring all previous discharge notes, operative summaries, and prescription histories. Relevant pathology tests—such as complete blood counts, metabolic profiles, imaging scans (X-ray, Ultrasonography, CT, MRI), or specialized biopsies—should be carried in their original film or digital formats.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        2. Understanding Consultation Fees & Serial Etiquette
    </h3>
    <p class="text-sm sm:text-base">
        Consultation fees for a senior <strong>{$specEn}</strong> typically vary depending on academic qualification (Professor, Associate Professor, or Senior Consultant) and clinical experience. When calling the clinic reception desk listed in our directory, always confirm the first-visit consultation charge and follow-up report review policies. We advise arriving at the waiting room 20 to 30 minutes prior to your scheduled serial number.
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        3. Emergency Medical Navigation & Hospital Support
    </h3>
    <p class="text-sm sm:text-base">
        Private specialist chambers operate exclusively for scheduled outpatient consultations and follow-up reviews. If a patient encounters acute life-threatening emergencies—such as acute chest distress, respiratory collapse, severe hemorrhage, or neurological trauma—immediate transfer to a 24-hour emergency hospital is vital. Families can explore our <a href="/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Verified Hospital Directory</a> or contact immediate transport via our <a href="/ambulances" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">24/7 Ambulance Service Directory</a>.
    </p>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white pt-4">
        Frequently Asked Questions (FAQ) About {$specEn}
    </h2>
    <div class="space-y-4 pt-2">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">How do I book a serial appointment with a {$specEn}?</h4>
            <p class="text-gray-600 dark:text-gray-400">You can call the verified chamber reception phone number provided on each doctor profile listed on DoctorBD24 to reserve your appointment serial.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">What should I bring to my first specialist consultation?</h4>
            <p class="text-gray-600 dark:text-gray-400">Always bring your chronological prescription history, previous diagnostic reports, active medication lists, and valid identification.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Are all specialists listed on DoctorBD24 verified by BMDC?</h4>
            <p class="text-gray-600 dark:text-gray-400">Yes, every practitioner featured in our directory holds recognized postgraduate qualifications registered with the Bangladesh Medical & Dental Council (BMDC).</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">What is the standard consultation fee for a {$specEn}?</h4>
            <p class="text-gray-600 dark:text-gray-400">Consultation fees generally range between BDT 800 and BDT 2,000 depending on physician seniority and institutional rank.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Is follow-up report review free of charge?</h4>
            <p class="text-gray-600 dark:text-gray-400">Many chambers offer discounted or complimentary report review sessions within a specific window (usually 7 to 14 days after the initial visit).</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">Can I consult a {$specEn} for pediatric or elderly family members?</h4>
            <p class="text-gray-600 dark:text-gray-400">Yes, our directory includes sub-specialists experienced in treating pediatric, adult, and geriatric patient demographics.</p>
        </div>
    </div>
</div>
HTML;
    }

    protected function buildSpecialtyBottomBn($specBn, $locBn, $locBnPossessive)
    {
        return <<<HTML
<div class="space-y-6 text-gray-700 dark:text-gray-300 leading-relaxed pt-6 border-t border-gray-200 dark:border-gray-700">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        রোগীদের জন্য পূর্ণাঙ্গ গাইড: একজন অভিজ্ঞ {$specBn} দেখানোর প্রস্তুতি ও নিয়মাবলী
    </h2>
    <p class="text-sm sm:text-base">
        সঠিক ও অভিজ্ঞ চিকিৎসক নির্বাচন এবং চেম্বারে যাওয়ার পূর্বে প্রয়োজনীয় প্রস্তুতি গ্রহণ করলে চিকিৎসার মান বহুগুণ বৃদ্ধি পায়। আপনারা যখন আমাদের ডিরেক্টরি থেকে <strong>{$specBn}</strong> অনুসন্ধান করেন, তখন শুধুমাত্র চেম্বারের দূরত্ব বিবেচনা না করে চিকিৎসকের বিশেষায়িত ক্ষেত্র, অভিজ্ঞতা এবং শিক্ষাগত যোগ্যতা যাচাই করে নেওয়া উচিত।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ১. প্রয়োজনীয় ডায়াগনস্টিক রিপোর্ট ও প্রেসক্রিপশন প্রস্তুতি
    </h3>
    <p class="text-sm sm:text-base">
        চেম্বারে যাওয়ার পূর্বে রোগীর অতীতের সকল প্রেসক্রিপশন, হাসপাতালের ছাড়পত্র এবং ডায়াগনস্টিক রিপোর্ট ক্রমানুসারে গুছিয়ে রাখুন। রক্ত পরীক্ষা, এক্স-রে, আল্ট্রাসাউন্ড বা এমআরআই রিপোর্ট সাথে থাকলে চিকিৎসক খুব দ্রুত ও সঠিকভাবে রোগের মূল কারণ নির্ণয় করতে সক্ষম হন।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ২. ভিজিট ফি, সিরিয়াল বুকিং ও চেম্বার শৃঙ্খলা
    </h3>
    <p class="text-sm sm:text-base">
        বেসরকারি চেম্বারগুলোতে চিকিৎসকের পদবী ও অভিজ্ঞতার ওপর ভিত্তি করে ভিজিট ফি নির্ধারিত হয়। আমাদের ডিরেক্টরিতে দেওয়া ফোন নম্বরে যোগাযোগ করে প্রথম ভিজিট ও রিপোর্ট দেখানোর ফি নিশ্চিত করে নিন। নির্ধারিত সিরিয়াল সময়ের অন্তত ২০-৩০ মিনিট পূর্বে চেম্বারে উপস্থিত থাকা বাঞ্ছনীয়।
    </p>

    <h3 class="text-xl font-bold text-gray-900 dark:text-white pt-2">
        ৩. জরুরি চিকিৎসা ও হাসপাতাল সহায়তা
    </h3>
    <p class="text-sm sm:text-base">
        বিশেষজ্ঞ চিকিৎসকদের চেম্বার মূলত নিয়মিত ও নির্ধারিত রোগী দেখার জন্য পরিচালিত হয়। হঠাৎ তীব্র শ্বাসকষ্ট, বুকে ব্যথা বা বড় কোনো আঘাতের মতো জরুরি অবস্থায় রোগীকে কালক্ষেপণ না করে নিকটস্থ ২৪ ঘণ্টার জরুরি হাসপাতালে স্থানান্তর করা উচিত। প্রয়োজনে আমাদের <a href="/bn/hospitals" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">হাসপাতাল ডিরেক্টরি</a> অথবা <a href="/bn/ambulances" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">জরুরি অ্যাম্বুলেন্স ডিরেক্টরি</a> থেকে সাহায্য নিতে পারেন।
    </p>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-white pt-4">
        {$specBn} সম্পর্কিত সাধারণ জিজ্ঞাসা (FAQ)
    </h2>
    <div class="space-y-4 pt-2">
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">কীভাবে একজন অভিজ্ঞ {$specBn}-এর সিরিয়াল বুক করব?</h4>
            <p class="text-gray-600 dark:text-gray-400">DoctorBD24 পোর্টালে তালিকাভুক্ত চিকিৎসকের প্রোফাইলে দেওয়া চেম্বারের সরাসরি রিসেপশন নম্বরে ফোন করে সহজেই সিরিয়াল বুক করা যায়।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">প্রথম সাক্ষাতে ডাক্তারের কাছে কী কী কাগজপত্র নেওয়া প্রয়োজন?</h4>
            <p class="text-gray-600 dark:text-gray-400">রোগীর পূর্ববর্তী সকল প্রেসক্রিপশন, প্যাথলজি বা ইমেজিং রিপোর্ট এবং চলমান ওষুধের তালিকা সাথে নেওয়া উচিত।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">DoctorBD24-এর তালিকাভুক্ত চিকিৎসকরা কি বিএমডিসি স্বীকৃত?</h4>
            <p class="text-gray-600 dark:text-gray-400">হ্যাঁ, আমাদের ডিরেক্টরিতে তালিকাভুক্ত প্রতিটি চিকিৎসক বাংলাদেশ মেডিকেল অ্যান্ড ডেন্টাল কাউন্সিল (BMDC) রেজিস্ট্রেশনপ্রাপ্ত উচ্চতর ডিগ্রিধারী।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">একজন {$specBn}-এর ভিজিট ফি সাধারণত কত হয়?</h4>
            <p class="text-gray-600 dark:text-gray-400">চিকিৎসকের অভিজ্ঞতা ও পদবীর ওপর ভিত্তি করে ভিজিট ফি সাধারণত ৮০০ টাকা থেকে ২,০০০ টাকা পর্যন্ত হয়ে থাকে।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">রিপোর্ট দেখানোর জন্য কি আলাদা ফি দিতে হয়?</h4>
            <p class="text-gray-600 dark:text-gray-400">অধিকাংশ চেম্বারেই নির্দিষ্ট সময়ের মধ্যে (সাধারণত ৭-১৪ দিন) রিপোর্ট দেখাতে গেলে কোনো ফি নেওয়া হয় না বা ছাড় দেওয়া হয়।</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white">শিশু ও বয়স্ক রোগীদের জন্য কি আলাদা পরামর্শ পাওয়া যাবে?</h4>
            <p class="text-gray-600 dark:text-gray-400">হ্যাঁ, আমাদের তালিকায় শিশু, প্রাপ্তবয়স্ক এবং বয়োজ্যেষ্ঠ রোগীদের জন্য অভিজ্ঞ চিকিৎসকদের তথ্য আলাদাভাবে উল্লেখ থাকে।</p>
        </div>
    </div>
</div>
HTML;
    }
}
