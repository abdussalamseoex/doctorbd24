<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use HasTranslations;

    protected $fillable = ['division_id', 'name', 'slug'];
    public array $translatable = ['name'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }
}
