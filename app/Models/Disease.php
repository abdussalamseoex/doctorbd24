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
        'name',
        'slug',
        'first_letter',
        'overview',
        'symptoms_summary',
        'causes',
        'risk_factors',
        'complications',
        'prevention',
        'when_to_see_doctor',
        'diagnosis_treatment',
        'image',
        'is_featured',
        'is_emergency',
        'status',
        'published_at',
        'view_count',
    ];

    public array $translatable = [
        'name',
        'overview',
        'symptoms_summary',
        'causes',
        'risk_factors',
        'complications',
        'prevention',
        'when_to_see_doctor',
        'diagnosis_treatment',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_emergency' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
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
