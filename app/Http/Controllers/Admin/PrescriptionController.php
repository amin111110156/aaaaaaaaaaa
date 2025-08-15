<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        // Normalize incoming medications to always have 'id'
        $data = $request->all();
        if (isset($data['medications']) && is_array($data['medications'])) {
            foreach ($data['medications'] as $k => $m) {
                if (!isset($m['id'])) {
                    if (isset($m['medication_id'])) {
                        $data['medications'][$k]['id'] = $m['medication_id'];
                    } elseif (isset($m['name'])) {
                        $found = PharmacyMedication::where('medication_name', $m['name'])->first();
                        if ($found) {
                            $data['medications'][$k]['id'] = $found->id;
                        }
                    }
                }
            }
        }

        $validator = Validator::make($data, [
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string',
            'followup_date' => 'nullable|date|after:today',
            'followup_reason' => 'nullable|string|max:255',
            'medications' => 'required|array|min:1',
            'medications.*.id' => 'required|exists:pharmacy_medications,id',
            'medications.*.dosage' => 'required|string|max:100',
            'medications.*.frequency' => 'required|string|max:100',
            'medications.*.duration' => 'required|integer|min:1',
            'medications.*.instructions' => 'nullable|string',
        ], [
            'medications.*.id.required' => 'يرجى اختيار الدواء.',
        ]);

        $validated = $validator->validate();

        $prescription = Prescription::create([
            'appointment_id' => $validated['appointment_id'],
            'notes' => $validated['notes'] ?? null,
            'followup_date' => $validated['followup_date'] ?? null,
            'followup_reason' => $validated['followup_reason'] ?? null,
        ]);

        foreach ($validated['medications'] as $medication) {
            $medicationModel = PharmacyMedication::find($medication['id']);
            $prescription->medications()->create([
                'medication_name' => $medicationModel ? $medicationModel->medication_name : null,
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

        $prescription->update($request->only([
            'appointment_id', 'notes', 'followup_date', 'followup_reason'
        ]));

        return redirect()->route('admin.prescriptions.index')->with('success', 'تم تحديث الروشته بنجاح');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('admin.prescriptions.index')->with('success', 'تم حذف الروشته بنجاح');
    }
}
