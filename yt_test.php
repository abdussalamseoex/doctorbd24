<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://www.youtube.com/@somoynews360/videos';
$response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get($url);

if ($response->successful()) {
    $html = $response->body();
    if (preg_match('/<meta\s+itemprop="identifier"\s+content="(UC[a-zA-Z0-9_-]+)">/i', $html, $matches) || preg_match('/"channelId":"(UC[a-zA-Z0-9_-]+)"/i', $html, $matches)) {
        $channelId = $matches[1];
        echo "Found Channel ID: $channelId\n";
        
        $apiKey = env('YOUTUBE_API_KEY');
        echo "API KEY: $apiKey\n";
        
        $apiUrl = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=5&type=video";
        $apiResponse = \Illuminate\Support\Facades\Http::get($apiUrl);
        if ($apiResponse->successful()) {
            $data = $apiResponse->json();
            echo "API SUCCESS! Next Page Token: " . ($data['nextPageToken'] ?? 'NONE') . "\n";
            foreach($data['items'] as $item) {
                echo $item['id']['videoId'] . " - " . $item['snippet']['title'] . "\n";
            }
        } else {
            echo "API ERROR: " . $apiResponse->body() . "\n";
        }
    } else {
        echo "No channel ID found.\n";
    }
}
