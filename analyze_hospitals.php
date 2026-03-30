<?php
$file = 'd:/doctorbd24.com/WordPress Data/hospital-export-2026-03-26.csv';
$handle = fopen($file, 'r');
$header = fgetcsv($handle);

$categories = [];
$empty_examples = [];

while (($row = fgetcsv($handle)) !== false) {
    if (count($header) !== count($row)) continue;
    $data = array_combine($header, $row);
    $cat = $data['hospital-category'] ?? '';
    $title = $data['post_title'] ?? '';
    
    if (!isset($categories[$cat])) {
        $categories[$cat] = 0;
    }
    $categories[$cat]++;
    
    if ($cat === '' && count($empty_examples) < 10) {
        $empty_examples[] = $title;
    }
}
fclose($handle);

echo "Categories Found:\n";
print_r($categories);
echo "\nExamples of empty categories:\n";
print_r($empty_examples);
