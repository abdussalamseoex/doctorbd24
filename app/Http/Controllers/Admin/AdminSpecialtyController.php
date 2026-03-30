<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->orderBy('name->en')->paginate(30);
        return view('admin.specialties.index', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_bn' => ['required','string','max:255'],
            'icon'    => ['nullable','string','max:10'],
        ]);

        Specialty::create([
            'name' => ['en' => $request->name_en, 'bn' => $request->name_bn],
            'slug' => Str::slug($request->name_en),
            'icon' => $request->icon ?? '🩺',
        ]);

        return redirect()->route('admin.specialties.index')->with('success', 'Specialty added.');
    }

    public function update(Request $request, Specialty $specialty)
    {
        $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_bn' => ['required','string','max:255'],
            'icon'    => ['nullable','string','max:10'],
        ]);

        $specialty->update([
            'name' => ['en' => $request->name_en, 'bn' => $request->name_bn],
            'icon' => $request->icon ?? $specialty->icon,
        ]);

        return redirect()->route('admin.specialties.index')->with('success', 'Specialty updated.');
    }

    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return redirect()->route('admin.specialties.index')->with('success', 'Deleted.');
    }

    public function create()  { return redirect()->route('admin.specialties.index'); }
    public function edit(Specialty $specialty) { return redirect()->route('admin.specialties.index'); }
    public function show(Specialty $specialty) { return redirect()->route('admin.specialties.index'); }
}
