<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'appointment_id',
        'notes',
        'followup_date',
        'followup_reason',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medications()
    {
        return $this->hasMany(PrescriptionMedication::class);
    }

    // للحصول على اسم المريض مباشرة
    public function getPatientNameAttribute()
    {
        return $this->appointment->patient->user->name ?? '-';
    }

    // للحصول على اسم الطبيب مباشرة
    public function getDoctorNameAttribute()
    {
        return $this->appointment->doctor->user->name ?? '-';
    }
}
