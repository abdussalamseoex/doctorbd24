<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\BlogPost;
use App\Models\Specialty;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = view('sitemap.index')->render();
        return response($content, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function doctors(): Response
    {
        @ini_set('memory_limit', '512M');
        $doctors = Doctor::published()->select('id', 'name', 'slug', 'updated_at')->get();
        $content = view('sitemap.doctors', compact('doctors'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function hospitals(): Response
    {
        @ini_set('memory_limit', '512M');
        $hospitals = Hospital::published()->select('id', 'name', 'slug', 'updated_at')->get();
        $content   = view('sitemap.hospitals', compact('hospitals'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function ambulances(): Response
    {
        $ambulances = \App\Models\Ambulance::published()->select('id', 'provider_name', 'slug', 'updated_at')->get();
        $content   = view('sitemap.ambulances', compact('ambulances'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function blog(): Response
    {
        $posts   = BlogPost::published()->select('id', 'title', 'slug', 'published_at', 'updated_at')->get();
        $content = view('sitemap.blog', compact('posts'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function pagesEn(): StreamedResponse
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        return response()->stream(function () {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

            $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
            if (empty($baseUrl) || $baseUrl === 'http://localhost') {
                $baseUrl = 'https://doctorbd24.com';
            }

            $count = 0;

            // 1. Programmatic SEO Landing Pages
            $seoPages = DB::table('seo_landing_pages')
                ->where('status', 'published')
                ->select('slug', 'title', 'updated_at')
                ->cursor();

            foreach ($seoPages as $page) {
                $count++;
                $hasBn = false;
                if (!empty($page->title)) {
                    if (str_starts_with($page->title, '{')) {
                        $dec = json_decode($page->title, true);
                        $hasBn = !empty($dec['bn']);
                    } else {
                        $hasBn = true;
                    }
                }

                $slug = htmlspecialchars($page->slug, ENT_XML1, 'UTF-8');
                $enUrl = $baseUrl . '/' . $slug;
                $bnUrl = $baseUrl . '/bn/' . $slug;
                $lastmod = date('Y-m-d\TH:i:sP', strtotime($page->updated_at ?: 'now'));

                echo "  <url>\n";
                echo "    <loc>{$enUrl}</loc>\n";
                echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$enUrl}\" />\n";
                echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$enUrl}\" />\n";
                if ($hasBn) {
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"bn\" href=\"{$bnUrl}\" />\n";
                }
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo "    <priority>0.9</priority>\n";
                echo "  </url>\n";

                if ($count % 1000 === 0) {
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }

            // 2. Static Pages
            $staticPages = DB::table('pages')
                ->where('status', 'published')
                ->select('slug', 'title', 'updated_at')
                ->cursor();

            foreach ($staticPages as $page) {
                $count++;
                $hasBn = false;
                if (!empty($page->title)) {
                    if (str_starts_with($page->title, '{')) {
                        $dec = json_decode($page->title, true);
                        $hasBn = !empty($dec['bn']);
                    } else {
                        $hasBn = true;
                    }
                }

                $slug = htmlspecialchars($page->slug, ENT_XML1, 'UTF-8');
                $enUrl = $baseUrl . '/' . $slug;
                $bnUrl = $baseUrl . '/bn/' . $slug;
                $lastmod = date('Y-m-d\TH:i:sP', strtotime($page->updated_at ?: 'now'));

                echo "  <url>\n";
                echo "    <loc>{$enUrl}</loc>\n";
                echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$enUrl}\" />\n";
                echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$enUrl}\" />\n";
                if ($hasBn) {
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"bn\" href=\"{$bnUrl}\" />\n";
                }
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>monthly</changefreq>\n";
                echo "    <priority>0.8</priority>\n";
                echo "  </url>\n";

                if ($count % 1000 === 0) {
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }

            echo '</urlset>' . "\n";
        }, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function pagesBn(): StreamedResponse
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        return response()->stream(function () {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

            $baseUrl = rtrim(config('app.url') ?: url('/'), '/');
            if (empty($baseUrl) || $baseUrl === 'http://localhost') {
                $baseUrl = 'https://doctorbd24.com';
            }

            $count = 0;

            // 1. Programmatic SEO Landing Pages
            $seoPages = DB::table('seo_landing_pages')
                ->where('status', 'published')
                ->select('slug', 'title', 'updated_at')
                ->cursor();

            foreach ($seoPages as $page) {
                $hasBn = false;
                if (!empty($page->title)) {
                    if (str_starts_with($page->title, '{')) {
                        $dec = json_decode($page->title, true);
                        $hasBn = !empty($dec['bn']);
                    } else {
                        $hasBn = true;
                    }
                }

                if ($hasBn) {
                    $count++;
                    $slug = htmlspecialchars($page->slug, ENT_XML1, 'UTF-8');
                    $enUrl = $baseUrl . '/' . $slug;
                    $bnUrl = $baseUrl . '/bn/' . $slug;
                    $lastmod = date('Y-m-d\TH:i:sP', strtotime($page->updated_at ?: 'now'));

                    echo "  <url>\n";
                    echo "    <loc>{$bnUrl}</loc>\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$enUrl}\" />\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$enUrl}\" />\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"bn\" href=\"{$bnUrl}\" />\n";
                    echo "    <lastmod>{$lastmod}</lastmod>\n";
                    echo "    <changefreq>weekly</changefreq>\n";
                    echo "    <priority>0.9</priority>\n";
                    echo "  </url>\n";

                    if ($count % 1000 === 0) {
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
            }

            // 2. Static Pages
            $staticPages = DB::table('pages')
                ->where('status', 'published')
                ->select('slug', 'title', 'updated_at')
                ->cursor();

            foreach ($staticPages as $page) {
                $hasBn = false;
                if (!empty($page->title)) {
                    if (str_starts_with($page->title, '{')) {
                        $dec = json_decode($page->title, true);
                        $hasBn = !empty($dec['bn']);
                    } else {
                        $hasBn = true;
                    }
                }

                if ($hasBn) {
                    $count++;
                    $slug = htmlspecialchars($page->slug, ENT_XML1, 'UTF-8');
                    $enUrl = $baseUrl . '/' . $slug;
                    $bnUrl = $baseUrl . '/bn/' . $slug;
                    $lastmod = date('Y-m-d\TH:i:sP', strtotime($page->updated_at ?: 'now'));

                    echo "  <url>\n";
                    echo "    <loc>{$bnUrl}</loc>\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$enUrl}\" />\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$enUrl}\" />\n";
                    echo "    <xhtml:link rel=\"alternate\" hreflang=\"bn\" href=\"{$bnUrl}\" />\n";
                    echo "    <lastmod>{$lastmod}</lastmod>\n";
                    echo "    <changefreq>monthly</changefreq>\n";
                    echo "    <priority>0.8</priority>\n";
                    echo "  </url>\n";

                    if ($count % 1000 === 0) {
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
            }

            echo '</urlset>' . "\n";
        }, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
