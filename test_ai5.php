<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://openrouter.ai/api/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['model'=>'google/gemini-2.0-flash-lite-preview-02-05:free', 'messages'=>[['role'=>'user','content'=>'hi']]]));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer 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',
    'Content-Type: application/json',
    'HTTP-Referer: https://doctorbd24.com',
    'X-Title: DoctorBD24'
]);
$result = curl_exec($ch);
if ($result === false) {
    echo "Curl Error: " . curl_error($ch) . "\n";
} else {
    echo "Result:\n" . $result . "\n";
    echo "HTTP Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
}
