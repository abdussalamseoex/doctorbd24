<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://agentrouter.org/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['model'=>'deepseek-r1-0528', 'messages'=>[['role'=>'user','content'=>'hi']]]));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer sk-TESTAKEY1234567890',
    'Content-Type: application/json',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
]);
$result = curl_exec($ch);
if ($result === false) {
    echo "Curl Error: " . curl_error($ch) . "\n";
} else {
    echo "Result:\n" . $result . "\n";
    echo "HTTP Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
}
