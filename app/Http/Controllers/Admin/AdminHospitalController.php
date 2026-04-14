<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminHospitalController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\Hospital::class;
    public function index(Request $request)
    {
        $query = Hospital::with('area');
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->has('verified')) {
            $query->where('verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('featured', true);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        } elseif ($request->filled('district_id')) {
            $query->whereHas('area', function($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        } elseif ($request->filled('division_id')) {
            $query->whereHas('area.district', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $counts = [
            'published' => Hospital::where('status', 'published')->count(),
            'draft'     => Hospital::where('status', 'draft')->count(),
        ];

        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500])) $perPage = 20;

        $hospitals = $query->latest()->paginate($perPage)->withQueryString();
        return view('admin.hospitals.index', compact('hospitals', 'counts'));
    }

    public function create()
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.hospitals.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required','string','max:255'],
            'type'            => ['required','in:hospital,diagnostic,clinic,other'],
            'phone'           => ['nullable','string','max:20'],
            'email'           => ['nullable','email'],
            'address'         => ['nullable','string'],
            'website'         => ['nullable','url'],
            'area_id'         => ['nullable','exists:areas,id'],
            'slug'            => ['nullable','string','max:255','unique:hospitals,slug'],
            'verified'        => ['boolean'],
            'featured'        => ['boolean'],
            'about'           => ['nullable','string'],
            'lat'             => ['nullable','numeric'],
            'lng'             => ['nullable','numeric'],
            'google_maps_url' => ['nullable','url'],
            'facebook_url'    => ['nullable','url'],
            'instagram_url'   => ['nullable','url'],
            'youtube_url'     => ['nullable','url'],
            'linkedin_url'    => ['nullable','url'],
            'twitter_url'     => ['nullable','url'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'banner'          => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }
        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');

        // Handle JSON arrays (sent as stringified JSON from Alpine.js)
        $validated['services']      = $this->parseJsonField($request->input('services'));
        $validated['opening_hours'] = $this->parseJsonField($request->input('opening_hours'));
        $videosInput = $this->parseJsonField($request->input('videos'));
        $validated['videos'] = $videosInput; // temporary, will be unset later
        $validated['blogs']         = $this->processExternalLinksData($this->parseJsonField($request->input('blogs')));

        if ($request->hasFile('logo')) {
            $validated['logo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('logo'), 'hospitals', 800);
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('banner'), 'hospitals/covers', 1200);
        } else {
            unset($validated['banner']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'hospitals/gallery', 1200);
            }
            $validated['gallery'] = $galleryPaths;
        } else {
            unset($validated['gallery']);
        }


        $validated['status'] = $request->input('status', 'draft');
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = $request->input('published_at');
        } else {
            $validated['published_at'] = null;
        } 

        $videosInput = $validated['videos'] ?? [];
        unset($validated['videos']);

        $hospital = Hospital::create($validated);
        $this->syncHospitalVideos($hospital, $videosInput);
        
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $hospital->updateSeo($seoData);
        }
        $hasDuplicate = Hospital::where('name', $hospital->name)->where('id', '!=', $hospital->id)->exists();
        $message = 'Hospital added successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Hospital with the same name exists in the system.';
        }

        return redirect()->route('admin.hospitals.index')->with('success', $message);
    }

    public function edit(Hospital $hospital)
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('admin.hospitals.edit', compact('hospital', 'divisions'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name'            => ['required','string','max:255'],
            'type'            => ['required','in:hospital,diagnostic,clinic,other'],
            'phone'           => ['nullable','string','max:20'],
            'email'           => ['nullable','email'],
            'address'         => ['nullable','string'],
            'website'         => ['nullable','url'],
            'area_id'         => ['nullable','exists:areas,id'],
            'slug'            => ['nullable','string','max:255','unique:hospitals,slug,'.$hospital->id],
            'verified'        => ['boolean'],
            'featured'        => ['boolean'],
            'about'           => ['nullable','string'],
            'lat'             => ['nullable','numeric'],
            'lng'             => ['nullable','numeric'],
            'google_maps_url' => ['nullable','url'],
            'facebook_url'    => ['nullable','url'],
            'instagram_url'   => ['nullable','url'],
            'youtube_url'     => ['nullable','url'],
            'linkedin_url'    => ['nullable','url'],
            'twitter_url'     => ['nullable','url'],
            'logo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'banner'          => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'         => ['nullable','array','max:10'],
            'gallery.*'       => ['image','mimes:jpeg,png,webp','max:2048'],
        ]);

        $validated['verified'] = $request->boolean('verified');
        $validated['opening_hours'] = $this->parseJsonField($request->input('opening_hours'));
        $validated['services'] = $this->parseJsonField($request->input('services'));
        
        $videosInput = $this->parseJsonField($request->input('videos'));
        $validated['videos'] = $videosInput; // temporary, will be unset later

        $validated['blogs'] = $this->processExternalLinksData($this->parseJsonField($request->input('blogs')));

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }

        if ($request->hasFile('logo')) {
            if ($hospital->logo) {
                Storage::disk('public')->delete($hospital->logo);
            }
            $validated['logo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('logo'), 'hospitals', 800);
        } elseif ($request->boolean('remove_logo') && $hospital->logo) {
            Storage::disk('public')->delete($hospital->logo);
            $validated['logo'] = null;
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('banner')) {
            if ($hospital->banner) Storage::disk('public')->delete($hospital->banner);
            $validated['banner'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('banner'), 'hospitals/covers', 1200);
        } elseif ($request->boolean('remove_banner') && $hospital->banner) {
            Storage::disk('public')->delete($hospital->banner);
            $validated['banner'] = null;
        } else {
            unset($validated['banner']);
        }

        $existingGallery = $hospital->gallery ?? [];
        if ($request->has('remove_gallery')) {
            $removeKeys = $request->input('remove_gallery');
            foreach ($removeKeys as $key) {
                if (isset($existingGallery[$key])) {
                    Storage::disk('public')->delete($existingGallery[$key]);
                    unset($existingGallery[$key]);
                }
            }
            $existingGallery = array_values($existingGallery);
            $validated['gallery'] = $existingGallery;
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $existingGallery[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'hospitals/gallery', 1200);
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
            unset($validated['gallery']);
        }


        $validated['status'] = $request->input('status', 'draft');


        $validated['status'] = $request->input('status', 'draft');

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = $request->input('published_at');
        } else {
            $validated['published_at'] = null;
        }

        $videosInput = $validated['videos'] ?? [];
        unset($validated['videos']);
        
        $hospital->update($validated);
        $this->syncHospitalVideos($hospital, $videosInput);
        
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $hospital->updateSeo($seoData);
        }
        $hasDuplicate = Hospital::where('name', $hospital->name)->where('id', '!=', $hospital->id)->exists();
        $message = 'Hospital updated successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Hospital with the same name exists in the system.';
        }

        return redirect()->route('admin.hospitals.index')->with('success', $message);
    }

    private function syncHospitalVideos(Hospital $hospital, $videosInput)
    {
        // Get existing videos
        $existingVideos = $hospital->hospitalVideos->keyBy('video_url');
        $keptUrls = [];

        foreach ($videosInput as $index => $videoData) {
            if (!isset($videoData['url'])) continue;
            
            $url = $videoData['url'];
            $title = $videoData['title'] ?? 'Video Link';
            $keptUrls[] = $url;
            
            $youtubeId = null;
            $thumbnailUrl = null;
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
                $youtubeId = $match[1];
                $thumbnailUrl = 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
            }

            // Backend fallback fetch if title wasn't fetched in frontend
            if (in_array($title, ['Fetching title...', 'Video Link']) || empty(trim($title))) {
                try {
                    $oembedRes = \Illuminate\Support\Facades\Http::timeout(3)->get("https://noembed.com/embed?url=" . urlencode($url));
                    if ($oembedRes->successful() && $oembedRes->json('title')) {
                        $title = $oembedRes->json('title');
                    }
                } catch (\Exception $e) {}
            }
            if(empty(trim($title)) || $title === 'Fetching title...') { $title = 'Video Link'; }

            if ($existingVideos->has($url)) {
                $existing = $existingVideos->get($url);
                $existing->update([
                    'title' => $title,
                    'sort_order' => $index,
                ]);
            } else {
                $hospital->hospitalVideos()->create([
                    'provider' => $youtubeId ? 'youtube' : 'custom',
                    'video_url' => $url,
                    'youtube_id' => $youtubeId,
                    'title' => $title,
                    'slug' => \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(4),
                    'thumbnail_url' => $thumbnailUrl,
                    'sort_order' => $index,
                ]);
            }
        }

        // Delete removed videos
        $hospital->hospitalVideos()->whereNotIn('video_url', $keptUrls)->delete();
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();
        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital deleted.');
    }

    public function show(Hospital $hospital)
    {
        return redirect()->route('admin.hospitals.edit', $hospital->id);
    }

    /**
     * Process array of links to ensure they have titles.
     */
    private function processExternalLinksData(?array $links): ?array
    {
        if (!$links) return null;
        
        $processed = [];
        foreach ($links as $link) {
            $url = is_string($link) ? $link : ($link['url'] ?? null);
            if (!$url) continue;

            $title = is_string($link) ? 'Linked Content' : ($link['title'] ?? 'Linked Content');
            $image = is_string($link) ? null : ($link['image'] ?? null);
            
            // Re-fetch only if title is generic
            if (in_array($title, ['Fetching title...', 'Linked Content', 'Linked Article/Media', 'Read Article']) || empty(trim($title))) {
                try {
                    // check if internal blog link first
                    $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'doctorbd24.com';
                    $linkHost = parse_url($url, PHP_URL_HOST);
                    if ($linkHost === $appHost || str_contains($url, '/blog/')) {
                        $parts = explode('/blog/', parse_url($url, PHP_URL_PATH));
                        if(count($parts) > 1) {
                            $slug = end($parts);
                            $post = \App\Models\BlogPost::where('slug', $slug)->first();
                            if($post) {
                                $processed[] = [
                                    'title' => $post->title,
                                    'url' => $url,
                                    'image' => $post->image ? asset('storage/' . $post->image) : null
                                ];
                                continue;
                            }
                        }
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(3)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                        ->get($url);
                    
                    if ($response->successful()) {
                        $html = $response->body();
                        if (preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches) ||
                            preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:title[\'"][^>]*>/i', $html, $matches) ||
                            preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                            $titleChunk = explode(' |', $matches[1])[0];
                            $title = trim(html_entity_decode(strip_tags(explode(' -', $titleChunk)[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        }
                        
                        if (!$image && preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches)) {
                            $image = $matches[1];
                        } elseif (!$image && preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:image[\'"][^>]*>/i', $html, $matches)) {
                            $image = $matches[1];
                        }
                    }
                } catch (\Exception $e) {}
            }
            if(empty(trim($title))) { $title = 'Linked Article/Media'; }

            $processed[] = ['title' => $title, 'url' => $url, 'image' => $image];
        }
        return $processed;
    }

    /**
     * Parse JSON string from Alpine.js hidden input.
     */
    private function parseJsonField(?string $value): ?array
    {
        if (!$value) return null;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function fetchVideoUrl(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json(['error' => 'Query is required'], 400);

        try {
            $url = "https://www.youtube.com/results?search_query=" . urlencode($query . " hospital bangladesh");
            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if ($response->successful()) {
                $html = $response->body();
                // Simple regex to extract the first video ID from ytInitialData
                if (preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $matches)) {
                    return response()->json(['url' => 'https://www.youtube.com/watch?v=' . $matches[1]]);
                }
            }
            return response()->json(['error' => 'No video found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchBlogUrl(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json(['error' => 'Query is required'], 400);

        try {
            // First search existing blog posts
            $post = \App\Models\BlogPost::where('title', 'like', '%' . $query . '%')
                ->where('status', 'published')
                ->first();

            if ($post) {
                return response()->json(['url' => route('blog.show', $post->slug)]);
            }

            // Fallback to dynamic search url
            return response()->json(['url' => route('blog.index', ['search' => $query])]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchUrlMeta(Request $request)
    {
        $url = $request->input('url');
        if (!$url) return response()->json(['error' => 'URL is required'], 400);

        try {
            $meta = ['title' => 'Linked Content', 'image' => null];

            // 1. FAST PATH: Check if it's an internal blog link!
            // E.g. https://doctorbd24.com/blog/popular-diagnostic
            $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'doctorbd24.com';
            $linkHost = parse_url($url, PHP_URL_HOST);
            if ($linkHost === $appHost || str_contains($url, '/blog/')) {
                $parts = explode('/blog/', parse_url($url, PHP_URL_PATH));
                if(count($parts) > 1) {
                    $slug = end($parts);
                    $post = \App\Models\BlogPost::where('slug', $slug)->first();
                    if($post) {
                        return response()->json([
                            'title' => $post->title,
                            'image' => $post->image ? asset('storage/' . $post->image) : null
                        ]);
                    }
                }
            }

            // check for oembed first as it's cleaner for media
            $oembedRes = \Illuminate\Support\Facades\Http::timeout(3)->get("https://noembed.com/embed?url=" . urlencode($url));
            if ($oembedRes->successful() && $oembedRes->json('title')) {
                $meta['title'] = $oembedRes->json('title');
                $meta['image'] = $oembedRes->json('thumbnail_url');
                return response()->json($meta);
            }

            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'])
                ->get($url);
            
            if ($response->successful()) {
                $html = $response->body();
                
                // Try OpenGraph title first
                if (preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches)) {
                    $meta['title'] = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                } elseif (preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:title[\'"][^>]*>/i', $html, $matches)) {
                    $meta['title'] = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                } elseif (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                    // Fallback to <title>
                    $title = explode(' |', $matches[1])[0]; // try to get core title
                    $title = explode(' -', $title)[0];
                    $meta['title'] = trim(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }

                if (preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches)) {
                    $meta['image'] = $matches[1];
                } elseif (preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:image[\'"][^>]*>/i', $html, $matches)) {
                    $meta['image'] = $matches[1];
                }
            }
            return response()->json($meta);
        } catch (\Exception $e) {
            return response()->json(['title' => 'Linked Content', 'image' => null, 'error' => $e->getMessage()], 500);
        }
    }
}
