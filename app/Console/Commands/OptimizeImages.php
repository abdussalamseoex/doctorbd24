<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Ambulance;
use App\Models\BlogPost;
use App\Services\ImageOptimizerService;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing images to WebP format to save space and improve performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $this->info('Starting image optimization...');
        
        $manager = new ImageManager(new Driver());

        // Hospitals
        $this->info('Optimizing Hospitals...');
        $hospitals = Hospital::all();
        foreach ($hospitals as $hospital) {
            $updates = [];
            
            if ($hospital->logo && !str_ends_with($hospital->logo, '.webp')) {
                $newPath = $this->optimizeFile($manager, $hospital->logo, 'hospitals', 800);
                if ($newPath) $updates['logo'] = $newPath;
            }
            if ($hospital->banner && !str_ends_with($hospital->banner, '.webp')) {
                $newPath = $this->optimizeFile($manager, $hospital->banner, 'hospitals/covers', 1200);
                if ($newPath) $updates['banner'] = $newPath;
            }
            if (!empty($hospital->gallery)) {
                $newGallery = [];
                $changed = false;
                foreach ($hospital->gallery as $img) {
                    if (!str_ends_with($img, '.webp')) {
                        $newPath = $this->optimizeFile($manager, $img, 'hospitals/gallery', 1200);
                        if ($newPath) {
                            $newGallery[] = $newPath;
                            $changed = true;
                            continue;
                        }
                    }
                    $newGallery[] = $img;
                }
                if ($changed) $updates['gallery'] = $newGallery;
            }
            
            if (!empty($updates)) {
                $hospital->update($updates);
                $this->line("Updated hospital: {$hospital->name}");
            }
        }

        // Doctors
        $this->info('Optimizing Doctors...');
        // We'll skip chunking for simplicity unless it's huge, but it's fine for CLI
        $doctors = Doctor::all();
        foreach ($doctors as $doctor) {
            $updates = [];
            
            if ($doctor->photo && !str_ends_with($doctor->photo, '.webp')) {
                $newPath = $this->optimizeFile($manager, $doctor->photo, 'doctors', 800);
                if ($newPath) $updates['photo'] = $newPath;
            }
            if ($doctor->cover_image && !str_ends_with($doctor->cover_image, '.webp')) {
                $newPath = $this->optimizeFile($manager, $doctor->cover_image, 'doctors/covers', 1200);
                if ($newPath) $updates['cover_image'] = $newPath;
            }
            if (!empty($doctor->gallery)) {
                $newGallery = [];
                $changed = false;
                foreach ($doctor->gallery as $img) {
                    if (!str_ends_with($img, '.webp')) {
                        $newPath = $this->optimizeFile($manager, $img, 'doctors/gallery', 1200);
                        if ($newPath) {
                            $newGallery[] = $newPath;
                            $changed = true;
                            continue;
                        }
                    }
                    $newGallery[] = $img;
                }
                if ($changed) $updates['gallery'] = $newGallery;
            }
            
            if (!empty($updates)) {
                $doctor->update($updates);
            }
        }

        // Ambulances
        $this->info('Optimizing Ambulances...');
        $ambulances = Ambulance::all();
        foreach ($ambulances as $ambulance) {
            $updates = [];
            
            if ($ambulance->logo && !str_ends_with($ambulance->logo, '.webp')) {
                $newPath = $this->optimizeFile($manager, $ambulance->logo, 'ambulances', 800);
                if ($newPath) $updates['logo'] = $newPath;
            }
            if ($ambulance->cover_image && !str_ends_with($ambulance->cover_image, '.webp')) {
                $newPath = $this->optimizeFile($manager, $ambulance->cover_image, 'ambulances/covers', 1200);
                if ($newPath) $updates['cover_image'] = $newPath;
            }
            if (!empty($ambulance->gallery)) {
                $newGallery = [];
                $changed = false;
                foreach ($ambulance->gallery as $img) {
                    if (!str_ends_with($img, '.webp')) {
                        $newPath = $this->optimizeFile($manager, $img, 'ambulances/gallery', 1200);
                        if ($newPath) {
                            $newGallery[] = $newPath;
                            $changed = true;
                            continue;
                        }
                    }
                    $newGallery[] = $img;
                }
                if ($changed) $updates['gallery'] = $newGallery;
            }
            
            if (!empty($updates)) {
                $ambulance->update($updates);
            }
        }

        // Blog Posts
        $this->info('Optimizing Blog Posts...');
        $blogs = BlogPost::all();
        foreach ($blogs as $blog) {
            $updates = [];
            
            if ($blog->image && !str_ends_with($blog->image, '.webp')) {
                $newPath = $this->optimizeFile($manager, $blog->image, 'blog', 1200);
                if ($newPath) $updates['image'] = $newPath;
            }
            
            if (!empty($updates)) {
                $blog->update($updates);
            }
        }

        $this->info('Optimization Complete!');
    }

    private function optimizeFile($manager, $path, $directory, $maxWidth)
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        try {
            $absolutePath = Storage::disk('public')->path($path);
            $image = $manager->decode($absolutePath);

            $filename = \Illuminate\Support\Str::uuid() . '.webp';
            $newRelativePath = $directory . '/' . $filename;
            $newAbsolutePath = Storage::disk('public')->path($newRelativePath);

            if ($maxWidth && $image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            Storage::disk('public')->makeDirectory($directory);
            $image->save($newAbsolutePath, quality: 80);

            // Delete old file
            Storage::disk('public')->delete($path);

            return $newRelativePath;
        } catch (\Exception $e) {
            $this->error("Failed to process {$path}: " . $e->getMessage());
            return null;
        }
    }
}
