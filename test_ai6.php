<?php
$data = json_decode(file_get_contents('https://openrouter.ai/api/v1/models'), true);
foreach($data['data'] as $model) {
    if(strpos($model['id'], ':free') !== false) {
        echo $model['id'] . "\n";
    }
}
