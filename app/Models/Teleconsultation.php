<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teleconsultation extends Model
{
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration',
        'status',
        'meeting_link',
        'notes',
        'prescription_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}