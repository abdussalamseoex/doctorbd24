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
        Schema::create('hospital_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('service_category')->nullable();
            $table->string('service_name');
            $table->string('price')->nullable(); // String because it could say "Contact us" or range
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('hospital_id');
            $table->index('service_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_services');
    }
};
