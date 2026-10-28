<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Symptom extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
        'body_part_id',
        'name',
        'slug',
        'first_letter',
        'description',
        'gender_scope',
        'age_group',
        'urgency_level',
        'is_emergency',
        'is_featured',
        'status',
    ];

    public array $translatable = ['name', 'description'];

    protected $casts = [
        'is_emergency' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class);
    }

    public function factors(): HasMany
    {
        return $this->hasMany(SymptomFactor::class)->orderBy('order_index', 'asc');
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'disease_symptom')
                    ->withPivot('is_primary', 'match_weight', 'notes')
                    ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForGender($query, $gender)
    {
        return $query->whereIn('gender_scope', ['all', $gender]);
    }

    public function scopeForAgeGroup($query, $ageGroup)
    {
        return $query->whereIn('age_group', ['all', $ageGroup]);
    }
}
