<?php

namespace App\Models;

use App\Traits\Publishable;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Translatable\HasTranslations;

class Ambulance extends Model
{
    use HasSeo, LogsActivity, Publishable, HasTranslations;

    public $translatable = ['provider_name', 'summary', 'notes', 'address'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'status', 'published_at',
        'user_id', 'provider_name', 'slug', 'type', 'logo', 'cover_image', 'gallery', 'phone', 'whatsapp', 'backup_phone', 'address', 'latitude', 'longitude', 'area_id', 'available_24h', 'features', 'summary', 'notes', 'meta_title', 'meta_description', 'is_verified', 'is_featured', 'view_count',
        'rating_avg', 'rating_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'available_24h' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'type' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'gallery' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public static function typeMap(): array
    {
        return \App\Models\AmbulanceType::pluck('name', 'slug')->toArray();
    }

    public function getTypeLabelsArray(): array
    {
        if (empty($this->type)) {
            return [__('Ambulance')];
        }
        
        $types = is_array($this->type) ? $this->type : [$this->type];
        $map = self::typeMap();
        $labels = [];
        
        foreach ($types as $t) {
            if (isset($map[$t])) {
                $labels[] = __($map[$t]);
            }
        }
        
        return empty($labels) ? [__('Ambulance')] : $labels;
    }

    public function getTypeLabel(): string
    {
        return implode(', ', $this->getTypeLabelsArray());
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable')->whereNotNull('approved_at');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->where('is_approved', true)->avg('rating') ?: 0, 1);
    }
    
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
