<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class BodyPart extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'order_index',
        'is_active',
    ];

    public array $translatable = ['name'];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];

    public function symptoms(): HasMany
    {
        return $this->hasMany(Symptom::class)->orderBy('order_index', 'asc');
    }
}
