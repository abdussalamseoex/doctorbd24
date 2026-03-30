<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Division extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'slug'];
    public array $translatable = ['name'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
