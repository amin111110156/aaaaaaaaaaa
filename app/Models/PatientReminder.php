<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientReminder extends Model
{
    protected $fillable = [
        'patient_id',
        'reminder_type',
        'title',
        'description',
        'reminder_date',
        'reminder_time',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'reminder_time' => 'time',
        'sent_at' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}