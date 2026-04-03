<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateManagerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'doctor'); // doctor or hospital

        if ($type === 'doctor') {
            // Find duplicates by exact name or exact phone
            $duplicateNames = Doctor::select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');
            
            $duplicates = Doctor::whereIn('name', $duplicateNames)
                ->with(['chambers.hospital', 'specialties'])
                ->get()
                ->groupBy('name');

        } else {
            $duplicateNames = Hospital::select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');

            $duplicates = Hospital::whereIn('name', $duplicateNames)
                ->with('area')
                ->get()
                ->groupBy('name');
        }

        return view('admin.duplicates.index', compact('duplicates', 'type'));
    }

    public function merge(Request $request)
    {
        $request->validate([
            'type' => 'required|in:doctor,hospital',
            'primary_id' => 'required|integer',
            'duplicate_id' => 'required|integer',
            'merged_phone' => 'nullable|string',
            'merged_bio' => 'nullable|string',
        ]);

        $type = $request->type;
        $primaryId = $request->primary_id;
        $duplicateId = $request->duplicate_id;

        DB::beginTransaction();

        try {
            if ($type === 'doctor') {
                $primary = Doctor::findOrFail($primaryId);
                $duplicate = Doctor::findOrFail($duplicateId);

                // Update primary data
                if ($request->merged_phone) $primary->phone = $request->merged_phone;
                if ($request->merged_bio) $primary->bio = $request->merged_bio;
                $primary->save();

                // Merge Chambers
                foreach ($duplicate->chambers as $chamber) {
                    $chamber->update(['doctor_id' => $primary->id]);
                }

                // Merge Specialties
                $primarySpecialties = $primary->specialties->pluck('id')->toArray();
                $duplicateSpecialties = $duplicate->specialties->pluck('id')->toArray();
                $mergedSpecialties = array_unique(array_merge($primarySpecialties, $duplicateSpecialties));
                $primary->specialties()->sync($mergedSpecialties);

                // Merge Reviews
                foreach ($duplicate->reviews as $review) {
                    $review->update(['reviewable_id' => $primary->id]);
                }

                // Delete Duplicate
                $duplicate->delete();

                // Record SEO Redirect (assuming URL format is /doctor/slug)
                \App\Models\RedirectLog::record("doctor/{$duplicate->slug}", route('doctors.show', $primary->slug, false));

            } else {
                $primary = Hospital::findOrFail($primaryId);
                $duplicate = Hospital::findOrFail($duplicateId);

                if ($request->merged_phone) $primary->phone = $request->merged_phone;
                $primary->save();

                // Merge Chambers (Doctors belonging to this Hospital)
                foreach ($duplicate->chambers as $chamber) {
                    $chamber->update(['hospital_id' => $primary->id]);
                }

                // Merge Reviews
                foreach ($duplicate->reviews as $review) {
                    $review->update(['reviewable_id' => $primary->id]);
                }

                $duplicate->delete();

                \App\Models\RedirectLog::record("hospital/{$duplicate->slug}", route('hospitals.show', $primary->slug, false));
            }

            DB::commit();
            return back()->with('success', ucfirst($type) . ' profiles merged successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error merging: ' . $e->getMessage());
        }
    }
}
