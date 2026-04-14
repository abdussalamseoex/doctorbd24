<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\HospitalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminHospitalServiceController extends Controller
{
    public function index(Hospital $hospital)
    {
        $services = $hospital->hospitalServices()->orderBy('service_category')->orderBy('service_name')->paginate(50);
        return view('admin.hospital_services.index', compact('hospital', 'services'));
    }

    public function store(Request $request, Hospital $hospital)
    {
        $request->validate([
            'service_category' => 'nullable|string|max:255',
            'service_name'     => 'required|string|max:500',
            'price'            => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $hospital->hospitalServices()->create([
            'service_category' => $request->service_category,
            'service_name'     => $request->service_name,
            'price'            => $request->price,
            'is_active'        => $request->has('is_active') ? $request->is_active : true,
        ]);

        return back()->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Hospital $hospital, HospitalService $service)
    {
        if ($service->hospital_id !== $hospital->id) {
            abort(403);
        }

        $request->validate([
            'service_category' => 'nullable|string|max:255',
            'service_name'     => 'required|string|max:500',
            'price'            => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $service->update([
            'service_category' => $request->service_category,
            'service_name'     => $request->service_name,
            'price'            => $request->price,
            'is_active'        => $request->has('is_active') ? $request->is_active : true,
        ]);

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroy(Hospital $hospital, HospitalService $service)
    {
        if ($service->hospital_id !== $hospital->id) {
            abort(403);
        }
        $service->delete();
        return back()->with('success', 'Service deleted successfully.');
    }

    public function clearAll(Hospital $hospital)
    {
        $hospital->hospitalServices()->delete();
        return back()->with('success', 'All services cleared for this hospital.');
    }

    public function import(Request $request, Hospital $hospital)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), "r");
            
            $header = fgetcsv($handle, 10000, ",");
            if (!$header) {
                return back()->with('error', 'Invalid CSV file format.');
            }

            // Normalize headers
            $header = array_map(function($val) {
                return strtolower(trim(preg_replace('/[^A-Za-z0-9_]/', '', $val)));
            }, $header);

            // Find column indexes
            $nameIdx = -1;
            $catIdx = -1;
            $priceIdx = -1;

            foreach($header as $idx => $col) {
                if (str_contains($col, 'name')) $nameIdx = $idx;
                if (str_contains($col, 'categor')) $catIdx = $idx;
                if (str_contains($col, 'price') || str_contains($col, 'charge') || str_contains($col, 'tk')) $priceIdx = $idx;
            }

            if ($nameIdx === -1 || $priceIdx === -1) {
                // Try fallback based on standard 4 column: SL, Category, Name, Price
                if (count($header) >= 4) {
                    $catIdx = 1;
                    $nameIdx = 2;
                    $priceIdx = 3;
                } else {
                    return back()->with('error', 'Could not detect Name and Price columns. Please use a standard CSV structure.');
                }
            }

            $currentCategory = null;
            $currentName = null;
            $servicesToInsert = [];

            DB::beginTransaction();

            // Clear old if requested
            if ($request->has('clear_old')) {
                $hospital->hospitalServices()->delete();
            }

            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                // Handle alternating empty rows like SL, , , Price (used as urgent price or just weird PDF copy)
                $cat = isset($data[$catIdx]) ? trim($data[$catIdx]) : '';
                $name = isset($data[$nameIdx]) ? trim($data[$nameIdx]) : '';
                $price = isset($data[$priceIdx]) ? trim($data[$priceIdx]) : '';

                if (empty($cat) && empty($name) && empty($price)) {
                    continue; // Completely empty row
                }

                // If row has no name but has a price, we assume it belongs to the PREVIOUS item 
                // We'll append the price to the previous item.
                if (empty($name) && !empty($price)) {
                    if (count($servicesToInsert) > 0) {
                        $lastIdx = count($servicesToInsert) - 1;
                        if (!str_contains($servicesToInsert[$lastIdx]['price'], $price)) {
                            // Format: "Norm: 500 | Adv: 700" etc.
                            $servicesToInsert[$lastIdx]['price'] .= ' / ' . $price;
                        }
                    }
                    continue;
                }

                if (!empty($cat)) {
                    $currentCategory = $cat; // Remember last seen category
                }

                if (!empty($name)) {
                    $servicesToInsert[] = [
                        'hospital_id' => $hospital->id,
                        'service_category' => Str::limit($currentCategory ?? 'Uncategorized', 250),
                        'service_name' => Str::limit($name, 490),
                        'price' => Str::limit($price, 250),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            fclose($handle);

            // Chunk insert
            $chunks = array_chunk($servicesToInsert, 500);
            foreach ($chunks as $chunk) {
                HospitalService::insert($chunk);
            }

            DB::commit();
            return back()->with('success', count($servicesToInsert) . ' services imported successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hospital Service Import Error: ' . $e->getMessage());
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
