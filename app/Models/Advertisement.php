<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title', 
        'image_path', 
        'target_url', 
        'position', 
        'is_active', 
        'clicks'
    ];
}
