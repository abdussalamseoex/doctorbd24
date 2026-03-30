<?php

use App\Models\Hospital;
use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$csvFile = __DIR__ . '/WordPress Data/hospital-export-2026-03-26.csv';

if (!file_exists($csvFile)) {
    die("CSV file not found: $csvFile\n");
}

$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle);

$count = 0;
$success = 0;
$skipped = 0;

echo "Starting Hospital Import...\n";

// Pre-load locations for performance
$divisions = Division::all();
$districts = District::all();
$areas = Area::all();

while (($row = fgetcsv($handle)) !== false) {
    if (count($header) !== count($row)) {
        continue;
    }

    $data = array_combine($header, $row);

    if (($data['post_status'] ?? '') !== 'publish') {
        $skipped++;
        continue;
    }

    $count++;
    $title = $data['post_title'];
    $slug = urldecode($data['post_name'] ?: Str::slug($title));

    echo "[$count] Importing: $title... ";

    // 1. Map Type
    $type = 'hospital';
    $cat = strtolower($data['hospital-category'] ?? '');
    if (Str::contains($cat, 'diagnostic') || Str::contains(strtolower($title), 'diagnostic') || Str::contains(strtolower($title), 'consultation') || Str::contains(strtolower($title), 'lab')) {
        $type = 'diagnostic';
    } elseif (Str::contains($cat, 'clinic') || Str::contains(strtolower($title), 'clinic') || Str::contains(strtolower($title), 'dental') || Str::contains(strtolower($title), 'eye')) {
        $type = 'clinic';
    } elseif (Str::contains($cat, 'veterinary')) {
        $type = 'clinic';
    }

    // 2. Map Location (Area)
    $areaId = null;
    $csvArea = trim($data['area'] ?? '');
    $csvCity = trim($data['city'] ?? ''); // District
    $csvState = trim($data['state'] ?? ''); // Division

    // Match Area
    if ($csvArea) {
        $matchedArea = $areas->first(function($a) use ($csvArea) {
            return Str::contains(strtolower($a->getTranslation('name', 'en')), strtolower($csvArea)) || 
                   Str::contains(strtolower($a->getTranslation('name', 'bn')), strtolower($csvArea));
        });
        if ($matchedArea) {
            $areaId = $matchedArea->id;
        }
    }

    // If area not found, try matching by city (District)
    if (!$areaId && $csvCity) {
        $matchedDist = $districts->first(function($d) use ($csvCity) {
            return Str::contains(strtolower($d->getTranslation('name', 'en')), strtolower($csvCity)) || 
                   Str::contains(strtolower($d->getTranslation('name', 'bn')), strtolower($csvCity));
        });
        if ($matchedDist) {
            // Pick the first area in this district as a fallback or just leave as district?
            // Since Hospital belongsTo Area, we need an area_id. 
            // We'll pick any area that matches "Sadar" or the first one.
            $fallbackArea = $matchedDist->areas->first();
            if ($fallbackArea) {
                $areaId = $fallbackArea->id;
            }
        }
    }

    // 3. Prepare Social Links & Opening Hours from JSON-LD
    $openingHours = [];
    $facebook = $data['facebook'] ?? null;
    $instagram = $data['instagram'] ?? null;
    $youtube = $data['youtube'] ?? null;

    $jsonLdRaw = $data['saswp_custom_schema_field'] ?? '';
    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $jsonLdRaw, $matches)) {
        $json = json_decode($matches[1], true);
        if ($json) {
            // Opening Hours
            if (isset($json['openingHoursSpecification'])) {
                foreach ($json['openingHoursSpecification'] as $spec) {
                    $days = is_array($spec['dayOfWeek']) ? $spec['dayOfWeek'] : [$spec['dayOfWeek']];
                    $timeStr = ($spec['opens'] ?? '09:00') . ' - ' . ($spec['closes'] ?? '21:00');
                    foreach ($days as $day) {
                        // Ensure capitalized day name (e.g. "Monday")
                        $dayKey = ucfirst(strtolower($day));
                        $openingHours[$dayKey] = $timeStr;
                    }
                }
            }
            // Social Links from sameAs
            if (isset($json['sameAs']) && is_array($json['sameAs'])) {
                foreach ($json['sameAs'] as $link) {
                    if (Str::contains($link, 'facebook.com')) $facebook = $link;
                    if (Str::contains($link, 'instagram.com')) $instagram = $link;
                    if (Str::contains($link, 'youtube.com')) $youtube = $link;
                }
            }
        }
    }

    // 4. Download Image
    $bannerPath = null;
    $existingHospital = Hospital::where('slug', $slug)->first();
    $imageUrl = $data['featured_image'] ?? null;
    
    if ($existingHospital && $existingHospital->banner) {
        $bannerPath = $existingHospital->banner;
    } elseif ($imageUrl) {
        $bannerPath = downloadHospitalImage($imageUrl, 'banner');
    }

    // 5. Create / Update Hospital
    $hospital = Hospital::updateOrCreate(
        ['slug' => $slug],
        [
            'name'           => $title,
            'type'           => $type,
            'banner'         => $bannerPath,
            'logo'           => $bannerPath, // Fallback to banner if no separate logo
            'about'          => $data['post_content'],
            'phone'          => $data['phone'] ?: null,
            'email'          => $data['contact-email'] ?: null,
            'website'        => $data['contact_web'] ?: null,
            'address'        => $data['address'] ?: null,
            'area_id'        => $areaId,
            'lat'            => $data['latitude'] ?: null,
            'lng'            => $data['longitude'] ?: null,
            'facebook_url'   => $facebook,
            'instagram_url'  => $instagram,
            'youtube_url'    => $youtube,
            'opening_hours'  => $openingHours ?: null,
            'verified'       => true,
            'featured'       => ($data['listing_featured'] ?? 'no') === 'yes',
        ]
    );

    // 6. SEO Meta
    $seoTitle = $data['_yoast_wpseo_title'] ?: ($title . ' | DoctorBD24');
    $seoDesc = $data['_yoast_wpseo_metadesc'] ?: Str::limit(strip_tags($data['post_content']), 160);
    $seoKwd = $data['_yoast_wpseo_focuskw'] ?: null;

    $hospital->updateSeo([
        'title'    => $seoTitle,
        'description' => $seoDesc,
        'keywords' => $seoKwd,
    ]);

    echo "OK\n";
    $success++;
}

