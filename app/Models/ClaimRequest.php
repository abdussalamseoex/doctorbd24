<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimRequest extends Model
{
    protected $fillable = ['user_id', 'doctor_id', 'hospital_id', 'ambulance_id', 'status', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }
}
