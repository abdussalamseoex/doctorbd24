<?php

namespace App\Models;

use App\Traits\Publishable;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BlogPost extends Model
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
        'status',
        'blog_category_id', 'user_id', 'title', 'slug', 'excerpt',
        'body', 'image', 'meta_title', 'meta_description', 'published_at', 'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    /**
     * Virtual accessor: $post->published
     * Returns true if published_at is set and in the past.
     */
    public function getPublishedAttribute(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
