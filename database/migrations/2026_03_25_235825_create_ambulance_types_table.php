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
        Schema::create('ambulance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed existing 11 types immediately to prevent crash
        $types = [
            ['slug' => 'ac', 'name' => 'AC Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'non_ac', 'name' => 'Non-AC Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'icu', 'name' => 'ICU Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'ccu', 'name' => 'CCU Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'ventilator', 'name' => 'Ventilator Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'freezing', 'name' => 'Freezing Ambulance (Dead Body)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'air', 'name' => 'Air Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'boat', 'name' => 'Boat Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'neonatal', 'name' => 'Neonatal Ambulance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'bls', 'name' => 'Basic Life Support (BLS)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'als', 'name' => 'Advanced Life Support (ALS)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('ambulance_types')->insert($types);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulance_types');
    }
};
