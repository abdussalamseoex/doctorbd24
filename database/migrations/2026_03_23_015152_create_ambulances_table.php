<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->enum('type', ['ac', 'non_ac', 'icu', 'freezing'])->default('non_ac');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('available_24h')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulances');
    }
};
