<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendPatientReminders extends Command
{
    protected $signature = 'notifications:patient-reminders';
    protected $description = 'إرسال تذكيرات المرضى';

    public function handle()
    {
        // تذكير بالتحاليل والأشعة
        $this->sendAnalysisReminders();
        
        // تذكير بمواعيد الإعادة
        $this->sendFollowupReminders();
        
        $this->info('تم إرسال تذكيرات المرضى');
    }

    private function sendAnalysisReminders()
    {
        // يمكنك إضافة منطق تذكير بالتحاليل هنا
        // مثلاً: البحث عن التحاليل المجدولة ليوم الغد
    }

    private function sendFollowupReminders()
    {
        $tomorrow = Carbon::tomorrow();
        
        $appointments = Appointment::whereDate('appointment_time', $tomorrow)
            ->with(['patient.user', 'doctor.user'])
            ->get();
            
        foreach ($appointments as $appointment) {
            $setting = $appointment->patient->user->notificationSetting;
            
            if ($setting && $setting->patient_reminder_enabled) {
                $message = "🔔 *تذكير موعد طبي*\n\n";
                $message .= "👨‍⚕️ الطبيب: " . $appointment->doctor->user->name . "\n";
                $message .= "📅 التاريخ: " . $appointment->appointment_time->format('Y-m-d H:i') . "\n";
                $message .= "📍 نتطلع لرؤيتك في الموعد المحدد";
                
                NotificationService::sendNotification($appointment->patient->user, $message);
            }
        }
    }
}