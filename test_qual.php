<?php
require __DIR__.'/vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;
$html = file_get_contents('https://www.doctorbangladesh.com/prof-dr-sayed-farhan-ali-razib/');
$crawler = new Crawler($html);
$content = $crawler->filter('.entry-content')->html();
echo "HTML:\n".strip_tags($content)."\n";
