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
        $seoPage = \App\Models\SeoLandingPage::published()
            ->where('slug', $slug)
            ->first();
            
        if ($seoPage) {
            \SEO::setTitle($seoPage->meta_title ?: $seoPage->title);
            if ($seoPage->meta_description) {
                \SEO::setDescription($seoPage->meta_description);
            }
            
            \SEO::opengraph()->setTitle($seoPage->meta_title ?: $seoPage->title);
            if ($seoPage->meta_description) {
                \SEO::opengraph()->setDescription($seoPage->meta_description);
            }
            \SEO::opengraph()->setUrl(url()->current());
            \SEO::opengraph()->setType('website');
            
            \Artesaos\SEOTools\Facades\JsonLdMulti::setType('CollectionPage');
            \Artesaos\SEOTools\Facades\JsonLdMulti::setTitle($seoPage->meta_title ?: $seoPage->title);
            if ($seoPage->meta_description) {
                \Artesaos\SEOTools\Facades\JsonLdMulti::setDescription($seoPage->meta_description);
            }
            \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('url', url()->current());

            return view('pages.seo-show', compact('seoPage'));
        }

        // 1.5. Check if it's a Blog Post for automatic SEO Redirection
        $blogPost = \App\Models\BlogPost::where('slug', $slug)->published()->first();
        if ($blogPost) {
            $from = request()->path();
            $to = route('blog.show', $slug, false);
            
            \App\Models\RedirectLog::record($from, $to);
            
            return redirect($to, 301);
        }

        // 2. Fallback to standard static Page
        $page = Page::published()->where('slug', $slug)->first();
        if (!$page) {
            $redirect = \App\Models\RedirectLog::where('from_url', $slug)->first();
            if ($redirect) {
                if ($redirect->to_url === '410') {
                    abort(410, 'Gone');
                }
                return redirect($redirect->to_url, 301);
            }
            abort(404);
        }

        $pageTitle = $page->getTranslation('title', app()->getLocale(), false) ?: $page->title;
        \Artesaos\SEOTools\Facades\SEOTools::setTitle($pageTitle . ' | DoctorBD24');
        $desc = \Illuminate\Support\Str::limit(strip_tags($page->getTranslation('content', app()->getLocale(), false) ?: $page->content), 160);
        \Artesaos\SEOTools\Facades\SEOTools::setDescription($desc);
        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(url()->current());

        \Artesaos\SEOTools\Facades\JsonLdMulti::setType($slug == 'about-us' ? 'AboutPage' : 'WebPage');
        \Artesaos\SEOTools\Facades\JsonLdMulti::setTitle($pageTitle);
        \Artesaos\SEOTools\Facades\JsonLdMulti::setDescription($desc);
        \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('url', url()->current());

        return view('pages.show', compact('page'));
    }

    public function contact()
    {
        \Artesaos\SEOTools\Facades\SEOTools::setTitle('Contact Us | DoctorBD24');
        \Artesaos\SEOTools\Facades\SEOTools::setDescription('Contact DoctorBD24 for any inquiries, healthcare support, or assistance with finding doctors and hospitals in Bangladesh.');
        \Artesaos\SEOTools\Facades\SEOTools::setCanonical(url()->current());
        
        \Artesaos\SEOTools\Facades\JsonLdMulti::setType('ContactPage');
        \Artesaos\SEOTools\Facades\JsonLdMulti::setTitle('Contact Us | DoctorBD24');
        \Artesaos\SEOTools\Facades\JsonLdMulti::setDescription('Contact DoctorBD24 for any inquiries, healthcare support, or assistance with finding doctors and hospitals in Bangladesh.');
        \Artesaos\SEOTools\Facades\JsonLdMulti::addValue('url', url()->current());

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
