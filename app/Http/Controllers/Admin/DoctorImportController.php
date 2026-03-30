<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DoctorImportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        try {
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);

            if (!$header || count($header) < 1) {
                return redirect()->back()->with('error', 'Invalid CSV format or empty file.');
            }

            // Lowercase headers for matching
            $header = array_map('strtolower', $header);
            $header = array_map('trim', $header);

            $imported = 0;
            foreach ($data as $row) {
                if (count($header) !== count($row)) continue;
                $rowAssoc = array_combine($header, $row);

                if (empty($rowAssoc['name'])) continue; // Name is required

                Doctor::create([
                    'name'             => trim($rowAssoc['name']),
                    'slug'             => Str::slug(trim($rowAssoc['name']) . '-' . uniqid()),
                    'designation'      => $rowAssoc['designation'] ?? null,
                    'qualifications'   => $rowAssoc['qualifications'] ?? null,
                    'bio'              => $rowAssoc['bio'] ?? null,
                    'experience_years' => isset($rowAssoc['experience_years']) ? (int)$rowAssoc['experience_years'] : 0,
                    'bmdc_number'      => $rowAssoc['bmdc_number'] ?? null,
                    'phone'            => $rowAssoc['phone'] ?? null,
                    'email'            => $rowAssoc['email'] ?? null,
                    'gender'           => isset($rowAssoc['gender']) ? strtolower(trim($rowAssoc['gender'])) : 'other',
                    'verified'         => filter_var($rowAssoc['verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'featured'         => filter_var($rowAssoc['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
                $imported++;
            }

            return redirect()->back()->with('success', "{$imported} Doctors imported successfully from CSV!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
