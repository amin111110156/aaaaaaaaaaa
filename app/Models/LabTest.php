<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $fillable = [
        'appointment_id',
        'test_type',
        'test_name',
        'description',
        'status',
        'result',
        'result_file',
        'requested_by',
        'performed_by',
        'requested_at',
        'completed_at'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->hasOneThrough(Patient::class, Appointment::class);
    }

    public function doctor()
    {
        return $this->hasOneThrough(Doctor::class, Appointment::class);
    }
}