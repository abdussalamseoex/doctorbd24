<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client(['verify' => false]);
$html = $client->request('GET', 'https://www.doctorbangladesh.com/prof-dr-akm-akhtaruzzaman/')->getBody()->getContents();

$crawler = new Crawler($html);

$images = $crawler->filter('img')->each(function (Crawler $node, $i) {
    return [
        'src' => $node->attr('src'),
        'class' => $node->attr('class'),
        'alt' => $node->attr('alt')
    ];
});

print_r($images);
