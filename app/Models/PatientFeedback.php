<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFeedback extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'doctor_id',
        'rating',
        'comment',
        'category',
        'status'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}