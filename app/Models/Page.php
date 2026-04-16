<?php

namespace App\Models;

use App\Traits\Publishable;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use Publishable, HasTranslations;

    public $translatable = ['title', 'content'];

    protected $fillable = [
        'status', 'published_at',
        'title',
        'slug',
        'content',
        ];

    protected $casts = [
        'published_at' => 'datetime',
        ];
}
