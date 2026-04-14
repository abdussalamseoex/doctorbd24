<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Chamber;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDoctorController extends Controller
{
    use \App\Traits\HasBulkActions;
    protected $model = \App\Models\Doctor::class;
    public function index(Request $request)
    {
        $query = Doctor::with('specialties');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('verified')) {
            $query->where('verified', (bool)$request->verified);
        }
        if ($request->has('featured')) {
            $query->where('featured', true);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Location & Hospital Filters via Chambers
        if ($request->filled('area_id')) {
            $query->whereHas('chambers', function ($q) use ($request) {
                $q->where('area_id', $request->area_id);
            });
        } elseif ($request->filled('district_id')) {
            $query->whereHas('chambers.area', function ($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        } elseif ($request->filled('division_id')) {
            $query->whereHas('chambers.area.district', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('hospital_id')) {
            $query->whereHas('chambers', function ($q) use ($request) {
                $q->where('hospital_id', $request->hospital_id);
            });
        }

        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100, 200, 500])) $perPage = 20;

        $doctors = $query->latest()->paginate($perPage)->withQueryString();
        
        $counts = [
            'all' => Doctor::count(),
            'published' => Doctor::where('status', 'published')->orWhereNull('status')->count(),
            'draft' => Doctor::where('status', 'draft')->count(),
        ];

        // For the location filter hospital dropdown
        $hospitals = Hospital::select('id', 'name')->orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'counts', 'hospitals'));
    }

    public function publish(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Doctor::whereIn('id', $request->ids)->update(['status' => 'published']);
        return back()->with('success', count($request->ids) . ' doctors published successfully.');
    }

    public function importPopular(Request $request)
    {
        // Handle explicit wipe/reset from the UI
        $isReset = $request->query('action') === 'reset';
        $progress = \Illuminate\Support\Facades\Cache::get('popular_import_progress');

        // WIPE CORRUPT IMPORTS & RESET POINTER IF RUNNING FOR THE FIRST TIME OR IF FORCED RESET
        if ($isReset || !$progress || $progress['status'] === 'idle' || $progress['status'] === 'completed') {
            \Illuminate\Support\Facades\Cache::put('popular_import_progress', ['status' => 'idle', 'current' => 0, 'total' => 0, 'message' => '']);
            \Illuminate\Support\Facades\Cache::forget('popular_import_pointer');
            


            // 2. Delete ALL previously imported drafts to ensure a clean slate
            \App\Models\Doctor::where('import_source', 'popular_diagnostic')->forceDelete();
            \App\Models\ReportDuplicate::where('reason', 'like', '%Popular Diagnostic%')->delete();
            \App\Models\Chamber::where('name', 'Popular Diagnostic Center')->delete();

            // 3. Clean up orphaned long specialties and pivot relations
            \Illuminate\Support\Facades\DB::statement('DELETE FROM doctor_specialty WHERE doctor_id NOT IN (SELECT id FROM doctors)');
            \App\Models\Specialty::doesntHave('doctors')->delete();
            
            if ($isReset) {
                return response()->json(['success' => true]);
            }
        }

        // 2. RUN CHUNK
        \Illuminate\Support\Facades\Artisan::call('import:popular-doctors', ['--chunk' => 3]);
        
        $newProgress = \Illuminate\Support\Facades\Cache::get('popular_import_progress');
        return response()->json(['success' => true, 'status' => $newProgress['status'] ?? 'completed']);
    }

    public function importProgress()
    {
        return response()->json(\Illuminate\Support\Facades\Cache::get('popular_import_progress', [
            'status' => 'idle',
            'current' => 0,
            'total' => 0,
            'message' => ''
        ]));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('name->en')->get();
        $divisions   = \App\Models\Division::orderBy('id')->get();
        $hospitals   = Hospital::orderBy('name')->get();
        return view('admin.doctors.create', compact('specialties', 'divisions', 'hospitals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'slug'             => ['nullable','string','max:255','unique:doctors,slug'],
            'designation'      => ['nullable','string','max:255'],
            'qualifications'   => ['nullable','string'],
            'gender'           => ['required','in:male,female'],
            'phone'            => ['nullable','string','max:20'],
            'email'            => ['nullable','email','max:255'],
            'bio'              => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0'],
            'verified'         => ['boolean'],
            'featured'         => ['boolean'],
            'specialties'      => ['nullable','array'],
            'photo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'      => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'          => ['nullable','array','max:10'],
            'gallery.*'        => ['image','mimes:jpeg,png,webp','max:2048'],
            'chambers'         => ['nullable','array'],
            'facebook_url'     => ['nullable','url','max:255'],
            'twitter_url'      => ['nullable','url','max:255'],
            'instagram_url'    => ['nullable','url','max:255'],
            'linkedin_url'     => ['nullable','url','max:255'],
            'youtube_url'      => ['nullable','url','max:255'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');

        if ($request->hasFile('photo')) {
            $validated['photo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('photo'), 'doctors', 800);
        } else {
            unset($validated['photo']);
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('cover_image'), 'doctors/covers', 1200);
        } else {
            unset($validated['cover_image']);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'doctors/gallery', 1200);
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

        $videosInput = $this->parseJsonField($request->input('videos'));
        $validated['videos'] = $videosInput; // temp
        
        $validated['blogs'] = $this->processExternalLinksData($this->parseJsonField($request->input('blogs')));

        $doctor = Doctor::create(array_diff_key($validated, ['videos' => '']));

        if (!empty($videosInput)) {
            $this->syncDoctorVideos($doctor, $videosInput);
        }

        if (!empty($validated['specialties'])) {
            $doctor->specialties()->sync($validated['specialties']);
        }

        // Save chambers
        $this->saveChambers($doctor, $request->input('chambers', []));

        // Save SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $doctor->updateSeo($seoData);
        }

        // Check for possible duplicates
        $hasDuplicate = Doctor::where('name', $doctor->name)->where('id', '!=', $doctor->id)->exists();
        $message = 'Doctor created successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: A Doctor with the same name already exists in the system.';
        }

        return redirect()->route('admin.doctors.index')->with('success', $message);
    }

    public function edit(Doctor $doctor)
    {
        $specialties       = Specialty::orderBy('name->en')->get();
        $divisions         = \App\Models\Division::orderBy('id')->get();
        $hospitals         = Hospital::orderBy('name')->get();
        $doctorSpecialties = $doctor->specialties->pluck('id')->toArray();
        return view('admin.doctors.edit', compact('doctor', 'specialties', 'divisions', 'hospitals', 'doctorSpecialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'designation'      => ['nullable','string','max:255'],
            'qualifications'   => ['nullable','string'],
            'gender'           => ['required','in:male,female'],
            'phone'            => ['nullable','string','max:20'],
            'email'            => ['nullable','email','max:255'],
            'bio'              => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0'],
            'verified'         => ['boolean'],
            'featured'         => ['boolean'],
            'specialties'      => ['nullable','array'],
            'photo'            => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'cover_image'      => ['nullable','image','mimes:jpeg,png,webp','max:2048'],
            'gallery'          => ['nullable','array','max:10'],
            'gallery.*'        => ['image','mimes:jpeg,png,webp','max:2048'],
            'chambers'         => ['nullable','array'],
            'facebook_url'     => ['nullable','url','max:255'],
            'twitter_url'      => ['nullable','url','max:255'],
            'instagram_url'    => ['nullable','url','max:255'],
            'linkedin_url'     => ['nullable','url','max:255'],
            'youtube_url'      => ['nullable','url','max:255'],
        ]);

        $validated['verified'] = $request->boolean('verified');
        $validated['featured'] = $request->boolean('featured');
        if (empty($request->slug)) {
            if (empty($doctor->slug)) {
                $validated['slug'] = Str::slug($validated['name']);
            }
        } else {
            $validated['slug'] = $request->slug;
        }

        if ($request->hasFile('photo')) {
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $validated['photo'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('photo'), 'doctors', 800);
        } elseif ($request->boolean('remove_photo') && $doctor->photo) {
            Storage::disk('public')->delete($doctor->photo);
            $validated['photo'] = null;
        } else {
            unset($validated['photo']);
        }

        if ($request->hasFile('cover_image')) {
            if ($doctor->cover_image) Storage::disk('public')->delete($doctor->cover_image);
            $validated['cover_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('cover_image'), 'doctors/covers', 1200);
        } elseif ($request->boolean('remove_cover_image') && $doctor->cover_image) {
            Storage::disk('public')->delete($doctor->cover_image);
            $validated['cover_image'] = null;
        } else {
            unset($validated['cover_image']);
        }

        $existingGallery = $doctor->gallery ?? [];
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
                $existingGallery[] = \App\Services\ImageOptimizerService::storeAndOptimize($file, 'doctors/gallery', 1200);
            }
            $validated['gallery'] = $existingGallery;
        } elseif (!isset($validated['gallery'])) {
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

        $videosInput = $this->parseJsonField($request->input('videos'));
        $validated['videos'] = $videosInput; // temporary, will be unset later

        $validated['blogs'] = $this->processExternalLinksData($this->parseJsonField($request->input('blogs')));

        $doctor->update(array_diff_key($validated, ['videos' => '']));
        $this->syncDoctorVideos($doctor, $videosInput ?? []);
        $doctor->specialties()->sync($validated['specialties'] ?? []);

        // Save chambers
        $this->saveChambers($doctor, $request->input('chambers', []));

        // Save SEO
        if ($request->has('seo')) {
            $seoData = $request->input('seo');
            if ($request->hasFile('seo.og_image_file')) {
                $seoData['og_image'] = \App\Services\ImageOptimizerService::storeAndOptimize($request->file('seo.og_image_file'), 'seo/og', 1200);
            }
            $doctor->updateSeo($seoData);
        }

        // Check for possible duplicates
        $hasDuplicate = Doctor::where('name', $doctor->name)->where('id', '!=', $doctor->id)->exists();
        $message = 'Doctor updated successfully.';
        if ($hasDuplicate) {
            $message .= ' ⚠ Warning: Another Doctor with the same name exists in the system.';
        }

        return redirect()->route('admin.doctors.index')->with('success', $message);
    }

    /**
     * Sync inline chambers from the form.
     * Chambers with an 'id' are updated; new ones are created.
     * Chambers removed from form are deleted.
     */
    private function saveChambers(Doctor $doctor, array $chambersInput): void
    {
        $submittedIds = [];

        foreach ($chambersInput as $i => $chamberData) {
            if (empty($chamberData['name'])) continue;

            $data = [
                'doctor_id'      => $doctor->id,
                'name'           => $chamberData['name'],
                'hospital_id'    => $chamberData['hospital_id'] ?: null,
                'area_id'        => $chamberData['area_id'] ?: null,
                'address'        => $chamberData['address'] ?? null,
                'visiting_hours' => $chamberData['visiting_hours'] ?? null,
                'closed_days'    => $chamberData['closed_days'] ?? null,
                'phone'          => $chamberData['phone'] ?? null,
                'lat'            => $chamberData['lat'] ?: null,
                'lng'            => $chamberData['lng'] ?: null,
                'google_maps_url'=> $chamberData['google_maps_url'] ?? null,
                'sort_order'     => $i,
            ];

            if (!empty($chamberData['id'])) {
                $chamber = Chamber::where('id', $chamberData['id'])->where('doctor_id', $doctor->id)->first();
                if ($chamber) {
                    $chamber->update($data);
                    $submittedIds[] = $chamber->id;
                }
            } else {
                $chamber = Chamber::create($data);
                $submittedIds[] = $chamber->id;
            }
        }

        // Delete chambers not in form
        $doctor->chambers()->whereNotIn('id', $submittedIds)->delete();
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted.');
    }

    public function show(Doctor $doctor)
    {
        return redirect()->route('admin.doctors.edit', $doctor->id);
    }

    private function syncDoctorVideos(Doctor $doctor, $videosInput)
    {
        $existingVideos = $doctor->doctorVideos->keyBy('video_url');
        $keptUrls = [];

        foreach ($videosInput as $index => $videoData) {
            if (!isset($videoData['url'])) continue;
            
            $url = $videoData['url'];
            $title = $videoData['title'] ?? 'Video Link';
            $keptUrls[] = $url;
            
            $youtubeId = null;
            $thumbnailUrl = null;
            $isFacebook = str_contains(strtolower($url), 'facebook.com') || str_contains(strtolower($url), 'fb.watch');

            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
                $youtubeId = $match[1];
                $thumbnailUrl = 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
            } elseif ($isFacebook) {
                $thumbnailUrl = 'https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg';
                if (in_array($title, ['Fetching title...', 'Video Link']) || empty(trim($title))) {
                    $title = str_contains($url, '/reel/') ? 'Facebook Reel' : 'Facebook Video';
                }
            }

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
                    'description' => $videoData['description'] ?? $existing->description,
                    'sort_order' => $index,
                ]);
            } else {
                $doctor->doctorVideos()->create([
                    'provider' => $youtubeId ? 'youtube' : 'custom',
                    'video_url' => $url,
                    'youtube_id' => $youtubeId,
                    'title' => $title,
                    'description' => $videoData['description'] ?? null,
                    'slug' => \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(4),
                    'thumbnail_url' => $thumbnailUrl,
                    'sort_order' => $index,
                ]);
            }
        }

        $doctor->doctorVideos()->whereNotIn('video_url', $keptUrls)->delete();
    }

    private function processExternalLinksData(?array $items): ?array
    {
        if (empty($items)) return null;

        $processed = [];
        foreach ($items as $item) {
            $url = $item['url'] ?? '';
            if (empty($url)) continue;

            $title = $item['title'] ?? 'Linked Content';
            $image = $item['image'] ?? null;

            if (in_array($title, ['Fetching title...', 'Linked Content']) || empty(trim($title))) {
                try {
                    $linkHostLower = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
                    if (str_contains($linkHostLower, 'facebook.com') || str_contains($linkHostLower, 'fb.watch')) {
                        $fbTitle = 'Facebook Post';
                        if (str_contains($url, '/reel/')) $fbTitle = 'Facebook Reel';
                        elseif (str_contains($url, '/videos/') || str_contains($linkHostLower, 'fb.watch')) $fbTitle = 'Facebook Video';
                        
                        $processed[] = [
                            'title' => $fbTitle,
                            'url' => $url,
                            'image' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg'
                        ];
                        continue;
                    }

                    $oembedRes = \Illuminate\Support\Facades\Http::timeout(3)->get("https://noembed.com/embed?url=" . urlencode($url));
                    if ($oembedRes->successful() && $oembedRes->json('title')) {
                        $processed[] = ['title' => $oembedRes->json('title'), 'url' => $url, 'image' => $oembedRes->json('thumbnail_url')];
                        continue;
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(3)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'])
                        ->get($url);
                    
                    if ($response->successful()) {
                        $html = $response->body();
                        if (preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches) ||
                            preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                            $titleChunk = explode(' |', $matches[1])[0];
                            $title = trim(html_entity_decode(strip_tags(explode(' -', $titleChunk)[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        }
                        
                        if (!$image && preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches)) {
                            $image = $matches[1];
                        }
                        
                        if (in_array(trim($title), ['Facebook', 'Log In or Sign Up to View'])) {
                            $title = 'Linked Media';
                        }
                    }
                } catch (\Exception $e) {}
            }
            if(empty(trim($title))) { $title = 'Linked Article/Media'; }

            $processed[] = ['title' => $title, 'url' => $url, 'image' => $image];
        }
        return $processed;
    }

    private function parseJsonField(?string $value): ?array
    {
        if (!$value) return null;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }
}
