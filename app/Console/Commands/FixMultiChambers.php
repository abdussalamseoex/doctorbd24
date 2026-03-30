<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Chamber;
use App\Models\Specialty;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class FixMultiChambers extends Command
{
    protected $signature = 'db:fix-multi-chambers';
    protected $description = 'Re-scrapes all existing doctors to extract multiple chambers and fix corrupted bios';

    private $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'verify' => false,
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
    }

    public function handle()
    {
        $doctors = Doctor::all();
        $this->info("Found {$doctors->count()} doctors to audit.");

        $bar = $this->output->createProgressBar($doctors->count());
        $bar->start();

        foreach ($doctors as $doctor) {
            $url = "https://www.doctorbangladesh.com/{$doctor->slug}/";
            
            try {
                $response = $this->client->request('GET', $url);
                $html = $response->getBody()->getContents();
                
                $crawler = new Crawler($html);
                $paragraphs = $crawler->filter('.entry-content p')->each(function (Crawler $node) {
                    return trim($node->text());
                });

                $rawChambers = [];
                $bioText = '';

                foreach ($paragraphs as $p) {
                    if (preg_match('/Address:/i', $p) || preg_match('/Visiting Hour:/i', $p)) {
                        $rawChambers[] = $p;
                    } else if (preg_match('/is a /i', $p) || preg_match('/qualification is/i', $p)) {
                        $bioText = $p;
                    }
                }

                if (count($rawChambers) > 0 || !empty($bioText)) {
                    // Fix Bio
                    if (!empty($bioText) && $doctor->bio !== $bioText) {
                        $doctor->bio = $bioText;
                        $doctor->save();
                    }

                    // Fix Chambers
                    foreach ($rawChambers as $chamberText) {
                        $chamberData = [];
                        if (strpos($chamberText, 'Address:') !== false) {
                            $parts = explode('Address:', $chamberText);
                            $chamberData['chamber_name'] = trim(preg_replace('/(Chamber & Appointment|\n|\r)+/i', '', $parts[0]));
                            $rem = trim($parts[1] ?? '');
                        } else {
                            $chamberData['chamber_name'] = "Private Chamber";
                            $rem = $chamberText;
                        }

                        if (preg_match('/(.*?)\s*Visiting Hour:\s*(.*?)\s*Appointment:\s*(.*)/i', $rem, $cmatch)) {
                            $chamberData['chamber_address'] = trim($cmatch[1]);
                            $chamberData['visiting_hour'] = trim($cmatch[2]);
                            $chamberData['appointment'] = str_replace('Call Now', '', trim($cmatch[3]));
                        } else if (preg_match('/(.*?)\s*Visiting Hour:\s*(.*)/i', $rem, $cmatch)) {
                            $chamberData['chamber_address'] = trim($cmatch[1]);
                            $chamberData['visiting_hour'] = trim($cmatch[2]);
                            $chamberData['appointment'] = null;
                        } else {
                            $chamberData['chamber_address'] = $rem;
                            $chamberData['visiting_hour'] = null;
                            $chamberData['appointment'] = null;
                        }

                        $hospitalNameRaw = preg_replace('/,.*$/', '', $chamberData['chamber_name']);
                        $hospitalNameRaw = trim($hospitalNameRaw);
                        $hSlug = Str::slug($hospitalNameRaw);
                        
                        $matchedHospital = null;
                        $highestScore = 0;
                        
                        $scrapedText = strtolower($chamberData['chamber_name'] . ' ' . $chamberData['chamber_address']);
                        $baseNameClean = strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $hospitalNameRaw)[0]));
                        
                        $candidates = Hospital::withTrashed()->where('name', 'LIKE', "%{$baseNameClean}%")->get();
                        if ($candidates->isEmpty() && strlen($baseNameClean) > 4) {
                            $candidates = Hospital::withTrashed()->get();
                        }

                        $ignore = ['hospital', 'clinic', 'center', 'centre', 'diagnostic', 'limited', 'ltd', 'complex', 'medical', 'services', 'care', 'health', 'house', 'road', 'dhaka', 'bhaban', 'market', 'tower', 'floor', 'room', 'block', 'sector', 'avenue', 'lane', 'building', 'street', 'city', 'pvt', 'private', 'unit', 'branch', 'general', 'medical', 'college', 'specialized'];

                        $scrapedNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $hospitalNameRaw));
                        preg_match_all('/\b[a-z]{4,}\b/i', $scrapedText, $scrapedWords);
                        $scrapedWords = array_unique($scrapedWords[0] ?? []);

                        foreach ($candidates as $h) {
                            $score = 0;
                            $dbName = strtolower($h->name);
                            $dbAddress = strtolower((string)$h->address);
                            $dbNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $dbName));
                            
                            if ($scrapedNameClean === $dbNameClean) {
                                $score += 100;
                            } elseif (strlen($dbNameClean) > 10 && str_contains($scrapedNameClean, $dbNameClean)) {
                                $score += 50;
                            }

                            preg_match_all('/\b[a-z]{4,}\b/i', $dbName . ' ' . $dbAddress, $dbWords);
                            $dbWords = array_unique($dbWords[0] ?? []);

                            foreach ($dbWords as $word) {
                                if (in_array($word, $ignore)) continue;
                                if (in_array($word, $scrapedWords)) {
                                    if (str_contains($dbName, $word)) {
                                        $score += 300;
                                    } else {
                                        $score += 10;
                                    }
                                }
                            }

                            if ($score > $highestScore) {
                                $highestScore = $score;
                                $matchedHospital = $h;
                            }
                        }
                        
                        if (!$matchedHospital) {
                            $matchedHospital = Hospital::withTrashed()->where('slug', $hSlug)->first();
                        }

                        if ($matchedHospital) {
                            if ($matchedHospital->trashed()) $matchedHospital->restore();
                            if (empty($matchedHospital->address) && !empty($chamberData['chamber_address'])) {
                                $matchedHospital->address = $chamberData['chamber_address'];
                                $matchedHospital->save();
                            }
                            if (empty($matchedHospital->phone) && !empty($chamberData['appointment'])) {
                                $matchedHospital->phone = $chamberData['appointment'];
                                $matchedHospital->save();
                            }
                            $hospital = $matchedHospital;
                        } else {
                            $hospital = Hospital::create([
                                'slug' => $hSlug,
                                'name' => $hospitalNameRaw,
                                'type' => 'hospital',
                                'address' => $chamberData['chamber_address'],
                                'phone' => $chamberData['appointment'],
                                'verified' => false
                            ]);
                        }

                        // Create Pivot! This loop ensures Chamber 2, Chamber 3 etc are created!
                        Chamber::updateOrCreate(
                            ['doctor_id' => $doctor->id, 'hospital_id' => $hospital->id],
                            [
                                'name' => $hospital->name,
                                'address' => $chamberData['chamber_address'],
                                'visiting_hours' => $chamberData['visiting_hour'],
                                'phone' => $chamberData['appointment'],
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
                // Ignore 404s
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Chamber and Bio rescue complete!");
    }
}
