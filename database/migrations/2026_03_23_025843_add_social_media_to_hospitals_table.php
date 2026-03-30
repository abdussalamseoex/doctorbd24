<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('google_maps_url');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->json('services')->nullable()->after('youtube_url'); // array of service strings
            $table->json('opening_hours')->nullable()->after('services'); // e.g. {"sat":"8am-8pm", ...}
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['facebook_url', 'instagram_url', 'youtube_url', 'services', 'opening_hours']);
        });
    }
};
