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
use Spatie\Translatable\HasTranslations;

class BlogPost extends Model
{
    use SoftDeletes, HasSeo, LogsActivity, Publishable, HasTranslations;

    public $translatable = ['title', 'excerpt', 'body'];

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
     * Returns true if post is live.
     */
    public function getPublishedAttribute(): bool
    {
        return $this->is_live;
    }

    /**
     * Determine if this post is currently published and live on frontend.
     */
    public function getIsLiveAttribute(): bool
    {
        if ($this->status === 'published') {
            return $this->published_at ? $this->published_at->isPast() : true;
        }

        if ($this->status === 'scheduled') {
            return $this->published_at ? $this->published_at->isPast() : false;
        }

        return false;
    }

    /**
     * Determine if this post is currently scheduled for future release.
     */
    public function getIsScheduledAttribute(): bool
    {
        if ($this->status === 'scheduled') {
            return $this->published_at ? $this->published_at->isFuture() : true;
        }

        if ($this->status === 'published' && $this->published_at && $this->published_at->isFuture()) {
            return true;
        }

        return false;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for live published posts on the frontend.
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'published')
              ->where(function ($sub) {
                  $sub->whereNull('published_at')
                      ->orWhere('published_at', '<=', now());
              });
        })->orWhere(function ($q) {
            $q->where('status', 'scheduled')
              ->whereNotNull('published_at')
              ->where('published_at', '<=', now());
        });
    }

    /**
     * Scope for scheduled posts.
     */
    public function scopeScheduled($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'scheduled')
              ->whereNotNull('published_at')
              ->where('published_at', '>', now());
        })->orWhere(function ($q) {
            $q->where('status', 'published')
              ->whereNotNull('published_at')
              ->where('published_at', '>', now());
        });
    }

    /**
     * Scope for draft posts.
     */
    public function scopeDrafted($query)
    {
        return $query->where('status', 'draft')
                     ->orWhere(function ($q) {
                         $q->whereNull('status');
                     });
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
