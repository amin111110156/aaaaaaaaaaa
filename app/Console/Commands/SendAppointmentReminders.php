<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AppointmentNotification;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind';
    protected $description = 'إرسال تذكيرات المواعيد';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        $appointments = Appointment::whereDate('appointment_time', $tomorrow)
            ->where('status', 'confirmed')
            ->with(['patient.user', 'doctor.user'])
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->patient->user->notify(new AppointmentNotification($appointment, 'reminder'));
        }

        $this->info('تم إرسال ' . $appointments->count() . ' تذكير موعد');
    }
}