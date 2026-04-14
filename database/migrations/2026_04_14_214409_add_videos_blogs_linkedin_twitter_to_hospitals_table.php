<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('linkedin_url')->nullable()->after('youtube_url');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            $table->json('videos')->nullable()->after('blog_url');
            $table->json('blogs')->nullable()->after('videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['linkedin_url', 'twitter_url', 'videos', 'blogs']);
        });
    }
};
