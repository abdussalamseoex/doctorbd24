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
        Schema::table('claim_requests', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->change();
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->cascadeOnDelete()->after('doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_requests', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn('hospital_id');
            // doctor_id was originally non-nullable, but rolling back `nullable()` requires doctrine/dbal. Just leave it nullable.
        });
    }
};
