<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalService extends Model
{
    protected $fillable = [
        'hospital_id',
        'service_category',
        'service_name',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
