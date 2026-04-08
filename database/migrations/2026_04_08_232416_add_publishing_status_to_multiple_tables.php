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
        // 1. Doctors
        Schema::table('doctors', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('status');
        });
        DB::table('doctors')->where('status', 'published')->update(['published_at' => now()]);

        // 2. Hospitals
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('is_duplicate_ignored');
            $table->timestamp('published_at')->nullable()->after('status');
        });
        DB::table('hospitals')->update(['status' => 'published', 'published_at' => now()]);

        // 3. Ambulances
        Schema::table('ambulances', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('rating_count');
            $table->timestamp('published_at')->nullable()->after('status');
        });
        DB::table('ambulances')->where('active', 1)->update(['status' => 'published', 'published_at' => now()]);
        DB::table('ambulances')->where('active', 0)->update(['status' => 'draft']);
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        // 4. Pages
        Schema::table('pages', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('is_active');
            $table->timestamp('published_at')->nullable()->after('status');
        });
        DB::table('pages')->where('is_active', 1)->update(['status' => 'published', 'published_at' => now()]);
        DB::table('pages')->where('is_active', 0)->update(['status' => 'draft']);
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        // 5. SEO Landing Pages
        Schema::table('seo_landing_pages', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('is_active');
            $table->timestamp('published_at')->nullable()->after('status');
        });
        DB::table('seo_landing_pages')->where('is_active', 1)->update(['status' => 'published', 'published_at' => now()]);
        DB::table('seo_landing_pages')->where('is_active', 0)->update(['status' => 'draft']);
        Schema::table('seo_landing_pages', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        // 6. Blog Posts
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('meta_description');
        });
        DB::table('blog_posts')->whereNotNull('published_at')->where('published_at', '<=', now())->update(['status' => 'published']);
        DB::table('blog_posts')->whereNotNull('published_at')->where('published_at', '>', now())->update(['status' => 'scheduled']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at']);
        });

        Schema::table('ambulances', function (Blueprint $table) {
            $table->boolean('active')->default(1)->after('notes');
        });
        DB::table('ambulances')->where('status', 'published')->update(['active' => 1]);
        DB::table('ambulances')->where('status', 'draft')->update(['active' => 0]);
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('content');
        });
        DB::table('pages')->where('status', 'published')->update(['is_active' => 1]);
        DB::table('pages')->where('status', 'draft')->update(['is_active' => 0]);
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at']);
        });

        Schema::table('seo_landing_pages', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('faq_schema');
        });
        DB::table('seo_landing_pages')->where('status', 'published')->update(['is_active' => 1]);
        DB::table('seo_landing_pages')->where('status', 'draft')->update(['is_active' => 0]);
        Schema::table('seo_landing_pages', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
