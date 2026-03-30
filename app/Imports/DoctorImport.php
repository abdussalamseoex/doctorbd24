<?php

namespace App\Imports;

use App\Models\Doctor;
use App\Models\Specialty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class DoctorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $doctor = Doctor::create([
            'user_id'          => auth()->id() ?? 1,
            'name'             => $row['name'],
            'slug'             => $row['slug'] ?? Str::slug($row['name'] . '-' . rand(100, 999)),
            'qualifications'   => $row['qualifications'],
            'designation'      => $row['designation'],
            'experience_years' => $row['experience_years'] ?? 0,
            'phone'            => $row['phone'],
            'email'            => $row['email'],
            'bmdc_number'      => $row['bmdc_number'],
            'bio'              => $row['bio'],
            'verified'         => true,
        ]);

        if (!empty($row['specialties'])) {
            $specialtyNames = array_map('trim', explode(',', $row['specialties']));
            foreach ($specialtyNames as $name) {
                $specialty = Specialty::where('name->en', 'like', '%' . $name . '%')
                                    ->orWhere('name->bn', 'like', '%' . $name . '%')
                                    ->first();
                if ($specialty) {
                    $doctor->specialties()->attach($specialty->id);
                }
            }
        }

        return $doctor;
    }
}
