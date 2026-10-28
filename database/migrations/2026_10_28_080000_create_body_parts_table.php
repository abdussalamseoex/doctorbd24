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
        Schema::create('body_parts', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable: {"en": "Head & Brain", "bn": "মাথা ও মস্তিষ্ক"}
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_parts');
    }
};
