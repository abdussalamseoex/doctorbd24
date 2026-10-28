<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class SymptomFactor extends Model
{
    use HasTranslations;

    protected $fillable = [
        'symptom_id',
        'factor_type',
        'question',
        'options',
        'order_index',
    ];

    public array $translatable = ['question'];

    protected $casts = [
        'options' => 'array',
        'order_index' => 'integer',
    ];

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class);
    }
}
