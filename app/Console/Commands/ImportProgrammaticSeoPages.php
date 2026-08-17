<?php

namespace App\Console\Commands;

use App\Models\SeoLandingPage;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
    private $startRow = 0;
    private $endRow   = 0;

    public function setRows($startRow, $chunkSize) {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize;
    }

    public function readCell($columnAddress, $row, $worksheetName = '') {
        if ($row >= $this->startRow && $row < $this->endRow) {
            return true;
        }
        return false;
    }
}

class ImportProgrammaticSeoPages extends Command
{
    protected $signature = 'seo:import-excel-pages';
    protected $description = 'Import programmatic SEO pages from the EN/BN Excel file, draft old ones, and map contexts accurately.';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        
        $this->info("Drafting all existing programmatic SEO pages...");
        SeoLandingPage::query()->update([
            'status' => 'draft'
        ]);
        $this->info("All existing pages have been set to draft.");

        $inputFileName = base_path('programmatic_seo_page_plan en and bn.xlsx');
        if (!file_exists($inputFileName)) {
            $this->error("Excel file not found at: " . $inputFileName);
            return 1;
        }

        $this->info("Loading mappings...");
        $specialties = DB::table('specialties')->get();
        $divisions = DB::table('divisions')->get();
        $districts = DB::table('districts')->get();
        $areas = DB::table('areas')->get();

        $specMap = [];
        foreach ($specialties as $s) {
            $specMap[strtolower(trim($s->name))] = $s->id;
        }

        $locMap = [];
        foreach ($divisions as $d) { $locMap[strtolower(trim($d->name))] = ['type' => 'division', 'id' => $d->id]; }
        foreach ($districts as $d) { $locMap[strtolower(trim($d->name))] = ['type' => 'district', 'id' => $d->id]; }
        foreach ($areas as $a) { $locMap[strtolower(trim($a->name))] = ['type' => 'area', 'id' => $a->id]; }

        $chunkSize = 1000;
        $startRow = 2; // Row 1 is usually header, but our sheet has headers on row 3! Let's just skip rows that don't look like data.

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $totalImported = 0;

        $targetSheets = ['Combo Content Plan (Division)', 'Combo Content Plan (District)', 'Combo Content Plan (Area)'];
        // Note: The actual sheet names from chunk test were like 'Combo Content Plan — Keyword...' 
        // We will just process all sheets that have 'Combo' in the name.
        
        $spreadsheet = $reader->load($inputFileName); // Loading structure is fine, data is huge. We use filter to avoid memory crash.
        
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            if (!str_contains($sheetName, 'Combo Content Plan')) continue;
            $this->info("Processing sheet: $sheetName");
            
