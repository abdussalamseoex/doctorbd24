<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->text('qualifications')->nullable(); // stored as text e.g. "MBBS, FCPS"
            $table->string('designation')->nullable(); // e.g. "Senior Consultant"
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('experience_years')->default(0);
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bmdc_number')->nullable();
            $table->string('language')->default('bn'); // bn or en profile
            $table->timestamps();
            $table->softDeletes();

            $table->index(['verified', 'featured']);
            $table->index('experience_years');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
