<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $keys = [
            'site_name', 'site_logo', 'favicon', 'contact_email', 'contact_phone', 
            'contact_address', 'facebook_url', 'twitter_url', 'instagram_url', 
            'youtube_url', 'footer_text', 'review_auto_approve',
            'homepage_hero_title', 'homepage_hero_subtitle', 'homepage_seo_title',
            'homepage_seo_description', 'homepage_seo_keywords', 'robots_txt',
            'google_analytics', 'google_search_console'
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method', 'site_logo', 'favicon']);

        // Handle File Uploads
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            Setting::set('favicon', $path);
        }

        // Handle Other Settings
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Handle specific toggles if not in $request
        Setting::set('review_auto_approve', $request->has('review_auto_approve') ? '1' : '0');

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved successfully.');
    }
}
