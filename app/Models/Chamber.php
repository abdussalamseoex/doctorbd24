<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Chamber extends Model
{
    use HasTranslations;

    public $translatable = ['address', 'visiting_hours', 'closed_days'];

    protected $fillable = [
        'doctor_id', 'hospital_id', 'name', 'address', 'area_id',
        'visiting_hours', 'closed_days', 'phone', 'lat', 'lng', 'google_maps_url', 'sort_order',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
