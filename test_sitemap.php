<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['verify' => false]);
$response = $client->request('GET', 'https://www.doctorbangladesh.com/post-sitemap.xml');
$xml = simplexml_load_string($response->getBody()->getContents());

$namespaces = $xml->getNamespaces(true);
$urls = [];
foreach ($xml->url as $urlElement) {
    if (isset($urlElement->loc)) {
        $urls[] = (string) $urlElement->loc;
    }
}

echo "Total URLs in sitemap: " . count($urls) . "\n";
if (count($urls) > 0) {
    echo "First 5 URLs:\n";
    foreach (array_slice($urls, 0, 5) as $u) {
        echo "- $u\n";
    }
    
    // Test fetch 1 profile
    $profileHtml = $client->request('GET', $urls[0])->getBody()->getContents();
    file_put_contents(__DIR__ . '/test_profile.html', $profileHtml);
    echo "Downloaded " . $urls[0] . " to test_profile.html\n";
}
