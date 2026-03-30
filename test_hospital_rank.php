<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Hospital;

$chamberName = "Popular Diagnostic Center";
$chamberAddress = "House # 2, English Road, Ray Shaheb Bazar, Dhaka";

// Find all hospitals that share the base name
$baseNameRaw = explode(' ', trim($chamberName))[0]; // "Popular"
$baseNameClean = strtolower(preg_replace('/[^a-z]/i', '', $baseNameRaw)); 
$candidates = Hospital::where('name', 'LIKE', "%{$baseNameClean}%")->get();

if ($candidates->isEmpty() && strlen($baseNameClean) > 4) {
    // If first word failed, try substring
    $candidates = Hospital::all();
}

echo "Found {$candidates->count()} candidates for base name.\n";

$bestMatch = null;
$highestScore = -1;

$scrapedText = strtolower($chamberName . ' ' . $chamberAddress);

$ignore = ['hospital', 'clinic', 'center', 'centre', 'diagnostic', 'limited', 'ltd', 'complex', 'medical', 'services', 'care', 'health', 'house', 'road', 'dhaka', 'bhaban', 'market', 'tower', 'floor', 'room', 'block', 'sector', 'avenue', 'lane', 'building', 'street', 'city', 'pvt', 'private', 'unit', 'branch', 'general', 'medical', 'college', 'specialized'];

foreach ($candidates as $h) {
    $score = 0;
    $dbName = strtolower($h->name);
    $dbAddress = strtolower((string)$h->address);
    $dbText = $dbName . ' ' . $dbAddress;
    
    // Core similarity
    $scrapedNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $chamberName));
    $dbNameClean = strtolower(preg_replace('/[^a-z0-9]/i', '', $dbName));
    
    // Exact or subset map for name gives huge baseline
    if ($scrapedNameClean === $dbNameClean) {
        $score += 100;
    } elseif (strlen($dbNameClean) > 10 && str_contains($scrapedNameClean, $dbNameClean)) {
        $score += 50;
    }

    // Keyword extraction: We pull all words > 3 chars from DB Name + Address
    preg_match_all('/\b[a-z]{4,}\b/i', $dbText, $dbWords);
    $dbWords = array_unique($dbWords[0] ?? []);
    
    // Pull words from Scraped Text
    preg_match_all('/\b[a-z]{4,}\b/i', $scrapedText, $scrapedWords);
    $scrapedWords = array_unique($scrapedWords[0] ?? []);
    
    // We award points for every DB word that appears in the Scraped words
    $matchedWords = [];
    foreach ($dbWords as $word) {
        if (in_array($word, $ignore)) continue;
        
        if (in_array($word, $scrapedWords)) {
            $matchedWords[] = $word;
            // Name words are worth more than address words (e.g. branch names like "English")
            if (str_contains($dbName, $word)) {
                $score += 300;
            } else {
                $score += 10;
            }
        }
    }

    if ($score > 0) {
        echo "Candidate: {$h->name} | Score: $score | Matched: " . implode(', ', $matchedWords) . "\n";
    }

    if ($score > $highestScore) {
        $highestScore = $score;
        $bestMatch = $h;
    }
}

echo "\nBest Match: " . ($bestMatch ? $bestMatch->name : 'None') . " (Score: $highestScore)\n";
