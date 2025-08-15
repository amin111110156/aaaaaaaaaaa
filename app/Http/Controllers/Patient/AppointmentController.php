<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'service'])
            ->orderBy('appointment_time', 'desc')
            ->get();
        
        return view('patient.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $doctors = Doctor::with('user')->get();
        $services = Service::all();
        return view('patient.appointments.create', compact('doctors', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_time' => 'required|date|after:now',
        ]);

        $patient = auth()->user()->patient;

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'service_id' => $request->service_id,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);

        return redirect()->route('patient.appointments.index')->with('success', 'تم حجز الموعد بنجاح وينتظر التأكيد');
    }

    public function show(Appointment $appointment)
    {
        $patient = auth()->user()->patient;
        if ($appointment->patient_id != $patient->id) {
            abort(403);
        }
        
        return view('patient.appointments.show', compact('appointment'));
    }
}