<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_url',
        'to_url',
        'hits',
        'last_hit_at',
    ];

    /**
     * Helper to log or increment a redirect.
     */
    public static function record($from, $to)
    {
        $log = self::firstOrCreate(
            ['from_url' => $from],
            ['to_url' => $to]
        );
        $log->increment('hits');
        $log->update(['last_hit_at' => now(), 'to_url' => $to]); // keep 'to_url' updated just in case
        return $log;
    }
}
