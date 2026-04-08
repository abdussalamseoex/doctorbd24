<?php

namespace App\Models;

use App\Traits\Publishable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoLandingPage extends Model
{
    use HasFactory, Publishable;

    protected $fillable = [
        'status', 'published_at',
        'type',
        'specialty_id',
        'division_id',
        'district_id',
        'area_id',
        'slug',
        'keyword',
        'title',
        'meta_title',
        'meta_description',
        'content_top',
        'content_bottom',
        'faq_schema',
        ];

    protected $casts = [
        'published_at' => 'datetime',
        'faq_schema' => 'array',
        ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
