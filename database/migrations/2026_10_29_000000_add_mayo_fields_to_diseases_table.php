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
        Schema::table('diseases', function (Blueprint $table) {
            // Taxonomy & Basic Identifiers
            $table->foreignId('body_system_id')->nullable()->after('primary_specialty_id')->constrained('body_parts')->nullOnDelete();
            $table->json('aliases')->nullable()->after('slug');
            $table->json('short_description')->nullable()->after('aliases');

            // Symptoms & Warning Signs
            $table->json('symptoms_detailed')->nullable()->after('symptoms_summary');
            $table->json('red_flags')->nullable()->after('when_to_see_doctor');

            // Diagnosis & Treatment Sections (Separated + Repeatable Lists)
            $table->json('diagnosis_overview')->nullable()->after('red_flags');
            $table->json('diagnosis_tests')->nullable()->after('diagnosis_overview');
            $table->json('treatment_overview')->nullable()->after('diagnosis_tests');
            $table->json('treatments')->nullable()->after('treatment_overview');
            $table->json('medicines')->nullable()->after('treatments');
            $table->json('procedures')->nullable()->after('medicines');
            $table->json('clinical_trials')->nullable()->after('procedures');

            // Lifestyle, Coping, Alternative & Appointment Prep
            $table->json('lifestyle_home_remedies')->nullable()->after('clinical_trials');
            $table->json('alternative_medicine')->nullable()->after('lifestyle_home_remedies');
            $table->json('coping_support')->nullable()->after('alternative_medicine');
            $table->json('preparing_for_appointment')->nullable()->after('coping_support');

            // Interactive FAQ & Visual Media
            $table->json('faqs')->nullable()->after('preparing_for_appointment');
            $table->json('featured_image_alt')->nullable()->after('image');
            $table->json('featured_image_caption')->nullable()->after('featured_image_alt');
            $table->json('body_images')->nullable()->after('featured_image_caption');

            // Author, Medical Reviewer & Review Dates (E-E-A-T)
            $table->foreignId('author_id')->nullable()->after('body_images')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_doctor_id')->nullable()->after('author_id')->constrained('doctors')->nullOnDelete();
            $table->date('last_reviewed_at')->nullable()->after('published_at');

            // Related Entities & Disclaimer Override
            $table->json('related_conditions')->nullable()->after('reviewer_doctor_id');
            $table->json('related_articles')->nullable()->after('related_conditions');
            $table->json('related_hospitals')->nullable()->after('related_articles');
            $table->json('disclaimer')->nullable()->after('related_hospitals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->dropForeign(['body_system_id']);
            $table->dropForeign(['author_id']);
            $table->dropForeign(['reviewer_doctor_id']);

            $table->dropColumn([
                'body_system_id',
                'aliases',
                'short_description',
                'symptoms_detailed',
                'red_flags',
                'diagnosis_overview',
                'diagnosis_tests',
                'treatment_overview',
                'treatments',
                'medicines',
                'procedures',
                'clinical_trials',
                'lifestyle_home_remedies',
                'alternative_medicine',
                'coping_support',
                'preparing_for_appointment',
                'faqs',
                'featured_image_alt',
                'featured_image_caption',
                'body_images',
                'author_id',
                'reviewer_doctor_id',
                'last_reviewed_at',
                'related_conditions',
                'related_articles',
                'related_hospitals',
                'disclaimer',
            ]);
        });
    }
};
