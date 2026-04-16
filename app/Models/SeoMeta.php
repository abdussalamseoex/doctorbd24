<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SeoMeta extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description', 'keywords'];

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
