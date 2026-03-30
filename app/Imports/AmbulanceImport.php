<?php

namespace App\Imports;

use App\Models\Ambulance;
use App\Models\Area;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class AmbulanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $area = Area::where('name->en', 'like', '%' . $row['area'] . '%')
                    ->orWhere('name->bn', 'like', '%' . $row['area'] . '%')
                    ->first();

        return new Ambulance([
            'user_id'       => auth()->id() ?? 1,
            'provider_name' => $row['provider_name'],
            'slug'          => $row['slug'] ?? Str::slug($row['provider_name']),
            'phone'         => $row['phone'],
            'address'       => $row['address'],
            'area_id'       => $area ? $area->id : null,
            'available_24h' => strtolower($row['available_24h'] ?? '') === 'yes',
            'active'        => true,
            'is_verified'   => true,
        ]);
    }
}
