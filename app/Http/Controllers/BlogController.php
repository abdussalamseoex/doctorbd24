<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLd;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        SEOTools::setTitle('স্বাস্থ্য ব্লগ — টিপস ও পরামর্শ | DoctorBD24');
        SEOTools::setDescription('বাংলাদেশের শীর্ষ ডাক্তারদের লেখা স্বাস্থ্য বিষয়ক আর্টিকেল, টিপস ও পরামর্শ পড়ুন।');
        OpenGraph::setType('website');

        $query = BlogPost::published()->with('category', 'author');

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

    public function show($id, $slug = null)
    {
        $post = BlogPost::published()->with('category', 'author')->findOrFail($id);
        
        // SEO Redirect: enforce correct slug in URL
        if ($slug !== $post->slug) {
            return redirect()->route('blog.show', ['id' => $post->id, 'slug' => $post->slug], 301);
        }

        $post->incrementViewCount();

        // ── SEO ──────────────────────────────────────
        $desc = $post->excerpt ?: mb_substr(strip_tags($post->body), 0, 160) . '…';

        SEOTools::setTitle($post->title . ' | DoctorBD24');
        SEOTools::setDescription($desc);
        SEOTools::setCanonical(route('blog.show', ['id' => $post->id, 'slug' => $post->slug]));

        OpenGraph::setType('article');
        OpenGraph::setUrl(route('blog.show', ['id' => $post->id, 'slug' => $post->slug]));
        OpenGraph::addProperty('article:author', $post->author->name);
        OpenGraph::addProperty('article:published_time', $post->published_at?->toIso8601String());
        if ($post->image) OpenGraph::addImage(asset('storage/' . $post->image));

        JsonLd::setType('Article');
        JsonLd::setTitle($post->title);
        JsonLd::setDescription($desc);
        JsonLd::addValue('url', route('blog.show', ['id' => $post->id, 'slug' => $post->slug]));
        JsonLd::addValue('datePublished', $post->published_at?->toIso8601String());
        JsonLd::addValue('author', ['@type' => 'Person', 'name' => $post->author->name]);
        JsonLd::addValue('publisher', [
            '@type' => 'Organization',
            'name'  => 'DoctorBD24',
            'url'   => url('/'),
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
