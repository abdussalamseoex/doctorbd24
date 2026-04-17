<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class HospitalService extends Model
{
    use HasTranslations;

    public $translatable = ['service_name', 'description'];

    protected $fillable = [
        'hospital_id',
        'service_category',
        'service_name',
        'slug',
        'description',
        'price',
        'is_active',
    ];

    protected static function booted()
    {
        static::saving(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->service_name);
            }
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
