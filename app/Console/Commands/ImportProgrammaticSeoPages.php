<?php

namespace App\Console\Commands;

use App\Models\SeoLandingPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProgrammaticSeoPages extends Command
{
    protected $signature = 'seo:import-excel-pages';
    protected $description = 'Import programmatic SEO pages securely from the bundled ZIP file.';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        
        $this->info("Drafting all existing programmatic SEO pages...");
        SeoLandingPage::query()->update(['status' => 'draft', 'is_active' => 0]);
        
        $zipPath = base_path('programmatic_seo_export.zip');
        $jsonlPath = base_path('programmatic_seo_export.jsonl');
        
        if (!file_exists($zipPath)) {
            $this->error("Export bundle not found: $zipPath");
            return 1;
        }

        $this->info("Extracting update bundle...");
        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo(base_path(), ['programmatic_seo_export.jsonl']);
            $zip->close();
        } else {
            $this->error("Failed to extract ZIP bundle.");
            return 1;
        }

        if (!file_exists($jsonlPath)) {
            $this->error("JSONL file not found after extraction.");
            return 1;
        }

        $this->info("Importing 40,000+ pages. This is extremely fast...");
        
        $fp = fopen($jsonlPath, 'r');
        $chunk = [];
        $totalImported = 0;
        
        DB::connection()->disableQueryLog();
        
        while (($line = fgets($fp)) !== false) {
            $data = json_decode($line, true);
            if (!$data) continue;
            
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $chunk[] = $data;
            
            if (count($chunk) >= 500) {
                SeoLandingPage::upsert($chunk, ['slug'], ['type', 'specialty_id', 'division_id', 'district_id', 'area_id', 'keyword', 'title', 'meta_title', 'meta_description', 'content_top', 'content_bottom', 'faq_schema', 'is_active', 'status', 'updated_at']);
                $totalImported += count($chunk);
                $this->info("Imported $totalImported pages...");
                $chunk = [];
            }
        }
        
        if (count($chunk) > 0) {
            SeoLandingPage::upsert($chunk, ['slug'], ['type', 'specialty_id', 'division_id', 'district_id', 'area_id', 'keyword', 'title', 'meta_title', 'meta_description', 'content_top', 'content_bottom', 'faq_schema', 'is_active', 'status', 'updated_at']);
            $totalImported += count($chunk);
            $this->info("Imported $totalImported pages...");
        }
        
        fclose($fp);
        unlink($jsonlPath);

        $this->info("Successfully imported/updated $totalImported programmatic pages!");
        return 0;
    }
}
