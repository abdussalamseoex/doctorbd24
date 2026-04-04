<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.populardiagnostic.com/api/doctors");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$output = curl_exec($ch);
curl_close($ch);
echo "Result for /api/doctors:\n";
echo substr($output, 0, 500) . "\n\n";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, "https://api.populardiagnostic.com/api/v1/doctors");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$output2 = curl_exec($ch2);
curl_close($ch2);
echo "Result for /api/v1/doctors:\n";
echo substr($output2, 0, 500);
