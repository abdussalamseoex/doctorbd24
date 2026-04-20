<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLdMulti;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $categorySlug = $request->query('category');
        $search = $request->query('search');

        $title = 'স্বাস্থ্য ব্লগ — টিপস ও পরামর্শ';
        $desc = 'বাংলাদেশের শীর্ষ ডাক্তারদের লেখা স্বাস্থ্য বিষয়ক আর্টিকেল, টিপস ও পরামর্শ পড়ুন।';

        if ($categorySlug) {
            $cat = BlogCategory::where('slug', $categorySlug)->first();
            if ($cat) {
                $catName = $cat->getTranslation('name', app()->getLocale()) ?: $cat->name;
                $title = "{$catName} ডক্টর ব্লগ";
                $desc = "{$catName} সম্পর্কে আমাদের ডাক্তারদের দেওয়া পরামর্শ এবং আর্টিকেলগুলো পড়ুন।";
            }
        }

        if ($search) {
            $title = "{$search} এর জন্য ফলাফল";
            $desc = "{$search} নিয়ে আমাদের ব্লগের সকল তথ্য।";
        }

        if ($page > 1) {
            $title .= " (পৃষ্ঠা {$page})";
            $desc .= " - পৃষ্ঠা {$page}";
        }

        SEOTools::setTitle($title . ' | DoctorBD24');
        SEOTools::setDescription($desc);
        OpenGraph::setType('website');

        $queryParams = array_filter($request->only(['category', 'search']));
        if ($page > 1) {
            $queryParams['page'] = $page;
        }

        if (!empty($queryParams)) {
            SEOTools::setCanonical(url()->current() . '?' . http_build_query($queryParams));
        } else {
            SEOTools::setCanonical(url()->current());
        }

        $query = BlogPost::published()->with('category', 'author');

        $breadcrumb = \Artesaos\SEOTools\Facades\JsonLdMulti::newJsonLd();
        $breadcrumb->setType('BreadcrumbList');
        
        $itemList = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/')
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => route('blog.index')
            ]
        ];

        if ($categorySlug && isset($catName)) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $catName,
                'item' => url()->current() . '?category=' . $categorySlug
            ];
        }

        $breadcrumb->addValue('itemListElement', $itemList);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts      = $query->orderByDesc('published_at')->paginate(9)->withQueryString();
        $categories = BlogCategory::withCount('posts')->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->with('category', 'author')->firstOrFail();
        $post->incrementViewCount();

        $hasBn = !empty($post->getTranslation('title', 'bn', false));
        \Illuminate\Support\Facades\View::share('has_bn_translation', $hasBn);
        
        if (app()->getLocale() === 'bn' && !$hasBn) {
            \Illuminate\Support\Facades\View::share('noindex_page', true);
        }

        // ── SEO ──────────────────────────────────────
        $desc = $post->excerpt ?: mb_substr(strip_tags($post->body), 0, 160) . '…';

        SEOTools::setTitle($post->title . ' | DoctorBD24');
        SEOTools::setDescription($desc);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setType('article');
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('article:author', $post->author->name);
        OpenGraph::addProperty('article:published_time', $post->published_at?->toIso8601String());
        if ($post->image) OpenGraph::addImage(asset('storage/' . $post->image));

        JsonLdMulti::setType('BlogPosting');
        JsonLdMulti::setTitle($post->title);
        JsonLdMulti::setDescription($desc);
        JsonLdMulti::addValue('headline', $post->title);
        JsonLdMulti::addValue('url', url()->current());
        JsonLdMulti::addValue('datePublished', $post->published_at?->toIso8601String());
        JsonLdMulti::addValue('dateModified', $post->updated_at?->toIso8601String() ?? $post->published_at?->toIso8601String());
        
        if ($post->image) {
            JsonLdMulti::addValue('image', asset('storage/' . $post->image));
        }

        $isBn = request()->routeIs('bn.*');
        $baseUrl = $isBn ? url('/bn') : url('/');
        $blogIndexUrl = $baseUrl . '/blog';

        JsonLdMulti::addValue('author', [
            '@type' => 'Person',
            'name'  => $post->author->name,
            'url'   => $baseUrl
        ]);
        
        JsonLdMulti::addValue('publisher', [
            '@type' => 'Organization',
            'name'  => 'DoctorBD24',
            'url'   => $baseUrl,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => asset('assets/images/logo.png')
            ]
        ]);

        $breadcrumb = \Artesaos\SEOTools\Facades\JsonLdMulti::newJsonLd();
        $breadcrumb->setType('BreadcrumbList');
        $breadcrumb->addValue('itemListElement', [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => $baseUrl
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => $blogIndexUrl
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $post->category?->name ?? 'Article',
                'item' => $post->category ? url()->current() . '?category=' . $post->category->slug : $blogIndexUrl
            ],
            [
                '@type' => 'ListItem',
                'position' => 4,
                'name' => $post->title,
                'item' => url()->current()
            ]
        ]);
        // ─────────────────────────────────────────────

        $related = BlogPost::published()
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->take(3)->get();
            
        $categories = BlogCategory::withCount('posts')->get();

        return view('blog.show', compact('post', 'related', 'categories'));
    }
}
