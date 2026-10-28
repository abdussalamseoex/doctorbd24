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
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->json('name'); // Translatable: {"en": "Migraine", "bn": "মাইগ্রেন"}
            $table->string('slug')->unique();
            $table->string('first_letter', 10)->nullable()->index();
            
            // Rich Content Sections (TinyMCE HTML, Translatable: en & bn)
            $table->json('overview')->nullable();
            $table->json('symptoms_summary')->nullable();
            $table->json('causes')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('complications')->nullable();
            $table->json('prevention')->nullable();
            $table->json('when_to_see_doctor')->nullable();
            $table->json('diagnosis_treatment')->nullable();

            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_emergency')->default(false)->index();
            $table->string('status', 20)->default('published')->index();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
