<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // العلاقة مع المريض
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    // العلاقة مع الطبيب
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // العلاقة مع إعدادات الإشعارات
    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    // إنشاء إعدادات الإشعارات تلقائيًا عند إنشاء المستخدم
    public static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            // التحقق من عدم وجود إعدادات مسبقة
            if (!$user->notificationSetting) {
                try {
                    $user->notificationSetting()->create([
                        'whatsapp_enabled' => false,
                        'telegram_enabled' => false,
                        'financial_report_enabled' => false,
                        'doctor_report_enabled' => false,
                        'patient_reminder_enabled' => false
                    ]);
                } catch (\Exception $e) {
                    // تجاهل الخطأ إذا كانت الإعدادات موجودة بالفعل
                    \Log::warning('Failed to create notification setting for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        });
    }
}