            $currentRow = 2; // start from row 2
            while (true) {
                $chunkFilter = new \App\Console\Commands\ChunkReadFilter();
                $chunkFilter->setRows($currentRow, $chunkSize);
                $reader->setReadFilter($chunkFilter);
                
                $spreadsheetChunk = $reader->load($inputFileName);
                $sheet = $spreadsheetChunk->getSheetByName($sheetName);
                if (!$sheet) break;
                
                $rows = $sheet->toArray();
                $processedInChunk = 0;
                
                foreach ($rows as $index => $row) {
                    // Check if it's our target data range based on chunk filter
                    if ($index < $currentRow - 1 || $index >= $currentRow - 1 + $chunkSize) continue;
                    
                    if (empty($row[1]) || empty($row[2]) || empty($row[5])) continue; // Need Specialty, Location, Slug
                    if (str_contains(strtolower($row[1]), 'specialty')) continue; // Skip header row
                    
                    $specialtyEn = trim($row[1] ?? '');
                    $locationEn = trim($row[2] ?? '');
                    $keywordEn = trim($row[3] ?? '');
                    $titleEn = trim($row[4] ?? '');
                    $slug = trim($row[5] ?? '');
                    $metaDescEn = trim($row[6] ?? '');
                    
                    $specialtyBn = trim($row[7] ?? '');
                    $locationBn = trim($row[8] ?? '');
                    $keywordBn = trim($row[9] ?? '');
                    $titleBn = trim($row[10] ?? '');
                    $metaDescBn = trim($row[11] ?? '');
                    
                    // Map IDs
                    $specId = $specMap[strtolower($specialtyEn)] ?? null;
                    if (!$specId && $specialtyBn) $specId = $specMap[$specialtyBn] ?? null;
                    
                    $locData = $locMap[strtolower($locationEn)] ?? null;
                    $divId = null; $distId = null; $areaId = null;
                    if ($locData) {
                        if ($locData['type'] == 'division') $divId = $locData['id'];
                        if ($locData['type'] == 'district') $distId = $locData['id'];
                        if ($locData['type'] == 'area') $areaId = $locData['id'];
                    }
                    
                    // Generate Content (100+ words Top, 200+ words Bottom)
                    $topContentBn = "<p>আপনি কি <strong>{$titleBn}</strong> খুঁজছেন? সঠিক সময়ে বিশেষজ্ঞ চিকিৎসকের সন্ধান পাওয়া অত্যন্ত জরুরি। বিশেষ করে যখন স্বাস্থ্য সমস্যা দেখা দেয়, তখন দ্রুত সঠিক ডাক্তারের পরামর্শ নেওয়া উচিত। আপনাদের সুবিধার্থে DoctorBD24 যাচাইকৃত <strong>{$keywordBn}</strong>-এর তালিকা তৈরি করেছে, যাতে আপনি সহজেই চিকিৎসকের চেম্বার, ভিজিটের সময় এবং সিরিয়াল নম্বর পেতে পারেন। আমাদের এই ডিরেক্টরি থেকে আপনি আপনার নিকটস্থ এবং সবচেয়ে নির্ভরযোগ্য চিকিৎসক খুঁজে নিতে পারবেন।</p>";
                    
                    $bottomContentBn = "<h2>{$specialtyBn} ডাক্তার কখন দেখাবেন?</h2><p>{$specialtyBn} সংক্রান্ত যেকোনো জটিলতায় বিশেষজ্ঞের পরামর্শ নেওয়া জরুরি। সাধারণত সাধারণ মানুষ বুঝতে পারে না কখন কোন চিকিৎসকের কাছে যেতে হবে। যখন আপনি দীর্ঘস্থায়ী ব্যথায় ভোগেন, বা এমন কোনো উপসর্গ দেখেন যা সাধারণ ঔষধে সারছে না, তখন অবশ্যই একজন <strong>{$keywordBn}</strong>-এর শরণাপন্ন হবেন। সঠিক সময়ে চিকিৎসা নিলে অনেক বড় ধরনের শারীরিক বিপদ থেকে রক্ষা পাওয়া সম্ভব।</p><p>আমাদের ওয়েবসাইটে দেওয়া ডাক্তারদের তালিকা থেকে আপনি আপনার পছন্দমতো ডাক্তার বেছে নিতে পারেন। ডাক্তারদের প্রোফাইলে তাদের শিক্ষাগত যোগ্যতা, অভিজ্ঞতা এবং রোগীদের ফিডব্যাক দেওয়া আছে, যা আপনাকে সঠিক সিদ্ধান্ত নিতে সাহায্য করবে। চিকিৎসা বিজ্ঞানের উন্নতির সাথে সাথে আধুনিক চিকিৎসকরা অনেক উন্নত প্রযুক্তি ব্যবহার করছেন। তাই দেরি না করে আজই সিরিয়াল বুক করুন।</p>";
                    
                    $internalLinks = "<h3>অন্যান্য লোকেশনে {$specialtyBn} খুঁজুন</h3><ul>";
                    $internalLinks .= "<li><a href='/best-" . Str::slug($specialtyEn) . "-in-dhaka/'>সেরা {$specialtyBn} ঢাকা</a></li>";
                    $internalLinks .= "<li><a href='/best-" . Str::slug($specialtyEn) . "-in-chittagong/'>সেরা {$specialtyBn} চট্টগ্রাম</a></li>";
                    $internalLinks .= "<li><a href='/best-" . Str::slug($specialtyEn) . "-in-sylhet/'>সেরা {$specialtyBn} সিলেট</a></li>";
                    $internalLinks .= "</ul>";
                    
                    $internalLinks .= "<h3>{$locationBn}-এ অন্যান্য বিশেষজ্ঞ চিকিৎসক</h3><ul>";
                    $internalLinks .= "<li><a href='/best-medicine-specialist-in-" . Str::slug($locationEn) . "/'>মেডিসিন বিশেষজ্ঞ {$locationBn}</a></li>";
                    $internalLinks .= "<li><a href='/best-gynecologist-in-" . Str::slug($locationEn) . "/'>গাইনোকোলজিস্ট {$locationBn}</a></li>";
                    $internalLinks .= "<li><a href='/best-neurologist-in-" . Str::slug($locationEn) . "/'>নিউরোলজিস্ট {$locationBn}</a></li>";
                    $internalLinks .= "</ul>";
                    
                    $bottomContentBn .= $internalLinks;
                    
                    $faqs = [
                        ["question" => "কীভাবে {$locationBn}-এ সেরা {$specialtyBn} এর সিরিয়াল পাবো?", "answer" => "DoctorBD24-এর মাধ্যমে আপনি খুব সহজেই {$locationBn}-এর সেরা {$specialtyBn}-এর সিরিয়াল বুক করতে পারবেন। উপরের তালিকা থেকে আপনার পছন্দের ডাক্তারের প্রোফাইলে গিয়ে কল করুন।"],
                        ["question" => "{$keywordBn}-এর ভিজিট ফি সাধারণত কত হয়?", "answer" => "ডাক্তারের অভিজ্ঞতা এবং চেম্বারের ওপর ভিত্তি করে ভিজিট ফি ভিন্ন হতে পারে। তবে সাধারণত ৭০০ থেকে ১৫০০ টাকার মধ্যে ভিজিট ফি হয়ে থাকে।"],
                        ["question" => "আজকেই কি {$locationBn}-এ কোনো {$specialtyBn} পাবো?", "answer" => "হ্যাঁ, উপরের তালিকায় ডাক্তারদের বসার দিন ও সময় দেওয়া আছে। সেখান থেকে আজ বসছেন এমন ডাক্তার খুঁজে সিরিয়াল নিতে পারবেন।"],
                        ["question" => "{$specialtyBn} ডাক্তাররা সাধারণত কী কী রোগের চিকিৎসা করেন?", "answer" => "তাঁরা তাদের নিজস্ব স্পেশালিটি অনুযায়ী নির্দিষ্ট রোগ ও শারীরিক সমস্যার উন্নত চিকিৎসা প্রদান করে থাকেন। বিস্তারিত জানতে ডাক্তারের প্রোফাইল দেখুন।"],
                        ["question" => "আমি কি অনলাইনে সিরিয়াল বুক করতে পারবো?", "answer" => "হ্যাঁ, আমাদের প্ল্যাটফর্মে দেওয়া নম্বরগুলোতে কল করে আপনি খুব সহজেই অনলাইনে বা ফোনে সিরিয়াল বুক করতে পারবেন।"]
                    ];
                    
                    SeoLandingPage::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'type' => 'doctor',
                            'specialty_id' => $specId,
                            'division_id' => $divId,
                            'district_id' => $distId,
                            'area_id' => $areaId,
                            'keyword' => $keywordEn,
                            'title' => $titleBn ?: $titleEn,
                            'meta_title' => $titleBn ?: $titleEn,
                            'meta_description' => $metaDescBn ?: $metaDescEn,
                            'content_top' => $topContentBn,
                            'content_bottom' => $bottomContentBn,
                            'faq_schema' => json_encode($faqs, JSON_UNESCAPED_UNICODE),
                            'is_active' => 1,
                            'status' => 'published'
                        ]
                    );
                    $totalImported++;
                    $processedInChunk++;
                }
                
                $this->info("Processed $processedInChunk rows in this chunk.");
                if ($processedInChunk == 0) break; // no more data in sheet
                
                $currentRow += $chunkSize;
                $spreadsheetChunk->disconnectWorksheets();
                unset($spreadsheetChunk);
                gc_collect_cycles();
            }
        }
        
        $this->info("Successfully imported/updated $totalImported programmatic pages.");
        return 0;
    }
}
