<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent "/public" from appearing in generated URLs when using cPanel or misconfigured server document root
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        if (\Illuminate\Support\Str::startsWith(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \App\Models\Review::observe(\App\Observers\ReviewObserver::class);

        // Auto-heal broken storage symlinks usually caused by cPanel/GitHub deployments
        if (!file_exists(public_path('storage'))) {
            try {
                if (is_link(public_path('storage'))) {
                    @unlink(public_path('storage'));
                }
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {}
        }
    }
}
