<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HospitalImport;
use App\Imports\AmbulanceImport;
use App\Imports\BlogPostImport;
use App\Imports\LocationImport;
use App\Imports\DoctorImport;
use Illuminate\Support\Facades\Log;

class BulkImportController extends Controller
{
    public function hospital(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        try {
            Excel::import(new HospitalImport, $request->file('file'));
            return back()->with('success', 'Hospitals imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Hospital import failed: ' . $e->getMessage()]);
        }
    }

    public function ambulance(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        try {
            Excel::import(new AmbulanceImport, $request->file('file'));
            return back()->with('success', 'Ambulances imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Ambulance import failed: ' . $e->getMessage()]);
        }
    }

    public function blogPost(Request $request)
    {
        Log::info('BulkImportController@blogPost called');
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        Log::info('File validated: ' . $request->file('file')->getClientOriginalName());
        try {
            Excel::import(new BlogPostImport, $request->file('file'));
            Log::info('Excel::import finished');
            return back()->with('success', 'Blog posts imported successfully.');
        } catch (\Exception $e) {
            Log::error('Blog import failed: ' . $e->getMessage());
            return back()->withErrors(['file' => 'Blog import failed: ' . $e->getMessage()]);
        }
    }

    public function location(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        try {
            Excel::import(new LocationImport, $request->file('file'));
            return back()->with('success', 'Locations imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Location import failed: ' . $e->getMessage()]);
        }
    }

    public function doctor(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);
        try {
            Excel::import(new DoctorImport, $request->file('file'));
            return back()->with('success', 'Doctors imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Doctor import failed: ' . $e->getMessage()]);
        }
    }
}
