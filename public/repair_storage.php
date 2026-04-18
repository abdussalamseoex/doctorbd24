<?php
/**
 * Storage Link Repair Script for cPanel (Improved)
 * 
 * Please upload this file into your `public` (or `public_html`) folder on cPanel,
 * and then visit: https://yourwebsite.com/repair_storage.php
 */

$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';
$backupFolder = __DIR__ . '/storage_backup_' . time();

echo '<h3>Storage Link Repair Tool</h3>';
echo '<pre>';

// 1. Remove or backup old symlink/directory
if (file_exists($shortcut) || is_link($shortcut)) {
    if (is_link($shortcut)) {
        unlink($shortcut);
        echo "[+] Deleted old 'storage' symlink.\n";
    } elseif (is_dir($shortcut)) {
        // Since rmdir fails if not empty, we rename it to a backup directory!
        if (rename($shortcut, $backupFolder)) {
            echo "[+] Found an actual 'storage' folder instead of a link.\n";
            echo "[+] Renamed to: " . basename($backupFolder) . " to prevent data loss.\n";
        } else {
            echo "[-] Failed to rename the 'storage' directory. Permission denied?\n";
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
    
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo "[+] " . \Illuminate\Support\Facades\Artisan::output();
} catch (\Exception $e) {
    echo "[!] Artisan command failed. Error: " . $e->getMessage() . "\n";
    
    echo "\nAttempting manual symlink creation...\n";
    if (symlink($target, $shortcut)) {
        echo "[+] Successfully created manual symlink: " . $shortcut . " -> " . $target . "\n";
    } else {
        echo "[-] Failed to create manual symlink. Please check server permissions.\n";
    }
}

echo '</pre>';
echo '<strong style="color:green;">Done! Now test your website images. Note: If images are STILL broken, the images might not have been uploaded to storage/app/public properly (e.g. if you uploaded them when the site was on a different server).</strong>';
