<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client(['verify' => false]);
$html = $client->request('GET', 'https://www.doctorbangladesh.com/dr-dipankar-ghosh-palash/')->getBody()->getContents();

$crawler = new Crawler($html);

$paragraphs = $crawler->filter('.entry-content p')->each(function (Crawler $node) {
    return trim($node->text());
});

$chambers = [];
$bio = '';

foreach ($paragraphs as $p) {
    if (preg_match('/Address:/i', $p) || preg_match('/Visiting Hour:/i', $p)) {
        // This is a chamber!
        $chambers[] = $p;
    } else if (preg_match('/is a (.*?)/i', $p) || preg_match('/qualification is/i', $p)) {
        // This is the bio!
        $bio = $p;
    }
}

print_r($chambers);
echo "\nBIO: $bio\n";
