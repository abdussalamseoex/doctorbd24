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
            $duplicateNames = Doctor::where('is_duplicate_ignored', false)
                ->select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');
            
            $duplicates = Doctor::whereIn('name', $duplicateNames)
                ->where('is_duplicate_ignored', false)
                ->with(['chambers.hospital', 'specialties'])
                ->get()
                ->groupBy('name');

        } else {
            $duplicateNames = Hospital::where('is_duplicate_ignored', false)
                ->select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');

            $duplicates = Hospital::whereIn('name', $duplicateNames)
                ->where('is_duplicate_ignored', false)
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
            'duplicate_ids' => 'required|array',
            'duplicate_ids.*' => 'integer',
            'merged_phone' => 'nullable|string',
            'merged_bio' => 'nullable|string',
        ]);

        $type = $request->type;
        $primaryId = $request->primary_id;
        $duplicateIds = $request->duplicate_ids;

        DB::beginTransaction();

        try {
            if ($type === 'doctor') {
                $primary = Doctor::findOrFail($primaryId);

                // Update primary data
                if ($request->merged_phone) $primary->phone = $request->merged_phone;
                if ($request->merged_bio) $primary->bio = $request->merged_bio;
                $primary->save();

                foreach ($duplicateIds as $duplicateId) {
                    $duplicate = Doctor::find($duplicateId);
                    if (!$duplicate) continue;

                    // Merge Chambers
                    foreach ($duplicate->chambers as $chamber) {
                        $exists = $primary->chambers()
                            ->where('name', $chamber->name)
                            ->where('area_id', $chamber->area_id)
                            ->where('hospital_id', $chamber->hospital_id)
                            ->exists();

                        if ($exists) {
                            $chamber->delete();
                        } else {
                            $chamber->update(['doctor_id' => $primary->id]);
                        }
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

                    // Record SEO Redirect
                    \App\Models\RedirectLog::record("doctor/{$duplicate->slug}", route('doctors.show', $primary->slug, false));
                }

            } else {
                $primary = Hospital::findOrFail($primaryId);

                if ($request->merged_phone) $primary->phone = $request->merged_phone;
                $primary->save();

                foreach ($duplicateIds as $duplicateId) {
                    $duplicate = Hospital::find($duplicateId);
                    if (!$duplicate) continue;

                    // Merge Chambers (Doctors belonging to this Hospital)
                    foreach ($duplicate->chambers as $chamber) {
                        $exists = $primary->chambers()
                            ->where('doctor_id', $chamber->doctor_id)
                            ->where('name', $chamber->name)
                            ->where('area_id', $chamber->area_id)
                            ->exists();

                        if ($exists) {
                            $chamber->delete();
                        } else {
                            $chamber->update(['hospital_id' => $primary->id]);
                        }
                    }

                    // Merge Reviews
                    foreach ($duplicate->reviews as $review) {
                        $review->update(['reviewable_id' => $primary->id]);
                    }

                    $duplicate->delete();

                    \App\Models\RedirectLog::record("hospital/{$duplicate->slug}", route('hospitals.show', $primary->slug, false));
                }
            }

            DB::commit();
            return back()->with('success', ucfirst($type) . ' profiles merged successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error merging: ' . $e->getMessage());
        }
    }

    public function ignore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:doctor,hospital',
            'id' => 'required|integer',
        ]);

        if ($request->type === 'doctor') {
            Doctor::where('id', $request->id)->update(['is_duplicate_ignored' => true]);
        } else {
            Hospital::where('id', $request->id)->update(['is_duplicate_ignored' => true]);
        }

        return redirect()->back()->with('success', 'Profile marked as not a duplicate and removed from this list.');
    }
}
