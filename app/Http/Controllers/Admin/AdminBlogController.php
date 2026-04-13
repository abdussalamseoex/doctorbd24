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
        $query = BlogPost::with('category', 'author')->latest();
        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }
        $posts = $query->paginate(20);
        return view('admin.blog.index', compact('posts'));
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
            'published'        => ['boolean'],
            'published_at'     => ['nullable','date'],
            'image'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        $validated['user_id']     = auth()->id();
        $validated['published']   = $request->boolean('published');
        $validated['published_at'] = $validated['published'] ? ($validated['published_at'] ?? now()) : null;
        
        if ($request->hasFile('image')) {
            $validated['image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('image'), 'blog', 1200);
        }


        $validated['status'] = $request->input('status', 'draft');


        if ($validated['status'] === 'published') {


            $validated['published_at'] = now();


        } elseif ($validated['status'] === 'scheduled') {


            $validated['published_at'] = $request->input('published_at');


        } else {


            $validated['published_at'] = null;


        }

        $post =
 $validated['status'] = $request->input('status', 'draft');
 if ($validated['status'] === 'published') {
     $validated['published_at'] = now();
 } elseif ($validated['status'] === 'scheduled') {
     $validated['published_at'] = $request->input('published_at');
 } else {
     $validated['published_at'] = null;
 } BlogPost::create($validated);

        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $post->updateSeo($seoData);
        }
        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post created.');
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
            'published'        => ['boolean'],
            'published_at'     => ['nullable','date'],
            'image'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
        ]);
        $validated['published']    = $request->boolean('published');
        $validated['published_at'] = $validated['published'] ? ($validated['published_at'] ?? $blogPost->published_at ?? now()) : null;

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


        $validated['status'] = $request->input('status', 'draft');


        if ($validated['status'] === 'published') {


            $validated['published_at'] = now();


        } elseif ($validated['status'] === 'scheduled') {


            $validated['published_at'] = $request->input('published_at');


        } else {


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
        return redirect()->route('admin.blog-posts.index')->with('success', 'Post updated.');
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
}
