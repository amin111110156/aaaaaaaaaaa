<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification
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
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $message = '';
        $actionUrl = '';

        switch ($this->type) {
            case 'confirmed':
                $message = 'تم تأكيد موعدك مع الدكتور ' . $this->appointment->doctor->user->name;
                $actionUrl = url('/patient/appointments/' . $this->appointment->id);
                break;
            case 'cancelled':
                $message = 'تم إلغاء موعدك مع الدكتور ' . $this->appointment->doctor->user->name;
                break;
            case 'reminder':
                $message = 'تذكير بموعدك مع الدكتور ' . $this->appointment->doctor->user->name . ' غداً';
                $actionUrl = url('/patient/appointments/' . $this->appointment->id);
                break;
        }

        $mail = (new MailMessage)
            ->subject('إشعار موعد - نظام إدارة العيادة')
            ->line($message)
            ->line('التاريخ: ' . $this->appointment->appointment_time);

        if ($actionUrl) {
            $mail->action('عرض الموعد', $actionUrl);
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'type' => $this->type,
            'message' => $this->getMessage(),
        ];
    }

    private function getMessage()
    {
        switch ($this->type) {
            case 'confirmed':
                return 'تم تأكيد موعدك مع الدكتور ' . $this->appointment->doctor->user->name;
            case 'cancelled':
                return 'تم إلغاء موعدك مع الدكتور ' . $this->appointment->doctor->user->name;
            case 'reminder':
                return 'تذكير بموعدك مع الدكتور ' . $this->appointment->doctor->user->name . ' غداً';
            default:
                return 'إشعار جديد';
        }
    }
}