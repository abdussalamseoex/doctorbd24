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
        Schema::table('doctors', function (Blueprint $table) {
            $table->boolean('is_duplicate_ignored')->default(false)->after('verified');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->boolean('is_duplicate_ignored')->default(false)->after('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('is_duplicate_ignored');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('is_duplicate_ignored');
        });
    }
};
