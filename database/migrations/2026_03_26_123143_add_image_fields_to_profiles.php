<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('photo');
            $table->json('gallery')->nullable()->after('cover_image');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('banner');
        });

        Schema::table('ambulances', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('type');
            $table->string('cover_image')->nullable()->after('logo');
            $table->json('gallery')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'gallery']);
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('gallery');
        });

        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn(['logo', 'cover_image', 'gallery']);
        });
    }
};
