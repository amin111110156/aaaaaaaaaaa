<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'dob',
        'gender',
        'address',
        'medical_history',
        'allergies',
        'chronic_conditions',
        'family_history',
        'insurance_info',
        'emergency_contact',
    ];

    protected $casts = [
        'dob' => 'date',
        'allergies' => 'array',
        'chronic_conditions' => 'array',
        'family_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions()
    {
        return $this->hasManyThrough(Prescription::class, Appointment::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Appointment::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }

    public function labTests()
    {
        return $this->hasManyThrough(LabTest::class, Appointment::class);
    }

    public function teleconsultations()
    {
        return $this->hasMany(Teleconsultation::class);
    }

    public function crm()
    {
        return $this->hasOne(PatientCRM::class);
    }

    public function reminders()
    {
        return $this->hasManyThrough(PatientReminder::class, PatientCRM::class);
    }

    public function feedback()
    {
        return $this->hasManyThrough(PatientFeedback::class, PatientCRM::class);
    }
}