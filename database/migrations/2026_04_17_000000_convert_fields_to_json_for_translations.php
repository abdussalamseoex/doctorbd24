<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'doctors' => ['name', 'designation', 'qualifications', 'bio'],
            'hospitals' => ['name', 'about', 'address'],
            'ambulances' => ['provider_name', 'address', 'summary', 'notes'],
            'blog_posts' => ['title', 'excerpt', 'content'],
            'pages' => ['title', 'content'],
            'seo_landing_pages' => ['h1_heading', 'top_content', 'bottom_content'],
            'seo_metas' => ['title', 'description', 'keywords'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            // Expand string columns to TEXT to prevent truncation errors before JSON conversion
            foreach ($columns as $column) {
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` MEDIUMTEXT");
                } catch (\Exception $e) {
                    // Ignore in case the exact type conversion throws warning on unsupported env
                }
            }

            // Update to JSON string format {"en": "value"}
            foreach (DB::table($table)->cursor() as $row) {
                $updates = [];
                foreach ($columns as $column) {
                    $value = $row->$column;
                    // Check if not empty and not already JSON object
                    if (!empty($value) && !str_starts_with(trim($value), '{')) {
                        $updates[$column] = json_encode(['en' => (string) $value], JSON_UNESCAPED_UNICODE);
                    } elseif (empty($value)) {
                        $updates[$column] = json_encode(['en' => ''], JSON_UNESCAPED_UNICODE);
                    }
                }
                
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
            
            // We consciously avoid $table->json()->change() to prevent doctrine/dbal errors 
            // on some MariaDB/MySQL versions with existing text data.
            // Spatie Translatable handles JSON strings inside TEXT/VARCHAR correctly.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting would mean parsing the JSON and extracting 'en'. 
        // For brevity, we leave it as JSON since it's backward compatible or write a reverse script.
    }
};
