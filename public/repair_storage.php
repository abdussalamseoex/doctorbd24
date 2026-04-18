<?php
/**
 * Storage Link Repair Script for cPanel
 * 
 * Please upload this file into your `public` (or `public_html`) folder on cPanel,
 * and then visit: https://yourwebsite.com/repair_storage.php
 */

// If your document root on cPanel is public_html, adjust this path. 
// Assuming it is one folder above where this script is placed.
$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';

echo '<h3>Storage Link Repair Tool</h3>';
echo '<pre>';

// 1. Remove old symlink/junction if exists
if (file_exists($shortcut) || is_link($shortcut)) {
    if (is_link($shortcut)) {
        unlink($shortcut);
        echo "[+] Deleted old 'storage' symlink.\n";
    } elseif (is_dir($shortcut)) {
        // Handle Windows junction or normal directory
        if (PHP_OS_FAMILY === 'Windows') {
            exec('rmdir "' . $shortcut . '"');
            echo "[+] Deleted old 'storage' directory/junction (Windows).\n";
        } else {
            // Delete recursively or assume empty
            @rmdir($shortcut);
            echo "[+] Deleted old 'storage' directory (Linux).\n";
        }
    }
} else {
    echo "[-] No old 'storage' folder/link found. Proceeding...\n";
}

// 2. Create the new link using artisan
try {
    echo "\nRunning storage:link command...\n";
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Call the artisan command directly programmatically
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    
    echo "[+] " . \Illuminate\Support\Facades\Artisan::output();
} catch (\Exception $e) {
    echo "[!] Artisan command failed (maybe vendor folder is missing?). Error: " . $e->getMessage() . "\n";
    
    // Falback: manual native symlink creation
    echo "\nAttempting manual symlink creation...\n";
    if (symlink($target, $shortcut)) {
        echo "[+] Successfully created manual symlink: " . $shortcut . " -> " . $target . "\n";
    } else {
        echo "[-] Failed to create manual symlink. Please check server permissions.\n";
    }
}

echo '</pre>';
echo '<strong style="color:green;">Done! Test your website now. After testing, please delete this file from your server.</strong>';
