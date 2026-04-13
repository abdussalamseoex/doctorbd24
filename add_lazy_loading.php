<?php
$files = [
    'resources/views/hospitals/show.blade.php',
    'resources/views/doctors/show.blade.php',
    'resources/views/ambulances/show.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Simple regex to add loading="lazy" decoding="async" to <img> tags if not already present
    // First, skip if it already has lazy
    // Need to match <img ...>
    $content = preg_replace_callback('/<img\s([^>]+)>/i', function ($matches) {
        $attrs = $matches[1];
        if (stripos($attrs, 'loading=') !== false) {
            return $matches[0]; // unchanged
        }
        return '<img loading="lazy" decoding="async" ' . $attrs . '>';
    }, $content);

    file_put_contents($file, $content);
}
echo "Lazy loading added successfully.\n";
