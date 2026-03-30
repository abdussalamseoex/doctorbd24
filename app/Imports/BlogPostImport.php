<?php

namespace App\Imports;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BlogPostImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    public function model(array $row)
    {
        // Clean keys in case of UTF-8 BOM or extra whitespace
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = trim(str_replace("\xEF\xBB\xBF", '', $key));
            $cleanRow[$cleanKey] = $value;
        }
        $row = array_merge($row, $cleanRow); // Merge cleaned keys

        Log::info('Importing Row: ' . json_encode($row));
        
        $title = $row['title'] ?? $row['post_title'] ?? $row['name'] ?? null;
        if (!$title) {
            Log::warning('Skip row: Missing title. Available keys: ' . implode(', ', array_keys($row)));
            return null;
        }

        $categoryName = $row['category'] ?? $row['blog_category'] ?? null;
        $category = null;
        if ($categoryName) {
            $category = BlogCategory::where('name', 'like', '%' . $categoryName . '%')->first();
            if (!$category) {
                $category = BlogCategory::create([
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName)
                ]);
            }
        }

        $rawSlug = !empty($row['slug']) ? $row['slug'] : Str::slug($title);
        $finalSlug = urldecode($rawSlug);

        return new BlogPost([
            'user_id'          => auth()->id() ?? 1,
            'blog_category_id' => $category ? $category->id : null,
            'title'            => $title,
            'slug'             => $finalSlug,
            'excerpt'          => $row['excerpt'] ?? null,
            'body'             => $row['body'] ?? $row['content'] ?? $row['post_content'] ?? '',
            'published_at'     => now(),
        ]);
    }

    public function onError(Throwable $e)
    {
        Log::error('Import Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $f) {
            Log::error('Import Failure: Row ' . $f->row() . ' - ' . json_encode($f->errors()));
        }
    }
}
