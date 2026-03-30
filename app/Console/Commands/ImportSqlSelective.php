<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Ambulance;
use App\Models\BlogPost;
use Illuminate\Support\Str;
use DB;

class ImportSqlSelective extends Command
{
    protected $signature = 'import:sql-selective {--limit=5}';
    protected $description = 'Import a selective number of records from WordPress SQL dump';

    public function handle()
    {
        $limit = $this->option('limit');
        $filePath = base_path('WordPress Data/doctorbd_wp712.sql');

        if (!file_exists($filePath)) {
            $this->error("SQL file not found at: $filePath");
            return;
        }

        $this->info("Starting robust selective import (limit: $limit per type)...");

        $targets = ['doctor', 'hospital', 'ambulance', 'post'];
        $samples = array_fill_keys($targets, []);
        $metaData = [];
        $allTargetIds = [];

        // Step 1: Scan for Post IDs
        $handle = fopen($filePath, 'r');
        $inPosts = false;
        
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, "INSERT INTO `wp9s_posts`") !== false) {
                $inPosts = true;
                continue;
            } elseif (strpos($line, "INSERT INTO") !== false || strpos($line, "UNLOCK TABLES") !== false || strpos($line, "CREATE TABLE") !== false) {
                if ($inPosts) { $inPosts = false; }
            }

            if ($inPosts) {
                $this->parseInsertLine($line, function($cols) use (&$samples, $targets, $limit, &$allTargetIds) {
                    if (count($cols) >= 21) {
                        $id = $cols[0];
                        $status = $cols[7];
                        $type = $cols[20];
                        
                        if (in_array($type, $targets) && ($status === 'publish' || $type === 'post') && count($samples[$type]) < $limit) {
                            $samples[$type][$id] = [
                                'title' => $cols[5],
                                'content' => $cols[4],
                                'slug' => $cols[11],
                            ];
                            $allTargetIds[] = $id;
                            $this->line("Sample Found: $type -> $id ({$cols[5]})");
                        }
                    }
                });
            }
            
            // Optimization: Stop scanning if we have enough of all samples
            $foundAll = true;
            foreach($targets as $t) if(count($samples[$t]) < $limit) $foundAll = false;
            // if ($foundAll) break; // Don't break yet, need to finish scanning current block if needed
        }
        fclose($handle);

        // Step 2: Scan for Meta Data for those IDs
        $this->info("Scanning metadata for " . count($allTargetIds) . " IDs...");
        $handle = fopen($filePath, 'r');
        $inMeta = false;
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, "INSERT INTO `wp9s_postmeta`") !== false) {
                $inMeta = true;
                continue;
            } elseif (strpos($line, "INSERT INTO") !== false || strpos($line, "UNLOCK TABLES") !== false || strpos($line, "CREATE TABLE") !== false) {
                if ($inMeta) { $inMeta = false; }
            }

            if ($inMeta) {
                $this->parseInsertLine($line, function($cols) use (&$metaData, $allTargetIds) {
                    if (count($cols) >= 4) {
                        $postId = $cols[1];
                        if (in_array($postId, $allTargetIds)) {
                            $metaData[$postId][$cols[2]] = $cols[3];
                            if ($postId == '2636') {
                                // $this->line("Meta Found for 2636: {$cols[2]} -> " . substr($cols[3], 0, 30));
                            }
                        }
                    }
                });
            }
        }
        fclose($handle);

        // Step 3: Insert into Laravel
        $this->info("Processing and inserting into Laravel...");
        
        foreach ($samples['doctor'] as $id => $data) {
            $meta = $metaData[$id] ?? [];
            
            // Extract phone from specialized ACF field if regular phone is missing
            $phone = $meta['phone'] ?? '';
            if (empty($phone) && !empty($meta['appointment'])) {
                try {
                    $rawAppt = $meta['appointment'];
                    // Fix escaped quotes in SQL dump before unserializing
                    $rawAppt = str_replace(['\"', "\\'"], ['"', "'"], $rawAppt);
                    $appt = @unserialize($rawAppt);
                    if (is_array($appt) && !empty($appt['title'])) {
                        $phone = $appt['title'];
                    }
                } catch (\Exception $e) {}
            }

            Doctor::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['title'],
                    'bio' => $data['content'],
                    'qualifications' => $meta['qualification'] ?? ($meta['qualifications'] ?? ''),
                    'designation' => $meta['position'] ?? ($meta['designation'] ?? ''),
                    'experience_years' => intval($meta['experience'] ?? 0),
                    'phone' => $phone,
                    'email' => $meta['email'] ?? '',
                    'verified' => true,
                ]
            );
        }

        foreach ($samples['hospital'] as $id => $data) {
            $meta = $metaData[$id] ?? [];
            Hospital::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['title'],
                    'about' => $data['content'],
                    'phone' => $meta['phone'] ?? '',
                    'email' => $meta['contact-email'] ?? ($meta['email'] ?? ''),
                    'address' => $meta['address'] ?? '',
                    'website' => $meta['contact_web'] ?? ($meta['website'] ?? ''),
                    'type' => 'hospital',
                    'verified' => true,
                ]
            );
        }

        foreach ($samples['ambulance'] as $id => $data) {
            $meta = $metaData[$id] ?? [];
            $slug = $data['slug'] ?: Str::slug($data['title']);
            Ambulance::updateOrCreate(
                ['slug' => $slug],
                [
                    'provider_name' => $data['title'],
                    'phone' => $meta['phone'] ?? 'dummy-'.$id,
                    'type' => 'ac',
                    'available_24h' => true,
                ]
            );
        }

        foreach ($samples['post'] as $id => $data) {
            $meta = $metaData[$id] ?? [];
            BlogPost::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'body' => $data['content'],
                    'excerpt' => Str::limit(strip_tags($data['content']), 160),
                    'user_id' => 1,
                    'published_at' => now(),
                    'meta_description' => $meta['_yoast_wpseo_metadesc'] ?? '',
                ]
            );
        }

        $this->info("Selective import complete!");
    }

    private function parseInsertLine($line, $callback)
    {
        if (strpos($line, "VALUES") !== false) {
            $line = substr($line, strpos($line, "VALUES") + 6);
        }
        
        $line = trim($line, "; ");
        $records = explode("),(", $line);
        foreach ($records as $record) {
            $record = trim($record, "()");
            $cols = str_getcsv($record, ",", "'");
            $cols = array_map(function($val) {
                return trim($val, " '\"\t\n\r");
            }, $cols);
            $callback($cols);
        }
    }
}
