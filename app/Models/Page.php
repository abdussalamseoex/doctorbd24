<?php

namespace App\Models;

use App\Traits\Publishable;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Publishable;

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
