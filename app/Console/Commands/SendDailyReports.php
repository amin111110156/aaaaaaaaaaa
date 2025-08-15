<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\NotificationSetting;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendDailyReports extends Command
{
    protected $signature = 'notifications:daily-reports';
    protected $description = 'إرسال التقارير اليومية للمستخدمين';

    public function handle()
    {
        $settings = NotificationSetting::whereNotNull('daily_report_time')
            ->where('financial_report_enabled', true)
            ->orWhere('doctor_report_enabled', true)
            ->with('user')
            ->get();

        foreach ($settings as $setting) {
            $currentTime = Carbon::now()->format('H:i');
            
            if ($currentTime === $setting->daily_report_time) {
                $this->sendReports($setting);
            }
        }

        $this->info('تم إرسال التقارير اليومية');
    }

    private function sendReports($setting)
    {
        $user = $setting->user;
        $today = Carbon::today();
        
        // تقرير مالي
        if ($setting->financial_report_enabled) {
            $todayRevenue = \App\Models\Payment::whereDate('created_at', $today)
                ->sum('amount');
            
            $todayAppointments = Appointment::whereDate('created_at', $today)->count();
            
            $message = "📊 *تقرير يومي - " . $today->format('Y-m-d') . "*\n\n";
            $message .= "💰 الإيرادات اليومية: " . number_format($todayRevenue, 2) . " ج.م\n";
            $message .= "📅 عدد المواعيد: " . $todayAppointments . "\n";
            
            NotificationService::sendNotification($user, $message);
        }
        
        // تقرير الأطباء
        if ($setting->doctor_report_enabled && $user->role === 'doctor') {
            $todayAppointments = Appointment::where('doctor_id', $user->doctor->id)
                ->whereDate('appointment_time', $today)
                ->count();
                
            $followupAppointments = Appointment::where('doctor_id', $user->doctor->id)
                ->whereDate('appointment_time', $today)
                ->whereHas('prescription', function($query) {
                    $query->whereNotNull('followup_date');
                })
                ->count();
            
            $message = "👨‍⚕️ *تقرير طبيبي - " . $today->format('Y-m-d') . "*\n\n";
            $message .= "📅 عدد الكشوفات: " . $todayAppointments . "\n";
            $message .= "🔄 عدد الإعادات: " . $followupAppointments . "\n";
            
            NotificationService::sendNotification($user, $message);
        }
    }
}