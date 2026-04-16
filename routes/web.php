<?php

use App\Http\Controllers\AmbulanceController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

// â”€â”€ API Routes (Public) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::name('api.')->prefix('api')->group(function () {
    Route::get('/districts', [\App\Http\Controllers\Api\LocationController::class, 'getDistricts'])->name('districts');
    Route::get('/areas', [\App\Http\Controllers\Api\LocationController::class, 'getAreas'])->name('areas');
    Route::get('/areas/{area}', [\App\Http\Controllers\Api\LocationController::class, 'show'])->name('areas.show');
});

// ── Language Switcher ──────────────────────────────────────────
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');



// ── Public Routes ──────────────────────────────────────────────────
$publicRoutes = function () {

    Route::get('/', [HomeController::class, 'index'])->middleware('cache.response')->name('home');

    // PWA Manifest
    Route::get('/manifest.json', function () {
        $faviconUrl = setting('favicon') ? asset('storage/'.setting('favicon')) : asset('favicon.ico');
        $name = setting('site_name', 'DoctorBD24');
        
        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#0A2540',
            'icons' => [
                [
                    'src' => $faviconUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => $faviconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ]
            ]
        ]);
    });

    // AI and LLM indexing files
    Route::get('/llms.txt', [\App\Http\Controllers\LlmsController::class, 'index'])->name('llms.txt');
    Route::get('/llms-full.txt', [\App\Http\Controllers\LlmsController::class, 'full'])->name('llms.full.txt');

    // Contact Page
    Route::get('/contact', [\App\Http\Controllers\PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [\App\Http\Controllers\PageController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:5,10');

    // ── Doctor Portal ──────────────────────
    Route::prefix('doctor')->middleware(['auth', 'role:doctor'])->name('doctor.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, '__invoke'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\DoctorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\DoctorProfileController::class, 'update'])->name('profile.update');
        Route::get('/reviews', [\App\Http\Controllers\ProviderReviewController::class, 'index'])->name('reviews.index');
    });

    // ── Hospital Portal ────────────────────
    Route::prefix('hospital')->middleware(['auth', 'role:hospital'])->name('hospital.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, '__invoke'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\HospitalProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\HospitalProfileController::class, 'update'])->name('profile.update');
        Route::get('/reviews', [\App\Http\Controllers\ProviderReviewController::class, 'index'])->name('reviews.index');
    });

    Route::middleware(['cache.response'])->group(function () {
        Route::get('/doctors', [\App\Http\Controllers\DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctor/{slug}/{tab?}', [\App\Http\Controllers\DoctorController::class, 'show'])->where('tab', 'overview|videos|blog')->name('doctors.show');
        Route::get('/hospitals', [\App\Http\Controllers\HospitalController::class, 'index'])->name('hospitals.index');
        Route::get('/hospital/{slug}/{tab?}', [\App\Http\Controllers\HospitalController::class, 'show'])->where('tab', 'overview|doctors|diagnostics|video|blog')->name('hospitals.show');
        Route::get('/hospital/{slug}/diagnostics/{service_slug}', [\App\Http\Controllers\HospitalController::class, 'showDiagnosticTest'])->name('hospitals.diagnostic.show');
        Route::get('/hospital/{hospital_slug}/video/{video_slug}', [\App\Http\Controllers\VideoController::class, 'show'])->name('video.show');
        Route::get('/doctor/{doctor_slug}/video/{video_slug}', [\App\Http\Controllers\VideoController::class, 'showDoctorVideo'])->name('doctor.video.show');
        Route::get('/specialties', [\App\Http\Controllers\SpecialtyController::class, 'index'])->name('specialties.index');
        Route::get('/ambulances', [\App\Http\Controllers\AmbulanceController::class, 'index'])->name('ambulances.index');
        Route::get('/ambulance/{slug}', [\App\Http\Controllers\AmbulanceController::class, 'resolve'])->name('ambulances.type');
        Route::get('/ambulance/{slug}', [\App\Http\Controllers\AmbulanceController::class, 'resolve'])->name('ambulances.show');
        Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
    });

    Route::post('/doctors/{doctor}/claim', [\App\Http\Controllers\ClaimRequestController::class, 'store'])->name('doctors.claim')->middleware('auth');
    Route::post('/hospitals/{hospital}/claim', [\App\Http\Controllers\ClaimRequestController::class, 'storeHospital'])->name('hospitals.claim')->middleware('auth');
    Route::post('/ambulances/{ambulance}/claim', [\App\Http\Controllers\ClaimRequestController::class, 'storeAmbulance'])->name('ambulances.claim')->middleware('auth');

    // Join Forms
    Route::get('/join/doctor', [\App\Http\Controllers\JoinController::class, 'doctorForm'])->name('join.doctor');
    Route::post('/join/doctor', [\App\Http\Controllers\JoinController::class, 'submitDoctor'])->name('join.doctor.submit')->middleware('throttle:5,10');
    Route::get('/join/hospital', [\App\Http\Controllers\JoinController::class, 'hospitalForm'])->name('join.hospital');
    Route::post('/join/hospital', [\App\Http\Controllers\JoinController::class, 'submitHospital'])->name('join.hospital.submit')->middleware('throttle:5,10');

    Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');
};

// ── Apply English Group ──────────────────────────────────────
Route::middleware([])->group($publicRoutes);

// ── Apply Bengali Group ──────────────────────────────────────
Route::prefix('bn')->name('bn.')->group($publicRoutes);

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap/doctors.xml', [\App\Http\Controllers\SitemapController::class, 'doctors']);
Route::get('/sitemap/hospitals.xml', [\App\Http\Controllers\SitemapController::class, 'hospitals']);
Route::get('/sitemap/ambulances.xml', [\App\Http\Controllers\SitemapController::class, 'ambulances']);
Route::get('/sitemap/blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog']);

// Robots.txt
Route::get('/robots.txt', function () {
    $default = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login\nDisallow: /register\n\nSitemap: " . url('/sitemap.xml') . "\n";
    $content = \App\Models\Setting::get('robots_txt', $default);
    return response($content, 200)->header('Content-Type', 'text/plain');
});


// â”€â”€ Authenticated User Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    // User dashboard
    Route::get('/dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::patch('/dashboard/profile', [\App\Http\Controllers\UserDashboardController::class, 'updateProfile'])->name('user.profile.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Favorites
    Route::post('/favorites', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites/check', [\App\Http\Controllers\FavoriteController::class, 'check'])->name('favorites.check');
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');

    // Reviews
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Report Duplicate (Public or Auth)
Route::post('/report-duplicate', [\App\Http\Controllers\ReportDuplicateController::class, 'store'])->name('report-duplicate.store');




// (blog-posts moved into main admin group below)

// â”€â”€ Admin Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|editor|moderator'])
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Admin\AdminDashboardController::class)->name('dashboard');

        // System optimization route
        Route::get('/optimize-system', function () {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            if (class_exists(\Spatie\ResponseCache\Facades\ResponseCache::class)) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
            }
            \Illuminate\Support\Facades\Artisan::call('optimize');
            \Illuminate\Support\Facades\Artisan::call('view:cache');
            return redirect()->back()->with('success', 'System optimized and Response Cache flushed successfully! All configuration and views have been cached for maximum performance.');
        })->name('optimize-system')->middleware('permission:manage settings');


        // Doctors
        Route::get('doctors/import-progress', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'importProgress'])->name('doctors.import-progress')->middleware('permission:manage doctors');
        Route::post('doctors/import-popular', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'importPopular'])->name('doctors.import-popular')->middleware('permission:manage doctors');
        Route::post('doctors/import', \App\Http\Controllers\Admin\DoctorImportController::class)->name('doctors.import')->middleware('permission:manage doctors');
        Route::post('doctors/bulk-action', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'bulkAction'])->name('doctors.bulk-action')->middleware('permission:manage doctors');
        Route::resource('doctors', \App\Http\Controllers\Admin\AdminDoctorController::class)->middleware('permission:manage doctors');
        // Hospitals
        Route::post('hospitals/bulk-action', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'bulkAction'])->name('hospitals.bulk-action')->middleware('permission:manage hospitals');
        Route::post('hospitals/fetch-video', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'fetchVideoUrl'])->name('hospitals.fetch-video')->middleware('permission:manage hospitals');
        Route::post('hospitals/generate-video-description', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'generateVideoDescription'])->name('hospitals.generate-video-description')->middleware('permission:manage hospitals');
        Route::post('hospitals/fetch-channel-videos', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'fetchChannelVideos'])->name('hospitals.fetch-channel-videos')->middleware('permission:manage hospitals');
        Route::post('hospitals/fetch-blog', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'fetchBlogUrl'])->name('hospitals.fetch-blog')->middleware('permission:manage hospitals');
        Route::post('hospitals/fetch-url-meta', [\App\Http\Controllers\Admin\AdminHospitalController::class, 'fetchUrlMeta'])->name('hospitals.fetch-url-meta')->middleware('permission:manage hospitals');
        Route::resource('hospitals', \App\Http\Controllers\Admin\AdminHospitalController::class)->middleware('permission:manage hospitals');
        // Hospital Services
        Route::get('hospitals/{hospital}/services', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'index'])->name('hospitals.services.index')->middleware('permission:manage hospitals');
        Route::post('hospitals/{hospital}/services/import', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'import'])->name('hospitals.services.import')->middleware('permission:manage hospitals');
        Route::post('hospitals/{hospital}/services/generate-ai', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'generateBiDescription'])->name('hospitals.services.generate-ai')->middleware('permission:manage hospitals');
        Route::post('hospitals/{hospital}/services', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'store'])->name('hospitals.services.store')->middleware('permission:manage hospitals');
        Route::put('hospitals/{hospital}/services/{service}', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'update'])->name('hospitals.services.update')->middleware('permission:manage hospitals');
        Route::delete('hospitals/{hospital}/services/clear', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'clearAll'])->name('hospitals.services.clear')->middleware('permission:manage hospitals');
        Route::delete('hospitals/{hospital}/services/{service}', [\App\Http\Controllers\Admin\AdminHospitalServiceController::class, 'destroy'])->name('hospitals.services.destroy')->middleware('permission:manage hospitals');
        // Ambulances
        Route::post('ambulances/bulk-action', [\App\Http\Controllers\Admin\AdminAmbulanceController::class, 'bulkAction'])->name('ambulances.bulk-action')->middleware('permission:manage ambulances');
        Route::resource('ambulances', \App\Http\Controllers\Admin\AdminAmbulanceController::class)->middleware('permission:manage ambulances');
        Route::resource('ambulance-types', \App\Http\Controllers\Admin\AmbulanceTypeController::class)->except('show')->middleware('permission:manage settings');
        Route::resource('ambulance-features', \App\Http\Controllers\Admin\AmbulanceFeatureController::class)->except('show')->middleware('permission:manage settings');
        // Specialties
        Route::resource('specialties', \App\Http\Controllers\Admin\AdminSpecialtyController::class)->middleware('permission:manage settings');
        // Locations
        Route::get('locations', [\App\Http\Controllers\Admin\AdminLocationController::class, 'index'])->name('locations.index')->middleware('permission:manage settings');
        Route::post('locations/divisions', [\App\Http\Controllers\Admin\AdminLocationController::class, 'storeDivision'])->name('locations.divisions.store')->middleware('permission:manage settings');
        Route::put('locations/divisions/{division}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'updateDivision'])->name('locations.divisions.update')->middleware('permission:manage settings');
        Route::delete('locations/divisions/{division}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'destroyDivision'])->name('locations.divisions.destroy')->middleware('permission:manage settings');
        Route::post('locations/districts', [\App\Http\Controllers\Admin\AdminLocationController::class, 'storeDistrict'])->name('locations.districts.store')->middleware('permission:manage settings');
        Route::put('locations/districts/{district}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'updateDistrict'])->name('locations.districts.update')->middleware('permission:manage settings');
        Route::delete('locations/districts/{district}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'destroyDistrict'])->name('locations.districts.destroy')->middleware('permission:manage settings');
        Route::post('locations/areas', [\App\Http\Controllers\Admin\AdminLocationController::class, 'storeArea'])->name('locations.areas.store')->middleware('permission:manage settings');
        Route::put('locations/areas/{area}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'updateArea'])->name('locations.areas.update')->middleware('permission:manage settings');
        Route::delete('locations/areas/{area}', [\App\Http\Controllers\Admin\AdminLocationController::class, 'destroyArea'])->name('locations.areas.destroy')->middleware('permission:manage settings');

        // Join Requests
        Route::get('join-requests', \App\Http\Controllers\Admin\AdminJoinRequestController::class . '@index')->name('join-requests.index')->middleware('permission:manage users');
        Route::get('join-requests', \App\Http\Controllers\Admin\AdminJoinRequestController::class . '@index')->name('join-requests.index')->middleware('permission:manage users');
        Route::patch('join-requests/{id}/{action}', \App\Http\Controllers\Admin\AdminJoinRequestController::class . '@updateStatus')->name('join-requests.status')->middleware('permission:manage users');
        // Reviews
        Route::get('reviews', \App\Http\Controllers\Admin\AdminReviewController::class . '@index')->name('reviews.index')->middleware('permission:manage reviews');
        Route::patch('reviews/{review}/approve', \App\Http\Controllers\Admin\AdminReviewController::class . '@approve')->name('reviews.approve')->middleware('permission:manage reviews');
        Route::delete('reviews/{review}', \App\Http\Controllers\Admin\AdminReviewController::class . '@destroy')->name('reviews.destroy')->middleware('permission:manage reviews');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('settings.index')->middleware('permission:manage settings');
        Route::put('settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('settings.update')->middleware('permission:manage settings');

        // System Updater
        Route::get('updater', [\App\Http\Controllers\Admin\SystemUpdaterController::class, 'index'])->name('updater.index')->middleware('role:admin');
        Route::post('updater/run', [\App\Http\Controllers\Admin\SystemUpdaterController::class, 'run'])->name('updater.run')->middleware('role:admin');


        // AI Agent Settings & Generator Endpoint
        Route::get('ai-settings', [\App\Http\Controllers\Admin\AiSettingController::class, 'index'])->name('ai-settings.index')->middleware('permission:manage settings');
        Route::get('ai-prompts', [\App\Http\Controllers\Admin\AiSettingController::class, 'prompts'])->name('ai-prompts.index')->middleware('permission:manage settings');
        Route::put('ai-prompts', [\App\Http\Controllers\Admin\AiSettingController::class, 'promptsUpdate'])->name('ai-prompts.update')->middleware('permission:manage settings');
        Route::put('ai-settings', [\App\Http\Controllers\Admin\AiSettingController::class, 'update'])->name('ai-settings.update')->middleware('permission:manage settings');
        Route::post('ai/generate', [\App\Http\Controllers\Admin\AIGeneratorController::class, 'generate'])->name('ai.generate');
        Route::post('ai/save-prompts', [\App\Http\Controllers\Admin\AIGeneratorController::class, 'savePrompts'])->name('ai.save-prompts');

        // Programmatic SEO
        Route::resource('seo-landing-pages', \App\Http\Controllers\Admin\SeoLandingPageController::class)->middleware('permission:manage settings');

        // Activity Logs
        Route::get('activity-logs', [\App\Http\Controllers\Admin\AdminActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:manage settings');

        // Users
        Route::get('users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index')->middleware('permission:manage users');
        Route::patch('users/{user}/role', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateRole'])->name('users.role')->middleware('permission:manage users');
        Route::patch('users/{user}/toggle-ban', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleBan'])->name('users.toggle-ban')->middleware('permission:manage users');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('users.destroy')->middleware('permission:manage users');

        // Roles & Permissions
        Route::resource('roles', \App\Http\Controllers\Admin\AdminRoleController::class)->except('show')->middleware('permission:manage roles');

        // Duplicates Manager
        Route::get('duplicates', [\App\Http\Controllers\Admin\DuplicateManagerController::class, 'index'])->name('duplicates.index')->middleware('role:admin');
        Route::get('duplicates/search', [\App\Http\Controllers\Admin\DuplicateManagerController::class, 'search'])->name('duplicates.search')->middleware('role:admin');
        Route::post('duplicates/merge', [\App\Http\Controllers\Admin\DuplicateManagerController::class, 'merge'])->name('duplicates.merge')->middleware('role:admin');
        Route::post('duplicates/ignore', [\App\Http\Controllers\Admin\DuplicateManagerController::class, 'ignore'])->name('duplicates.ignore')->middleware('role:admin');


        // Redirect Logs
        Route::resource('redirect-logs', \App\Http\Controllers\Admin\RedirectLogController::class)->only(['index', 'store', 'destroy'])->middleware('role:admin');

        // Claim Requests
        Route::get('claim-requests', [\App\Http\Controllers\Admin\AdminClaimRequestController::class, 'index'])->name('claim-requests.index')->middleware('permission:manage claims');
        Route::patch('claim-requests/{claimRequest}/status', [\App\Http\Controllers\Admin\AdminClaimRequestController::class, 'updateStatus'])->name('claim-requests.status')->middleware('permission:manage claims');

        // Advertisements
        Route::resource('advertisements', \App\Http\Controllers\Admin\AdminAdvertisementController::class)->middleware('permission:manage settings');
        
        // Media Library
        Route::get('media', [\App\Http\Controllers\Admin\AdminMediaController::class, 'index'])->name('media.index')->middleware('role:admin');
        Route::post('media/rename', [\App\Http\Controllers\Admin\AdminMediaController::class, 'rename'])->name('media.rename')->middleware('role:admin');
        Route::post('media/bulk-delete', [\App\Http\Controllers\Admin\AdminMediaController::class, 'bulkDelete'])->name('media.bulk-delete')->middleware('role:admin');
        Route::post('media/optimize-batch', [\App\Http\Controllers\Admin\AdminMediaController::class, 'optimizeBatch'])->name('media.optimize.batch')->middleware('role:admin');

        // Contact Messages
        Route::get('contact-messages', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'index'])->name('contact-messages.index')->middleware('permission:manage users');
        Route::get('contact-messages/{contact_message}', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'show'])->name('contact-messages.show')->middleware('permission:manage users');
        Route::patch('contact-messages/{contact_message}/status', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'updateStatus'])->name('contact-messages.status')->middleware('permission:manage users');
        Route::delete('contact-messages/{contact_message}', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy')->middleware('permission:manage users');
        
        // Blog Posts
        Route::post('blog-posts/bulk-action', [\App\Http\Controllers\Admin\AdminBlogController::class, 'bulkAction'])->name('blog-posts.bulk-action');
        Route::resource('blog-posts', \App\Http\Controllers\Admin\AdminBlogController::class)->middleware('permission:manage blog');

        // Blog Categories
        Route::resource('blog-categories', \App\Http\Controllers\Admin\AdminBlogCategoryController::class)->middleware('permission:manage settings');

        // Bulk Import & Templates
        Route::prefix('import')->name('import.')->group(function () {
            Route::post('hospitals', [\App\Http\Controllers\Admin\BulkImportController::class, 'hospital'])->name('hospitals');
            Route::post('ambulances', [\App\Http\Controllers\Admin\BulkImportController::class, 'ambulance'])->name('ambulances');
            Route::post('blog-posts', [\App\Http\Controllers\Admin\BulkImportController::class, 'blogPost'])->name('blog-posts');
            Route::post('locations', [\App\Http\Controllers\Admin\BulkImportController::class, 'location'])->name('locations');
            Route::post('doctors', [\App\Http\Controllers\Admin\BulkImportController::class, 'doctor'])->name('doctors');
        });

        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('hospital', [\App\Http\Controllers\Admin\TemplateController::class, 'hospital'])->name('hospital');
            Route::get('ambulance', [\App\Http\Controllers\Admin\TemplateController::class, 'ambulance'])->name('ambulance');
            Route::get('blog-post', [\App\Http\Controllers\Admin\TemplateController::class, 'blogPost'])->name('blog-post');
            Route::get('location', [\App\Http\Controllers\Admin\TemplateController::class, 'location'])->name('location');
            Route::get('doctor', [\App\Http\Controllers\Admin\TemplateController::class, 'doctor'])->name('doctor');
        });

        // Pages
        Route::resource('pages', \App\Http\Controllers\Admin\AdminPageController::class)->middleware('permission:manage settings');
    });



// ─── Legacy Image SEO Redirect Route ────────────────────
// Catch 404s for .jpg/.png in storage and 301 redirect to .webp version
Route::get('storage/{path}', function ($path) {
    if (preg_match('/\.(jpg|jpeg|png)$/i', $path)) {
        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($webpPath)) {
            return redirect(\Illuminate\Support\Facades\Storage::disk('public')->url($webpPath), 301);
        }
    }
    abort(404);
})->where('path', '.*');

// ─── Dynamic Pages (Catch-All) ────────────────────────


Route::get('/test-ai-popup', function () {
    return \Blade::render('<!DOCTYPE html><html><head><script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100"> <h1>Test</h1> @include("admin.shared._ai_assistant") </body></html>');
});

Route::get('/test-ai-3', function () {
    return \Blade::render('<!DOCTYPE html><html><head><script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100"> <h1>Test 3</h1> @include("admin.shared._ai_assistant") </body></html>');
});
