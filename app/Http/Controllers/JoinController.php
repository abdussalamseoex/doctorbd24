<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use App\Models\Specialty;
use Illuminate\Http\Request;

class JoinController extends Controller
{
    public function doctorForm()
    {
        $specialties = Specialty::orderBy('name->en')->get();
        $divisions   = \App\Models\Division::orderBy('id')->get();
        return view('join.doctor', compact('specialties', 'divisions'));
    }

    public function submitDoctor(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['required', 'email', 'max:255'],
            'specialty'      => ['nullable', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string', 'max:500'],
            'message'        => ['nullable', 'string', 'max:1000'],
            'division_id'    => ['nullable', 'exists:divisions,id'],
            'district_id'    => ['nullable', 'exists:districts,id'],
            'area_id'        => ['nullable', 'exists:areas,id'],
        ]);

        JoinRequest::create(array_merge($validated, ['type' => 'doctor']));

        return redirect()->route('join.doctor')->with('success', 'আপনার আবেদন সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করবো।');
    }

    public function hospitalForm()
    {
        $divisions = \App\Models\Division::orderBy('id')->get();
        return view('join.hospital', compact('divisions'));
    }

    public function submitHospital(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'area_id'     => ['nullable', 'exists:areas,id'],
        ]);

        JoinRequest::create(array_merge($validated, ['type' => 'hospital']));

        return redirect()->route('join.hospital')->with('success', 'আপনার হাসপাতাল লিস্টিং আবেদন পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করবো।');
    }
}
