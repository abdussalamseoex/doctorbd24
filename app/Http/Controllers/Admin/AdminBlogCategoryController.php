<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->latest()->get();
        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (BlogCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        BlogCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(BlogCategory $blog_category)
    {
        return view('admin.blog-categories.edit', compact('blog_category'));
    }

    public function update(Request $request, BlogCategory $blog_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        if ($blog_category->name !== $request->name) {
            $originalSlug = $slug;
            $counter = 1;
            while (BlogCategory::where('slug', $slug)->where('id', '!=', $blog_category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        } else {
            $slug = $blog_category->slug;
        }

        $blog_category->update([
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(BlogCategory $blog_category)
    {
        // Don't delete if there are posts attached
        if ($blog_category->posts()->count() > 0) {
            return redirect()->route('admin.blog-categories.index')->with('error', 'Cannot delete a category that has related posts.');
        }

        $blog_category->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'Category deleted successfully.');
    }
}
