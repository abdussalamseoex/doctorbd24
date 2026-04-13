<?php

namespace App\CacheProfiles;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

class CacheGuestsOnlyProfile extends CacheAllSuccessfulGetRequests
{
    public function shouldCacheRequest(Request $request): bool
    {
        // Don't cache if the user is authenticated (to prevent showing cached guest navbars to logged-in users)
        if (auth()->check()) {
            return false;
        }

        return parent::shouldCacheRequest($request);
    }
}
