<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    protected $fillable = [
        'type', 'name', 'phone', 'email', 'specialty',
        'qualifications', 'message', 'status', 'admin_note',
        'division_id', 'district_id', 'area_id',
    ];

    public function division() { return $this->belongsTo(Division::class); }
    public function district() { return $this->belongsTo(District::class); }
    public function area()     { return $this->belongsTo(Area::class); }
}
