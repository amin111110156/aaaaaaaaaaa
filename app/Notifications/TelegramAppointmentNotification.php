<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TelegramAppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $type;

    public function __construct($appointment, $type)
    {
        $this->appointment = $appointment;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toTelegram($notifiable)
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$botToken || !$chatId) {
            return;
        }

        $message = $this->getMessage();
        
        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }

    private function getMessage()
    {
        $patientName = $this->appointment->patient->user->name;
        $doctorName = $this->appointment->doctor->user->name;
        $appointmentTime = $this->appointment->appointment_time->format('Y-m-d H:i');

        switch ($this->type) {
            case 'created':
                return "🔔 *حجز موعد جديد*\n\n👨‍⚕️ الطبيب: {$doctorName}\n👤 المريض: {$patientName}\n📅 التاريخ: {$appointmentTime}\n📊 الحالة: معلق";
            
            case 'confirmed':
                return "✅ *تم تأكيد الموعد*\n\n👨‍⚕️ الطبيب: {$doctorName}\n👤 المريض: {$patientName}\n📅 التاريخ: {$appointmentTime}\n📊 الحالة: مؤكّد";
            
            case 'cancelled':
                return "❌ *تم إلغاء الموعد*\n\n👨‍⚕️ الطبيب: {$doctorName}\n👤 المريض: {$patientName}\n📅 التاريخ: {$appointmentTime}\n📊 الحالة: ملغى";
            
            default:
                return "ℹ️ *إشعار موعد*\n\n👨‍⚕️ الطبيب: {$doctorName}\n👤 المريض: {$patientName}\n📅 التاريخ: {$appointmentTime}";
        }
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'type' => $this->type,
            'message' => $this->getMessage(),
        ];
    }
}