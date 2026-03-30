<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Doctor extends Model
{
    use SoftDeletes, HasSeo, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'name', 'slug', 'photo', 'cover_image', 'gallery', 'gender', 'qualifications', 'designation',
        'bio', 'experience_years', 'verified', 'featured', 'view_count',
        'rating_avg', 'rating_count',
        'phone', 'email', 'bmdc_number', 'language', 'user_id',
        'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'youtube_url'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'featured' => 'boolean',
        'experience_years' => 'integer',
        'view_count' => 'integer',
        'gallery' => 'array',
    ];

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty');
    }

    public function chambers()
    {
        return $this->hasMany(Chamber::class)->orderBy('sort_order');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
