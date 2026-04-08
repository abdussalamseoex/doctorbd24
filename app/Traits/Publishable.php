<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    /**
     * Scope a query to only include published items.
     * published means status = 'published' AND (published_at is null OR published_at <= now())
     * Note: Depending on logic, usually published_at shouldn't be null for published, but it acts as a fallback.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', Carbon::now());
                     });
    }

    /**
     * Scope a query to only include drafted items.
     */
    public function scopeDrafted(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include scheduled items.
     * Scheduled means either status = 'scheduled'
     * or status = 'published' but published_at is in the future.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
                     ->orWhere(function ($q) {
                         $q->where('status', 'published')
                           ->whereNotNull('published_at')
                           ->where('published_at', '>', Carbon::now());
                     });
    }

    /**
     * Determine if the item is currently published and live.
     */
    public function getIsLiveAttribute(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        return true;
    }
}
