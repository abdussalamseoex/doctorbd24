<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoLandingPage;
use App\Models\Specialty;
use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoLandingPageController extends Controller
{
    public function index()
    {
        $pages = SeoLandingPage::with(['specialty', 'division', 'district', 'area'])->latest()->paginate(20);
        return view('admin.seo-landing-pages.index', compact('pages'));
    }

    public function create()
    {
        $specialties = Specialty::all();
        $divisions = Division::all();
        $districts = District::all();
        $areas = Area::all();
        return view('admin.seo-landing-pages.create', compact('specialties', 'divisions', 'districts', 'areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'specialty_id' => 'nullable|exists:specialties,id',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'slug' => 'required|string|unique:seo_landing_pages,slug',
            'keyword' => 'required|string',
            'title' => 'required|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'content_top' => 'nullable|string',
            'content_bottom' => 'nullable|string',
            'faq_schema' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['slug'] = Str::slug($data['slug']);

        SeoLandingPage::create($data);

        return redirect()->route('admin.seo-landing-pages.index')->with('success', 'SEO Page created successfully.');
    }

    public function edit(SeoLandingPage $seoLandingPage)
    {
        $specialties = Specialty::all();
        $divisions = Division::all();
        $districts = District::all();
        $areas = Area::all();
        return view('admin.seo-landing-pages.edit', compact('seoLandingPage', 'specialties', 'divisions', 'districts', 'areas'));
    }

    public function update(Request $request, SeoLandingPage $seoLandingPage)
    {
         $data = $request->validate([
            'type' => 'required|string',
            'specialty_id' => 'nullable|exists:specialties,id',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'slug' => 'required|string|unique:seo_landing_pages,slug,' . $seoLandingPage->id,
            'keyword' => 'required|string',
            'title' => 'required|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'content_top' => 'nullable|string',
            'content_bottom' => 'nullable|string',
            'faq_schema' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['slug'] = Str::slug($data['slug']);

        $seoLandingPage->update($data);

        return redirect()->route('admin.seo-landing-pages.index')->with('success', 'SEO Page updated successfully.');
    }

    public function destroy(SeoLandingPage $seoLandingPage)
    {
        $seoLandingPage->delete();
        return redirect()->route('admin.seo-landing-pages.index')->with('success', 'SEO Page deleted successfully.');
    }
}
