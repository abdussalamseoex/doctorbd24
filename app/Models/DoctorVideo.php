<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorVideo extends Model
{
    protected $fillable = [
        'doctor_id', 'provider', 'video_url', 'youtube_id', 'title', 'description', 'slug', 'thumbnail_url', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
