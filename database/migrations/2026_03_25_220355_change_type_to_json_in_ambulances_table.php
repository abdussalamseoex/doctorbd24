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
            $table->dropColumn('type');
        });
        Schema::table('ambulances', function (Blueprint $table) {
            $table->json('type')->nullable()->after('provider_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('ambulances', function (Blueprint $table) {
            $table->enum('type', ['ac', 'non_ac', 'icu', 'freezing'])->after('provider_name');
        });
    }
};
