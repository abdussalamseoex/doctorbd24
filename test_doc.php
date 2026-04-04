<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.populardiagnostic.com/api/doctor?token=UCbuv3xIyFsMS9pycQzIiwdwaiS3izz4&page=1&per_page=1000");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json, text/plain, */*',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'Origin: https://www.populardiagnostic.com',
    'Referer: https://www.populardiagnostic.com/'
]);
$output = curl_exec($ch);
curl_close($ch);
echo substr($output, 0, 1000);
file_put_contents('popular_docs.json', $output);
