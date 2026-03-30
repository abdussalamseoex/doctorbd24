<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $fillable = [
        'metasable_id',
        'metasable_type',
        'path',
        'title',
        'description',
        'keywords',
        'og_image',
    ];

    public function metasable()
    {
        return $this->morphTo();
    }
}
