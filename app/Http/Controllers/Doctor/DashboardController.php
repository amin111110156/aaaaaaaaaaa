<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user()->doctor;
        
        // إحصائيات سريعة
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_time', today())
            ->count();
            
        $newPatients = Patient::whereHas('appointments', function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->whereDate('created_at', '>=', today()->subDays(30))
            ->count();
            
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_time', '>', now())
            ->where('status', 'confirmed')
            ->with(['patient.user', 'service'])
            ->orderBy('appointment_time', 'asc')
            ->limit(5)
            ->get();

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'newPatients',
            'upcomingAppointments'
        ));
    }
}