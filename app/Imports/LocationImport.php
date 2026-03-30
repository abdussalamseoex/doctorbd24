<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\District;
use App\Models\Area;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class LocationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Division
        $divSlug = $row['division_slug'] ?? Str::slug($row['division']);
        $division = Division::firstOrCreate(
            ['slug' => $divSlug],
            ['name' => ['en' => $row['division'], 'bn' => $row['division_bn'] ?? $row['division']]]
        );

        // 2. District
        $distSlug = $row['district_slug'] ?? Str::slug($row['district']);
        $district = District::firstOrCreate(
            ['slug' => $distSlug],
            [
                'division_id' => $division->id,
                'name'        => ['en' => $row['district'], 'bn' => $row['district_bn'] ?? $row['district']]
            ]
        );

        // 3. Area
        $areaSlug = $row['area_slug'] ?? Str::slug($row['area']);
        return new Area([
            'district_id' => $district->id,
            'name'        => ['en' => $row['area'], 'bn' => $row['area_bn'] ?? $row['area']],
            'slug'        => $areaSlug,
        ]);
    }
}
