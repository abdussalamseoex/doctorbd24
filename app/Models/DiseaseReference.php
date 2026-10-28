<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseReference extends Model
{
    protected $fillable = [
        'disease_id',
        'source_name',
        'source_url',
        'notes',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
