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
        Schema::create('symptom_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('symptom_id')->constrained('symptoms')->cascadeOnDelete();
            $table->string('factor_type', 50); // duration, quality, trigger, accompanying
            $table->json('question'); // Translatable question: {"en": "What type of pain do you feel?", "bn": "ব্যথার ধরন কেমন?"}
            $table->json('options'); // Array of options: [{"id": "throbbing", "text_en": "Throbbing or pulsing", "text_bn": "দপদপে বা স্পন্দনশীল", "red_flag": false, "weight": 5}]
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom_factors');
    }
};
