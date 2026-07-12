<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class SeoLandingPageSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('seo:generate-programmatic-pages', [], $this->command ? $this->command->getOutput() : null);
    }
}
