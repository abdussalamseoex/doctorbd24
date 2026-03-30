<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Polymorphic: reviewable = Doctor or Hospital
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // reviewable_type, reviewable_id
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamp('approved_at')->nullable(); // null = pending
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id', 'approved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
