<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\BlogPost::class;
    public function index(Request $request)
    {
        $baseQuery = BlogPost::query();
        if (!auth()->user()->hasRole('admin')) {
            $baseQuery->where('user_id', auth()->id());
        }

        $counts = [
            'all'       => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->published()->count(),
            'scheduled' => (clone $baseQuery)->scheduled()->count(),
            'draft'     => (clone $baseQuery)->drafted()->count(),
        ];

        $query = (clone $baseQuery)->with('category', 'author');

        $status = $request->query('status', 'all');
        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'scheduled') {
            $query->scheduled();
        } elseif ($status === 'draft') {
            $query->drafted();
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 20;
        }

        $posts = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('admin.blog.index', compact('posts', 'counts', 'status'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required','string','max:255'],
            'slug'             => ['nullable','string','max:255','unique:blog_posts,slug'],
            'body'             => ['required','string'],
            'excerpt'          => ['nullable','string','max:500'],
            'blog_category_id' => ['nullable','exists:blog_categories,id'],
            'status'           => ['required','in:draft,scheduled,published'],
            'published_at'     => ['nullable','date'],
            'image'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        $validated['user_id'] = auth()->id();
        
        if ($request->hasFile('image')) {
            $validated['image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('image'), 'blog', 1200);
        }

        $status = $validated['status'];
        if ($status === 'published') {
            $validated['published_at'] = !empty($validated['published_at'])
                ? \Carbon\Carbon::parse($validated['published_at'])
                : now();
        } elseif ($status === 'scheduled') {
            if (empty($validated['published_at'])) {
                return redirect()->back()->withInput()->withErrors(['published_at' => 'Please select a date and time for scheduling.']);
            }
            $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        $post = BlogPost::create($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $post->updateSeo($seoData);
        }
        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(BlogPost $blogPost)
    {
        if (!auth()->user()->hasRole('admin') && $blogPost->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = BlogCategory::all();
        return view('admin.blog.edit', compact('blogPost', 'categories'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        if (!auth()->user()->hasRole('admin') && $blogPost->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title'            => ['required','string','max:255'],
            'slug'             => ['nullable','string','max:255','unique:blog_posts,slug,'.$blogPost->id],
            'body'             => ['required','string'],
            'excerpt'          => ['nullable','string','max:500'],
            'blog_category_id' => ['nullable','exists:blog_categories,id'],
            'status'           => ['required','in:draft,scheduled,published'],
            'published_at'     => ['nullable','date'],
            'image'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if (empty($request->slug)) {
            if (empty($blogPost->slug)) {
                $validated['slug'] = Str::slug($validated['title']);
            }
        } else {
            $validated['slug'] = $request->slug;
        }

        if ($request->hasFile('image')) {
            if ($blogPost->image) Storage::disk('public')->delete($blogPost->image);
            $validated['image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('image'), 'blog', 1200);
        } elseif ($request->boolean('remove_image') && $blogPost->image) {
            Storage::disk('public')->delete($blogPost->image);
            $validated['image'] = null;
        }

        $status = $validated['status'];
        if ($status === 'published') {
            if (!empty($validated['published_at'])) {
                $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
            } elseif ($blogPost->published_at) {
                $validated['published_at'] = $blogPost->published_at;
            } else {
                $validated['published_at'] = now();
            }
        } elseif ($status === 'scheduled') {
            if (empty($validated['published_at'])) {
                return redirect()->back()->withInput()->withErrors(['published_at' => 'Please select a date and time for scheduling.']);
            }
            $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        $blogPost->update($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                if ($blogPost->seoMeta && $blogPost->seoMeta->og_image && !Str::startsWith($blogPost->seoMeta->og_image, 'http')) {
                    Storage::disk('public')->delete($blogPost->seoMeta->og_image);
                }
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $blogPost->updateSeo($seoData);
        }
        return redirect()->route('admin.blog-posts.index')->with('success', 'Post updated successfully.');
    }



    public function destroy(BlogPost $blogPost)
    {
        if (!auth()->user()->hasRole('admin') && $blogPost->user_id !== auth()->id()) {
            abort(403);
        }
        
        $blogPost->delete();
        return redirect()->route('admin.blog-posts.index')->with('success', 'Post deleted.');
    }

    public function show(BlogPost $blogPost)
    {
        return redirect()->route('admin.blog-posts.edit', $blogPost->id);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120',
        ]);

        $path = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('file'), 'blog/content', 1200);

        return response()->json([
            'location' => asset('storage/' . $path)
        ]);
    }
}
