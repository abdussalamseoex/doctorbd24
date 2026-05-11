<?php

// Force 301 Redirect to remove /public from the URL (bypasses any server/cPanel config quirks)
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($requestUri, '/public/') === 0 || $requestUri === '/public') {
    $cleanUri = preg_replace('#^/public/?#i', '/', $requestUri);
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'doctorbd24.com';
    header('Location: ' . $scheme . '://' . $host . $cleanUri, true, 301);
    exit;
}

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
