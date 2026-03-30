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
        Schema::table('ambulances', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('whatsapp', 20)->nullable()->after('phone');
            $table->string('backup_phone', 20)->nullable()->after('whatsapp');
            $table->json('features')->nullable()->after('notes');
            $table->text('summary')->nullable()->after('features');
            $table->string('meta_title')->nullable()->after('summary');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->boolean('is_verified')->default(false)->after('active');
            $table->boolean('is_featured')->default(false)->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'whatsapp', 'backup_phone', 
                'features', 'summary', 'meta_title', 'meta_description', 
                'is_verified', 'is_featured'
            ]);
        });
    }
};
