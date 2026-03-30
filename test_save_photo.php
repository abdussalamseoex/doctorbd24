<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doctor = App\Models\Doctor::where('slug', 'prof-dr-akm-akhtaruzzaman')->first();
echo "Before: " . var_export($doctor->photo, true) . "\n";

$client = new GuzzleHttp\Client(['verify' => false]);
$html = $client->request('GET', 'https://www.doctorbangladesh.com/prof-dr-akm-akhtaruzzaman/')->getBody()->getContents();

$crawler = new Symfony\Component\DomCrawler\Crawler($html);
$imgSrc = $crawler->filter('.wp-post-image')->count() > 0 ? $crawler->filter('.wp-post-image')->first()->attr('src') : null;
echo "Extracted: $imgSrc\n";

if ($imgSrc) {
    $photoPath = 'doctors/' . basename(parse_url($imgSrc, PHP_URL_PATH));
    echo "Path: $photoPath\n";
    
    // Testing get_file_contents with context!
    $context = stream_context_create([
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ]);
    
    $contents = file_get_contents($imgSrc, false, $context);
    echo "Downloaded bytes: " . strlen($contents) . "\n";
    
    // Test direct assignment
    $doctor->photo = $photoPath;
    echo "Set property to: " . $doctor->photo . "\n";
    $doctor->save();
    
    echo "Database value: " . App\Models\Doctor::where('slug', 'prof-dr-akm-akhtaruzzaman')->first()->photo . "\n";
}
