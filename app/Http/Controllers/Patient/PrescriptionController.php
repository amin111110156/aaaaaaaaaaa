<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        $prescriptions = Prescription::whereHas('appointment', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with(['appointment.doctor.user', 'medications'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.prescriptions.index', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        $patient = auth()->user()->patient;
        if ($prescription->appointment->patient_id != $patient->id) {
            abort(403);
        }
        
        return view('patient.prescriptions.show', compact('prescription'));
    }
}