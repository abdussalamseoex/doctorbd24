<?php

namespace App\Models;

use App\Traits\Publishable;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Hospital extends Model
{
    use SoftDeletes, HasSeo, LogsActivity, Publishable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'status', 'published_at',
        'user_id', 'name', 'slug', 'type', 'logo', 'banner', 'gallery', 'about',
        'phone', 'email', 'website', 'address', 'area_id',
        'lat', 'lng', 'google_maps_url', 'verified', 'featured', 'view_count',
        'rating_avg', 'rating_count',
        'facebook_url', 'instagram_url', 'youtube_url', 'services', 'opening_hours',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'verified'      => 'boolean',
        'featured'      => 'boolean',
        'view_count'    => 'integer',
        'lat'           => 'float',
        'lng'           => 'float',
        'services'      => 'array',
        'opening_hours' => 'array',
        'gallery'       => 'array',
    ];


    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Doctors who have chambers at this hospital
    public function chambers(): HasMany
    {
        return $this->hasMany(Chamber::class);
    }

    public function doctors()
    {
        return Doctor::whereHas('chambers', fn ($q) => $q->where('hospital_id', $this->id));
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable')->whereNotNull('approved_at');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
