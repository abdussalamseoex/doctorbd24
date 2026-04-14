<?php
$handle = fopen("Popular Diagnostic Centre Ltd. Test and Service Charges.csv", "r");
$header = fgetcsv($handle, 10000, ",");
$catIdx = 1;
$nameIdx = 2;
$priceIdx = 3;
$servicesToInsert = [];
$currentCategory = null;

while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
    $cat = isset($data[$catIdx]) ? trim($data[$catIdx]) : '';
    $name = isset($data[$nameIdx]) ? trim($data[$nameIdx]) : '';
    $price = isset($data[$priceIdx]) ? trim($data[$priceIdx]) : '';

    if (empty($cat) && empty($name) && empty($price)) {
        continue;
    }

    if (empty($name) && !empty($price)) {
        if (count($servicesToInsert) > 0) {
            $lastIdx = count($servicesToInsert) - 1;
            if (!str_contains($servicesToInsert[$lastIdx]['price'], $price)) {
                $servicesToInsert[$lastIdx]['price'] .= ' / ' . $price;
            }
        }
        continue;
    }

    if (!empty($cat)) {
        $currentCategory = $cat;
    }
    
    if (!empty($name)) {
        $servicesToInsert[] = [
            'category' => $currentCategory ?? 'Uncat',
            'name' => $name,
            'price' => $price
        ];
    }
}
fclose($handle);

echo count($servicesToInsert) . " tests parsed.\n";
for($i=0; $i<8; $i++) { print_r($servicesToInsert[$i]); }
