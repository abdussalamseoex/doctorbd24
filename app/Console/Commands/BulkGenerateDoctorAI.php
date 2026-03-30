<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Jobs\GenerateDoctorAIContentJob;
use Illuminate\Console\Command;

class BulkGenerateDoctorAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:generate-doctors {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to bulk generate AI bio and SEO meta for doctors missing them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info("Fetching up to {$limit} doctors without bio or SEO descriptions...");

        // Find doctors where bio is null/empty OR seo description is null/empty
        $doctors = Doctor::whereNull('bio')
            ->orWhere('bio', '')
            ->orWhereDoesntHave('seoMeta')
            ->orWhereHas('seoMeta', function($query) {
                $query->whereNull('description')->orWhere('description', '');
            })
            ->take($limit)
            ->get();

        if ($doctors->isEmpty()) {
            $this->info('No doctors found requiring AI generation.');
            return;
        }

        $this->info("Found {$doctors->count()} doctors. Dispatching jobs...");

        $bar = $this->output->createProgressBar($doctors->count());

        foreach ($doctors as $doctor) {
            GenerateDoctorAIContentJob::dispatch($doctor);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All jobs dispatched to the queue successfully.');
    }
}
