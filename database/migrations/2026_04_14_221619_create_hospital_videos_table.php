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
        Schema::create('hospital_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('youtube'); // youtube, facebook, vimeo, custom
            $table->string('video_url');
            $table->string('youtube_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['hospital_id', 'slug']);
        });

        // Drop the old json column as we are moving to relations
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_videos');
        Schema::table('hospitals', function (Blueprint $table) {
            $table->json('videos')->nullable();
        });
    }
};
