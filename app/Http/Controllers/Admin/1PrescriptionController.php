<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\PharmacyMedication;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::with([
            'appointment.patient.user',
            'appointment.doctor.user',
            'appointment.service'
        ])->orderBy('created_at', 'desc')->get();

        return view('admin.prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])
            ->whereDoesntHave('prescription')
            ->orderBy('appointment_time', 'desc')
            ->get();
            
        $medications = PharmacyMedication::select('id', 'medication_name')->get();

        return view('admin.prescriptions.create', compact('appointments', 'medications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string',
            'followup_date' => 'nullable|date|after:today',
            'followup_reason' => 'nullable|string|max:255',
            'medications' => 'required|array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:100',
            'medications.*.frequency' => 'required|string|max:100',
            'medications.*.duration' => 'required|integer|min:1',
            'medications.*.instructions' => 'nullable|string',
        ]);

        $prescription = Prescription::create([
            'appointment_id' => $request->appointment_id,
            'notes' => $request->notes,
            'followup_date' => $request->followup_date,
            'followup_reason' => $request->followup_reason,
        ]);

        foreach ($request->medications as $medication) {
            $prescription->medications()->create([
                'medication_name' => $medication['name'],
                'dosage' => $medication['dosage'],
                'frequency' => $medication['frequency'],
                'duration_days' => $medication['duration'],
                'instructions' => $medication['instructions'] ?? null,
            ]);
        }

        return redirect()->route('admin.prescriptions.index')->with('success', 'تم إنشاء الروشته بنجاح');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load([
            'appointment.patient.user',
            'appointment.doctor.user',
            'appointment.service',
            'medications'
        ]);

        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])->get();
        $medications = PharmacyMedication::select('id', 'medication_name')->get();
        return view('admin.prescriptions.edit', compact('prescription', 'appointments', 'medications'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string',
            'followup_date' => 'nullable|date|after:today',
            'followup_reason' => 'nullable|string|max:255',
        ]);

        $prescription->update($request->all());

        return redirect()->route('admin.prescriptions.index')->with('success', 'تم تحديث الروشته بنجاح');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('admin.prescriptions.index')->with('success', 'تم حذف الروشته بنجاح');
    }
}