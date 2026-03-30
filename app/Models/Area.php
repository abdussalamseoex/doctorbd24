<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Area extends Model
{
    use HasTranslations;

    protected $fillable = ['district_id', 'name', 'slug'];
    public array $translatable = ['name'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
