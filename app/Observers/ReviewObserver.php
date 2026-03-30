<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->updateRating($review);
    }

    public function updated(Review $review): void
    {
        $this->updateRating($review);
    }

    public function deleted(Review $review): void
    {
        $this->updateRating($review);
    }

    protected function updateRating(Review $review): void
    {
        $model = $review->reviewable;
        if ($model) {
            $avg = $model->reviews()->whereNotNull('approved_at')->avg('rating') ?: 0;
            $count = $model->reviews()->whereNotNull('approved_at')->count();
            
            $model->update([
                'rating_avg'   => $avg,
                'rating_count' => $count
            ]);
        }
    }
}
