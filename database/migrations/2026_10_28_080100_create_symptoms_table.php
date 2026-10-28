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
        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('body_part_id')->nullable()->constrained('body_parts')->nullOnDelete();
            $table->json('name'); // Translatable: {"en": "Headache", "bn": "মাথাব্যথা"}
            $table->string('slug')->unique();
            $table->string('first_letter', 10)->nullable()->index();
            $table->json('description')->nullable();
            $table->string('gender_scope', 20)->default('all'); // all, male, female
            $table->string('age_group', 20)->default('all'); // all, adult, child
            $table->string('urgency_level', 20)->default('routine'); // emergency, urgent, routine
            $table->boolean('is_emergency')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('status', 20)->default('published')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptoms');
    }
};
