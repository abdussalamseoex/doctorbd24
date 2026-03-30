<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;

class FixScrapedImages extends Command
{
    protected $signature = 'db:fix-scraped-images';
    protected $description = 'Retroactively download images for scraped doctors missing photos';

    public function handle()
    {
        $doctors = Doctor::whereNull('photo')->get();
        if ($doctors->isEmpty()) {
            $this->info("No doctors missing photos found.");
            return;
        }

        $this->info("Found " . $doctors->count() . " doctors missing photos. Attempting to fetch...");

        $client = new Client(['verify' => false, 'timeout' => 10]);

        $count = 0;
        foreach ($doctors as $doctor) {
            $url = 'https://www.doctorbangladesh.com/' . $doctor->slug . '/';
            $this->line("Fetching HTML for {$doctor->name}...");
            
            try {
                $html = $client->request('GET', $url)->getBody()->getContents();
                $crawler = new Crawler($html);

                if ($crawler->filter('.wp-post-image')->count() > 0) {
                    $imgSrc = $crawler->filter('.wp-post-image')->first()->attr('src');
                    
                    if (!empty($imgSrc)) {
                        $photoPath = 'doctors/' . basename(parse_url($imgSrc, PHP_URL_PATH));
                        if (!Storage::disk('public')->exists($photoPath)) {
                            // Use guzzle client to avoid SSL / User-Agent blocks
                            $imageResponse = $client->request('GET', $imgSrc);
                            $contents = $imageResponse->getBody()->getContents();
                            if ($contents) {
                                Storage::disk('public')->put($photoPath, $contents);
                            }
                        }
                        
                        $doctor->photo = $photoPath;
                        $doctor->save();
                        $count++;
                        $this->info(" -> Saved Image!");
                    }
                }
            } catch (\Exception $e) {
                $this->warn(" -> Failed: " . $e->getMessage());
            }
            usleep(300000); // 0.3s delay
        }

        $this->info("Complete! Fixed images for $count doctors.");
    }
}
