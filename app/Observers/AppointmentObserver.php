<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Notifications\AppointmentNotification;
use App\Services\NotificationService;
use App\Services\SmsService;

class AppointmentObserver
{
    public function created(Appointment $appointment)
    {
        // إرسال إشعار عند إنشاء موعد جديد
        $appointment->patient->user->notify(new AppointmentNotification($appointment, 'created'));
        
        // إرسال SMS للمريض
        $message = "مرحباً {$appointment->patient->user->name}، تم حجز موعدك مع الدكتور {$appointment->doctor->user->name} في {$appointment->appointment_time->format('Y-m-d H:i')}";
        SmsService::send($appointment->patient->phone, $message);
        
        // إرسال عبر WhatsApp أو Telegram إذا تم تفعيلها
        $setting = $appointment->patient->user->notificationSetting;
        if ($setting) {
            $whatsappMessage = "📅 *تأكيد حجز موعد*\n\n";
            $whatsappMessage .= "👨‍⚕️ الطبيب: {$appointment->doctor->user->name}\n";
            $whatsappMessage .= "📅 التاريخ: {$appointment->appointment_time->format('Y-m-d H:i')}\n";
            $whatsappMessage .= "📍 نتطلع لخدمتكم في الموعد المحدد";
            
            NotificationService::sendNotification($appointment->patient->user, $whatsappMessage);
        }
    }

    public function updated(Appointment $appointment)
    {
        // إرسال إشعار عند تحديث حالة الموعد
        if ($appointment->isDirty('status')) {
            $type = $appointment->status;
            $appointment->patient->user->notify(new AppointmentNotification($appointment, $type));
            
            // إرسال SMS حسب الحالة
            $message = '';
            switch ($appointment->status) {
                case 'confirmed':
                    $message = "تم تأكيد موعدك مع الدكتور {$appointment->doctor->user->name} في {$appointment->appointment_time->format('Y-m-d H:i')}";
                    break;
                case 'cancelled':
                    $message = "تم إلغاء موعدك مع الدكتور {$appointment->doctor->user->name}";
                    break;
            }
            
            if ($message) {
                SmsService::send($appointment->patient->phone, $message);
                
                // إرسال عبر WhatsApp أو Telegram
                $setting = $appointment->patient->user->notificationSetting;
                if ($setting) {
                    $whatsappMessage = "🔔 *تحديث حالة موعد*\n\n";
                    $whatsappMessage .= "👨‍⚕️ الطبيب: {$appointment->doctor->user->name}\n";
                    $whatsappMessage .= "📊 الحالة: " . ($appointment->status == 'confirmed' ? 'مؤكّد' : 'ملغى') . "\n";
                    
                    NotificationService::sendNotification($appointment->patient->user, $whatsappMessage);
                }
            }
        }
    }
}