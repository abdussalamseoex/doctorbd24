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
        Schema::create('ambulance_features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed existing 8 features immediately to prevent form loss
        $features = [
            ['name' => 'Oxygen Support', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Life Support / ICU', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paramedic on Board', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'First Aid Kit', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wheelchair Friendly', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Air Conditioned', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Freezing Frame', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Neonatal Incubator', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('ambulance_features')->insert($features);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulance_features');
    }
};
