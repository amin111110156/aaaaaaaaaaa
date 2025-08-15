<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'whatsapp_enabled',
        'whatsapp_phone',
        'telegram_enabled',
        'telegram_chat_id',
        'daily_report_time',
        'analysis_reminder_time',
        'followup_reminder_time',
        'financial_report_enabled',
        'doctor_report_enabled',
        'patient_reminder_enabled'
    ];

    protected $casts = [
        'whatsapp_enabled' => 'boolean',
        'telegram_enabled' => 'boolean',
        'financial_report_enabled' => 'boolean',
        'doctor_report_enabled' => 'boolean',
        'patient_reminder_enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}