fclose($handle);

echo "\nImport Complete!\n";
echo "Total processed: $count\n";
echo "Successfully imported: $success\n";
echo "Skipped (non-publish): $skipped\n";

/**
 * Image downloader helper (handles Bengali chars in URLs)
 */
function downloadHospitalImage($url, $subfolder = 'banner')
{
    if (!$url) return null;

    // Smart URL encoding for non-ASCII characters (e.g. Bengali)
    $parts = parse_url($url);
    if (!$parts) return null;

    $pathArr = explode('/', $parts['path']);
    foreach ($pathArr as $key => $pathPart) {
        $pathArr[$key] = rawurlencode($pathPart);
    }
    $encodedPath = implode('/', $pathArr);
    $encodedUrl = $parts['scheme'] . '://' . $parts['host'] . $encodedPath;

    $filename = basename($parts['path']);
    $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
    $newFilename = time() . '_' . Str::random(10) . '.' . $extension;
    $savePath = 'hospitals/' . $subfolder . '/' . $newFilename;
    $absolutePath = storage_path('app/public/' . $savePath);

    if (!file_exists(dirname($absolutePath))) {
        mkdir(dirname($absolutePath), 0755, true);
    }

    $ch = curl_init($encodedUrl);
    $fp = fopen($absolutePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if ($statusCode === 200) {
        return $savePath;
    } else {
        @unlink($absolutePath);
        return null;
    }
}
