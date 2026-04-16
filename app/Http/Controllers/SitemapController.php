<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\BlogPost;
use App\Models\Specialty;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = view('sitemap.index')->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function doctors(): Response
    {
        $doctors = Doctor::published()->select('id', 'name', 'slug', 'updated_at')->get();
        $content = view('sitemap.doctors', compact('doctors'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function hospitals(): Response
    {
        $hospitals = Hospital::published()->select('id', 'name', 'slug', 'updated_at')->get();
        $content   = view('sitemap.hospitals', compact('hospitals'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function ambulances(): Response
    {
        $ambulances = \App\Models\Ambulance::where('active', true)->select('id', 'provider_name', 'slug', 'updated_at')->get();
        $content   = view('sitemap.ambulances', compact('ambulances'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function blog(): Response
    {
        $posts   = BlogPost::published()->select('id', 'title', 'slug', 'published_at', 'updated_at')->get();
        $content = view('sitemap.blog', compact('posts'))->render();
        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
