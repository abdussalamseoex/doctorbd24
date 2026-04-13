<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Ambulance;
use App\Models\BlogPost;
use App\Models\Advertisement;
use App\Models\SeoMeta;
use App\Models\Setting;

class AdminMediaController extends Controller
{
    /**
     * Display a listing of the media files.
     */
    public function index(Request $request)
    {
        $directory = $request->input('folder', '');

        // Fetch all files
        $allFiles = Storage::disk('public')->allFiles($directory);

        // Filter out non-images or system files if desired
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        
        $files = collect($allFiles)->filter(function ($file) use ($allowedExtensions) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($ext, $allowedExtensions);
        })->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url'  => Storage::disk('public')->url($file),
                'size' => Storage::disk('public')->size($file),
                'last_modified' => Storage::disk('public')->lastModified($file),
                'directory' => dirname($file) === '.' ? '' : dirname($file),
            ];
        })->sortByDesc('last_modified')->values();

        // Pagination
        $perPage = $request->input('per_page', 54); // 54 nice grid number
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $items = $files->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedFiles = new \Illuminate\Pagination\LengthAwarePaginator($items, $files->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

        // Get unique directories for a simplistic filter
        $directories = collect($allFiles)
                        ->map(fn($f) => dirname($f) === '.' ? 'Root' : dirname($f))
                        ->unique()
                        ->sort()
                        ->values();

        return view('admin.media.index', compact('paginatedFiles', 'directories', 'directory'));
    }

    /**
     * Rename a file and update all database references
     */
    public function rename(Request $request)
    {
        $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string|regex:/^[a-zA-Z0-9_\-\.]+$/'
        ]);

        $oldPath = $request->input('old_path');
        $newName = $request->input('new_name');

        if (!Storage::disk('public')->exists($oldPath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        $directory = dirname($oldPath);
        if ($directory === '.') {
            $directory = '';
        }
        
        $newPath = ($directory !== '' ? $directory . '/' : '') . $newName;

        if (Storage::disk('public')->exists($newPath)) {
            return response()->json(['success' => false, 'message' => 'A file with this name already exists in this folder.'], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Move file
            Storage::disk('public')->move($oldPath, $newPath);

            // 2. Perform Smart DB Replace
            $this->smartDbReplace($oldPath, $newPath);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'File renamed and links updated successfully!',
                'new_path' => $newPath,
                'new_url' => Storage::disk('public')->url($newPath)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Try to revert file if it moved but DB failed
            if (Storage::disk('public')->exists($newPath) && !Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($newPath, $oldPath);
            }
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk Delete Files
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
            'paths.*' => 'string'
        ]);

        $deleted = 0;
        foreach ($request->input('paths') as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted++;
            }
        }

        return response()->json(['success' => true, 'message' => "$deleted files deleted."]);
    }

    /**
     * Intelligently replace references of old path to new path in DB.
     */
    private function smartDbReplace(string $oldPath, string $newPath)
    {
        // 1. Direct String Columns
        Doctor::where('photo', $oldPath)->update(['photo' => $newPath]);
        Doctor::where('cover_image', $oldPath)->update(['cover_image' => $newPath]);
        
        Hospital::where('logo', $oldPath)->update(['logo' => $newPath]);
        Hospital::where('banner', $oldPath)->update(['banner' => $newPath]);
        
        Ambulance::where('logo', $oldPath)->update(['logo' => $newPath]);
        Ambulance::where('cover_image', $oldPath)->update(['cover_image' => $newPath]);

        BlogPost::where('image', $oldPath)->update(['image' => $newPath]);
        Advertisement::where('image_path', $oldPath)->update(['image_path' => $newPath]);
        
        SeoMeta::where('og_image', $oldPath)->update(['og_image' => $newPath]);
        Setting::whereIn('key', ['site_logo', 'favicon'])->where('value', $oldPath)->update(['value' => $newPath]);

        // 2. JSON Columns (gallery)
        $this->updateJsonArrayGallery(Doctor::class, $oldPath, $newPath);
        $this->updateJsonArrayGallery(Hospital::class, $oldPath, $newPath);
        $this->updateJsonArrayGallery(Ambulance::class, $oldPath, $newPath);
    }

    /**
     * Update JSON gallery
     */
    private function updateJsonArrayGallery($modelClass, $oldPath, $newPath) {
        $records = $modelClass::where('gallery', 'LIKE', '%"'.$oldPath.'"%')->get();
        foreach ($records as $record) {
            $gallery = $record->gallery;
            if (is_array($gallery)) {
                foreach ($gallery as $index => $item) {
                    if ($item === $oldPath) {
                        $gallery[$index] = $newPath;
                    }
                }
                $record->gallery = $gallery;
                $record->save();
            }
        }
    }
}
