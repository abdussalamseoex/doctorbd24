<?php

namespace App\Imports;

use App\Models\Hospital;
use App\Models\Area;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class HospitalImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $area = Area::where('name->en', 'like', '%' . $row['area'] . '%')
                    ->orWhere('name->bn', 'like', '%' . $row['area'] . '%')
                    ->first();

        return new Hospital([
            'user_id' => auth()->id() ?? 1,
            'name'    => $row['name'],
            'slug'    => $row['slug'] ?? Str::slug($row['name']),
            'type'    => $row['type'] ?? 'General',
            'phone'   => $row['phone'],
            'email'   => $row['email'],
            'address' => $row['address'],
            'area_id' => $area ? $area->id : null,
            'about'   => $row['about'],
            'verified'=> true,
        ]);
    }
}
