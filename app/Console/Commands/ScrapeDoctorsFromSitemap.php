<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Hospital;
use App\Models\Chamber;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ScrapeDoctorsFromSitemap extends Command
{
    protected $signature = 'scrape:doctors {--sitemap=1 : The sitemap number 1-7} {--limit=null : Maximum profiles to scrape}';
    protected $description = 'Scrape doctors from doctorbangladesh.com using its sitemap and import them while avoiding duplicates.';

    private $client;

    public function __construct()
    {
        parent::__construct();
        // Disable SSL verification for local testing, optional timeout
        $this->client = new Client([
            'verify' => false,
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ]
        ]);
    }

    public function handle()
    {
        $sitemapId = $this->option('sitemap');
        $sitemapUrl = $sitemapId == 1 ? 'https://www.doctorbangladesh.com/post-sitemap.xml' : "https://www.doctorbangladesh.com/post-sitemap{$sitemapId}.xml";

        $this->info("Fetching Sitemap: $sitemapUrl");
        
        try {
            $response = $this->client->request('GET', $sitemapUrl);
            $xml = simplexml_load_string($response->getBody()->getContents());
        } catch (\Exception $e) {
            $this->error("Failed to fetch sitemap: " . $e->getMessage());
            return;
        }

        $urls = [];
        foreach ($xml->url as $urlElement) {
            if (isset($urlElement->loc)) {
                $urls[] = (string) $urlElement->loc;
            }
        }

        $totalUrls = count($urls);
        $this->info("Found $totalUrls URLs in the sitemap.");

        $limit = $this->option('limit');
        $count = 0;
        $skipped = 0;
        $processed = 0;

        foreach ($urls as $url) {
            if ($limit && $processed >= $limit) break;

            // Extract slug from URL (e.g., https://.../dr-md-nazrul-islam-kidney/ -> dr-md-nazrul-islam-kidney)
            $path = parse_url($url, PHP_URL_PATH);
            $slug = trim($path, '/');
            
            // Skip blog posts or non-doctor URLs if they slip in, but usually post-sitemap has doctors here.
            
            // DUPLICATE CHECK: 
            // We ensure we don't import him if he's already in the 900+ database!
            if (Doctor::where('slug', $slug)->exists()) {
                $skipped++;
                continue; // Extremely fast skipping, saves hours!
            }

            $this->line("Scraping: $slug");
            try {
                $html = $this->client->request('GET', $url)->getBody()->getContents();
                $crawler = new Crawler($html);
                $this->parseAndSaveProfile($crawler, $slug, $url);
                $processed++;
                $count++;
            } catch (\Exception $e) {
                $this->warn("Failed to scrape $url : " . $e->getMessage());
            }

            // Small delay to prevent being blocked by the target server
            usleep(500000); // 0.5 seconds
        }

        $this->info("Scraping complete! Processed: $count. Skipped Duplicates: $skipped.");
    }

    private function parseAndSaveProfile(Crawler $crawler, $slug, $sourceUrl)
    {
        $data = [
            'name' => '',
            'bio' => '',
            'qualifications' => '',
            'designation' => '',
            'specialty' => '',
            'image' => '',
            'chamber_name' => '',
            'chamber_address' => '',
            'visiting_hour' => '',
            'appointment' => ''
        ];

        // 1. Name
        try {
            $data['name'] = trim($crawler->filter('h1')->text());
        } catch (\Exception $e) {}

        if (empty($data['name'])) return;

        // 2. Image
        try {
            if ($crawler->filter('.wp-post-image')->count() > 0) {
                // The first wp-post-image is the doctor's portrait, subsequent ones are usually related doctors in the sidebar
                $data['image'] = $crawler->filter('.wp-post-image')->first()->attr('src');
            } else if ($crawler->filter('.entry-content img')->count() > 0) {
                $data['image'] = $crawler->filter('.entry-content img')->first()->attr('src');
            }
        } catch (\Exception $e) {}

        // 3. Bio and Details from paragraphs
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
            $data['bio'] = $bioText;

            // Extract Specialty from Bio
            if (preg_match('/is a (.*?) Specialist/i', $bioText, $match) || preg_match('/is a skilled (.*?) Specialist/i', $bioText, $match)) {
                $data['specialty'] = trim(explode(' in ', $match[1])[0]);
            } else if (preg_match('/Specialist & (.*?)\s/i', $bioText, $match)) {
                $data['specialty'] = trim($match[1]);
            } else {
                $data['specialty'] = 'General Physician';
            }

            // Extract Qualifications
            if (preg_match('/qualification is (.*?)\. He/i', $bioText, $match) || preg_match('/qualification is (.*?)\. She/i', $bioText, $match)) {
                $data['qualifications'] = trim($match[1]);
            }

            // Extract Designation with a more comprehensive list of titles and stopping at the first period.
            if (preg_match('/(?:He|She) is a (Professor.*?)\./i', $bioText, $match) 
             || preg_match('/(?:He|She) is a (Senior Consultant.*?)\./i', $bioText, $match) 
             || preg_match('/(?:He|She) is a (Consultant.*?)\./i', $bioText, $match) 
             || preg_match('/(?:He|She) is an (Assistant Professor.*?)\./i', $bioText, $match) 
             || preg_match('/(?:He|She) is an (Associate Professor.*?)\./i', $bioText, $match) 
             || preg_match('/(?:He|She) is an (Ex-Professor.*?)\./i', $bioText, $match)
             || preg_match('/(?:He|She) is a (Medical Officer.*?)\./i', $bioText, $match)
             || preg_match('/(?:He|She) is a (Director.*?)\./i', $bioText, $match)) {
                $data['designation'] = trim(preg_replace('/\s+/', ' ', $match[1]));
            }

            // Clean values
            $gender = stripos($data['name'], ' Begum') !== false || stripos($data['name'], ' Khatun') !== false || stripos($data['bio'], 'She is') !== false ? 'female' : 'male';

            // Download Image
            $photoPath = null;
            if (!empty($data['image_url'])) {
                try {
                    $photoPath = 'doctors/' . basename(parse_url($data['image_url'], PHP_URL_PATH));
                    if (!Storage::disk('public')->exists($photoPath)) {
                        // Use guzzle client to avoid SSL / User-Agent blocks
                        $imageResponse = $this->client->request('GET', $data['image_url']);
                        $contents = $imageResponse->getBody()->getContents();
                        if ($contents) {
                             Storage::disk('public')->put($photoPath, $contents);
                        }
                    }
                } catch (\Exception $e) {
                    // If the download completely fails, don't assign a broken path
                    $photoPath = null;
                }
            }

            // Save Doctor
            $doctor = Doctor::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'bio' => $data['bio'],
                    'qualifications' => $data['qualifications'],
                    'designation' => $data['designation'],
                    'gender' => $gender,
                    'photo' => $photoPath,
                    'verified' => true,
                ]
            );

            // Map Specialty (De-duplication feature user requested!)
            if (!empty($data['specialty'])) {
                $specSlug = $this->normalizeSpecialtySlug($data['specialty']);
                $specName = ucwords(str_replace('-', ' ', $specSlug));
                
                $spec = Specialty::firstOrCreate(
                    ['slug' => $specSlug],
                    ['name' => ['en' => $specName, 'bn' => $specName]]
                );
                $doctor->specialties()->syncWithoutDetaching([$spec->id]);
            }

            // Save Chambers
            foreach ($rawChambers as $chamberText) {
                $chamberData = [];
                // Check if Address: exists to split it safely. The name is everything BEFORE Address:
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
                
                // Advanced Address-Aware Fuzzy Match
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
                    $hospital = $matchedHospital;
                    
                    // Optionally update missing address on match if we want (risky, so skip)
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
            $this->info("Scraped successfully: {$data['name']}");
        }
    }

    private function normalizeSpecialtySlug($specialtyRaw)
    {
        $slug = Str::slug($specialtyRaw);
        
        // Advanced Mapping to prevent 114 expanding to 1000 duplicates
        $map = [
            'kidney-specialist' => 'nephrology',
            'kidney' => 'nephrology',
            'nephrologist' => 'nephrology',
            'heart-specialist' => 'cardiology',
            'cardiologist' => 'cardiology',
            'skin-specialist' => 'dermatology',
            'dermatologist' => 'dermatology',
            'eye-specialist' => 'ophthalmology',
            'ophthalmologist' => 'ophthalmology',
            'child-specialist' => 'pediatrics',
            'pediatrician' => 'pediatrics',
            'gynecologist' => 'gynecology-obstetrics',
            'gynecology' => 'gynecology-obstetrics',
            'obstetrics' => 'gynecology-obstetrics',
            'medicine-specialist' => 'internal-medicine',
            'general-physician' => 'internal-medicine',
            'medicine' => 'internal-medicine',
            'bone-specialist' => 'orthopedics',
            'orthopedist' => 'orthopedics',
            'orthopedic-surgeon' => 'orthopedics',
            'neurologist' => 'neurology',
            'brain-specialist' => 'neurology',
            'neuro-surgeon' => 'neurosurgery',
            'cancer-specialist' => 'oncology',
            'oncologist' => 'oncology',
            'gastroenterologist' => 'gastroenterology',
            'liver-specialist' => 'hepatology',
            'rheumatologist' => 'rheumatology',
            'ent-specialist' => 'ent-otolaryngology',
            'otolaryngologist' => 'ent-otolaryngology',
            'psychiatrist' => 'psychiatry',
            'mental-health' => 'psychiatry',
            'dentist' => 'dental-surgeon',
            'dental' => 'dental-surgeon',
            'general-surgeon' => 'surgery',
            'surgeon' => 'surgery',
            'urologist' => 'urology',
        ];

        foreach ($map as $key => $mainSlug) {
            if (str_contains($slug, $key)) {
                return $mainSlug;
            }
        }

        // Fallback: If no direct map found, remove generic words to condense variants
        $slug = str_replace(['-specialist', '-surgeon', '-doctor', '-physician', 'expert'], '', $slug);
        
        return trim($slug, '-');
    }
}
