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
        $this->info("User requested to delete all generated SEO pages. Deleting...");
        
        // Truncate the table to remove all 150,000+ pages instantly
        DB::table('seo_landing_pages')->truncate();
        
        $this->info("Successfully deleted all programmatic SEO pages.");
        return 0;
    }
}
