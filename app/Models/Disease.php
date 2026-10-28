<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Disease extends Model
{
    use SoftDeletes, HasSeo, Publishable, HasTranslations;

    protected $fillable = [
        'primary_specialty_id',
        'body_system_id',
        'name',
        'slug',
        'first_letter',
        'aliases',
        'short_description',
        'overview',
        'symptoms_summary',
        'symptoms_detailed',
        'causes',
        'risk_factors',
        'complications',
        'prevention',
        'when_to_see_doctor',
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
        'diagnosis_treatment',
        'image',
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
        'is_featured',
        'is_emergency',
        'status',
        'published_at',
        'view_count',
    ];

    public array $translatable = [
        'name',
        'short_description',
        'overview',
        'symptoms_summary',
        'symptoms_detailed',
        'causes',
        'risk_factors',
        'complications',
        'prevention',
        'when_to_see_doctor',
        'diagnosis_overview',
        'treatment_overview',
        'clinical_trials',
        'lifestyle_home_remedies',
        'alternative_medicine',
        'coping_support',
        'preparing_for_appointment',
        'featured_image_alt',
        'featured_image_caption',
        'disclaimer',
        'diagnosis_treatment',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_emergency' => 'boolean',
        'published_at' => 'datetime',
        'last_reviewed_at' => 'date',
        'view_count' => 'integer',
        'aliases' => 'array',
        'red_flags' => 'array',
        'diagnosis_tests' => 'array',
        'treatments' => 'array',
        'medicines' => 'array',
        'procedures' => 'array',
        'faqs' => 'array',
        'body_images' => 'array',
        'related_conditions' => 'array',
        'related_articles' => 'array',
        'related_hospitals' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function ($disease) {
            if (empty($disease->first_letter)) {
                $enName = $disease->getTranslation('name', 'en', false) ?: $disease->slug;
                $clean = strtoupper(substr(trim($enName), 0, 1));
                if (preg_match('/[A-Z]/', $clean)) {
                    $disease->first_letter = $clean;
                } else {
                    $disease->first_letter = 'A';
                }
            }
        });
    }

    public function primarySpecialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'primary_specialty_id');
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'disease_specialty');
    }

    public function bodySystem(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class, 'body_system_id');
    }

    public function reviewerDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'reviewer_doctor_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'disease_symptom')
                    ->withPivot('is_primary', 'match_weight', 'notes')
                    ->withTimestamps();
    }

    public function references(): HasMany
    {
        return $this->hasMany(DiseaseReference::class)->orderBy('order_index', 'asc');
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Localized helper for Red Flags
     */
    public function getLocalizedRedFlags(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $flags = $this->red_flags ?: [];
        $result = [];

        foreach ($flags as $flag) {
            if (is_array($flag)) {
                $text = $flag[$locale] ?? $flag[$fallback] ?? reset($flag);
            } else {
                $text = (string) $flag;
            }
            if (!empty(trim((string) $text))) {
                $result[] = trim((string) $text);
            }
        }

        return $result;
    }

    /**
     * Localized helper for Diagnostic Tests
     */
    public function getLocalizedDiagnosisTests(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $tests = $this->diagnosis_tests ?: [];
        $result = [];

        foreach ($tests as $t) {
            if (!is_array($t)) continue;
            $name = $t["name_{$locale}"] ?? $t['name'][$locale] ?? $t["name_{$fallback}"] ?? $t['name'] ?? '';
            $desc = $t["desc_{$locale}"] ?? $t['desc'][$locale] ?? $t["description_{$locale}"] ?? $t["desc_{$fallback}"] ?? $t['desc'] ?? '';
            if (!empty($name) || !empty($desc)) {
                $result[] = [
                    'name' => is_array($name) ? ($name[$locale] ?? reset($name)) : $name,
                    'desc' => is_array($desc) ? ($desc[$locale] ?? reset($desc)) : $desc,
                ];
            }
        }

        return $result;
    }

    /**
     * Localized helper for Treatments
     */
    public function getLocalizedTreatments(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $treatments = $this->treatments ?: [];
        $result = [];

        foreach ($treatments as $t) {
            if (!is_array($t)) continue;
            $category = $t["category_{$locale}"] ?? $t['category'][$locale] ?? $t["category_{$fallback}"] ?? $t['category'] ?? '';
            $name = $t["name_{$locale}"] ?? $t['name'][$locale] ?? $t["name_{$fallback}"] ?? $t['name'] ?? '';
            $desc = $t["desc_{$locale}"] ?? $t['desc'][$locale] ?? $t["description_{$locale}"] ?? $t["desc_{$fallback}"] ?? $t['desc'] ?? '';
            if (!empty($name) || !empty($desc) || !empty($category)) {
                $result[] = [
                    'category' => is_array($category) ? ($category[$locale] ?? reset($category)) : $category,
                    'name' => is_array($name) ? ($name[$locale] ?? reset($name)) : $name,
                    'desc' => is_array($desc) ? ($desc[$locale] ?? reset($desc)) : $desc,
                ];
            }
        }

        return $result;
    }

    /**
     * Localized helper for Medicines
     */
    public function getLocalizedMedicines(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $medicines = $this->medicines ?: [];
        $result = [];

        foreach ($medicines as $m) {
            if (!is_array($m)) continue;
            $name = $m["name_{$locale}"] ?? $m['name'][$locale] ?? $m["name_{$fallback}"] ?? $m['name'] ?? '';
            $type = $m['type'] ?? 'prescription';
            $dosageNotes = $m["dosage_notes_{$locale}"] ?? $m['dosage_notes'][$locale] ?? $m["dosage_notes_{$fallback}"] ?? $m['dosage_notes'] ?? '';
            if (!empty($name)) {
                $result[] = [
                    'type' => $type,
                    'name' => is_array($name) ? ($name[$locale] ?? reset($name)) : $name,
                    'dosage_notes' => is_array($dosageNotes) ? ($dosageNotes[$locale] ?? reset($dosageNotes)) : $dosageNotes,
                ];
            }
        }

        return $result;
    }

    /**
     * Localized helper for Procedures / Therapies
     */
    public function getLocalizedProcedures(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $procedures = $this->procedures ?: [];
        $result = [];

        foreach ($procedures as $p) {
            if (!is_array($p)) continue;
            $name = $p["name_{$locale}"] ?? $p['name'][$locale] ?? $p["name_{$fallback}"] ?? $p['name'] ?? '';
            $desc = $p["desc_{$locale}"] ?? $p['desc'][$locale] ?? $p["desc_{$fallback}"] ?? $p['desc'] ?? '';
            if (!empty($name)) {
                $result[] = [
                    'name' => is_array($name) ? ($name[$locale] ?? reset($name)) : $name,
                    'desc' => is_array($desc) ? ($desc[$locale] ?? reset($desc)) : $desc,
                ];
            }
        }

        return $result;
    }

    /**
     * Localized helper for FAQs
     */
    public function getLocalizedFaqs(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $faqs = $this->faqs ?: [];
        $result = [];

        foreach ($faqs as $f) {
            if (!is_array($f)) continue;
            $q = $f["question_{$locale}"] ?? $f['question'][$locale] ?? $f["question_{$fallback}"] ?? $f['question'] ?? '';
            $a = $f["answer_{$locale}"] ?? $f['answer'][$locale] ?? $f["answer_{$fallback}"] ?? $f['answer'] ?? '';
            if (!empty($q) && !empty($a)) {
                $result[] = [
                    'question' => is_array($q) ? ($q[$locale] ?? reset($q)) : $q,
                    'answer' => is_array($a) ? ($a[$locale] ?? reset($a)) : $a,
                ];
            }
        }

        return $result;
    }

    /**
     * Localized helper for Body Images
     */
    public function getLocalizedBodyImages(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = $locale === 'bn' ? 'en' : 'bn';
        $images = $this->body_images ?: [];
        $result = [];

        foreach ($images as $img) {
            if (!is_array($img) || empty($img['url'])) continue;
            $alt = $img["alt_{$locale}"] ?? $img['alt'][$locale] ?? $img["alt_{$fallback}"] ?? $img['alt'] ?? '';
            $caption = $img["caption_{$locale}"] ?? $img['caption'][$locale] ?? $img["caption_{$fallback}"] ?? $img['caption'] ?? '';
            $result[] = [
                'url' => $img['url'],
                'alt' => is_array($alt) ? ($alt[$locale] ?? reset($alt)) : $alt,
                'caption' => is_array($caption) ? ($caption[$locale] ?? reset($caption)) : $caption,
            ];
        }

        return $result;
    }

    /**
     * Get relevant doctors for this disease based on primary and secondary specialties.
     */
    public function getRelevantDoctorsQuery()
    {
        $specialtyIds = collect([$this->primary_specialty_id])
            ->filter()
            ->merge($this->specialties()->pluck('specialties.id'))
            ->unique()
            ->values()
            ->all();

        return Doctor::query()
            ->whereHas('specialties', function ($q) use ($specialtyIds) {
                $q->whereIn('specialties.id', $specialtyIds);
            })
            ->with(['specialties', 'chambers']);
    }
}
