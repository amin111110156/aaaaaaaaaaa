<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientCRM extends Model
{
    protected $fillable = [
        'patient_id',
        'last_visit',
        'next_appointment',
        'total_visits',
        'total_spent',
        'loyalty_points',
        'preferred_doctor',
        'preferred_time',
        'communication_preferences',
        'allergies',
        'chronic_conditions',
        'family_history',
        'insurance_info',
        'emergency_contact'
    ];

    protected $casts = [
        'last_visit' => 'datetime',
        'next_appointment' => 'datetime',
        'communication_preferences' => 'array',
        'allergies' => 'array',
        'chronic_conditions' => 'array',
        'family_history' => 'array',
        'total_spent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function preferredDoctor()
    {
        return $this->belongsTo(Doctor::class, 'preferred_doctor');
    }

    public function reminders()
    {
        return $this->hasMany(PatientReminder::class);
    }

    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class);
    }
}