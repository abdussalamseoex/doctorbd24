<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        // 1. Check for programmatic SEO landing page
        $seoPage = \App\Models\SeoLandingPage::where('slug', $slug)
            ->where('is_active', true)
            ->first();
            
        if ($seoPage) {
            return view('pages.seo-show', compact('seoPage'));
        }

        // 1.5. Check if it's a Blog Post for automatic SEO Redirection
        $blogPost = \App\Models\BlogPost::where('slug', $slug)->where('is_published', true)->first();
        if ($blogPost) {
            $from = request()->path();
            $to = route('blog.show', $slug, false);
            
            \App\Models\RedirectLog::record($from, $to);
            
            return redirect($to, 301);
        }

        // 2. Fallback to standard static Page
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('pages.show', compact('page'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000'
        ]);

        ContactMessage::create($validated);

        return redirect()->back()->with('success', 'Thank you for contacting us! We have received your message and will get back to you soon.');
    }
}
