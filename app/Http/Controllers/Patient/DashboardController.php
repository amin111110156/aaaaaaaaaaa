<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'service'])
            ->orderBy('appointment_time', 'desc')
            ->limit(5)
            ->get();
        
        $prescriptions = Prescription::whereHas('appointment', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with('appointment.doctor.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $invoices = Invoice::whereHas('appointment', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with('appointment.service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('patient.dashboard', compact('appointments', 'prescriptions', 'invoices'));
    }
}