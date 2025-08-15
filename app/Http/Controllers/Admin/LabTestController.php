<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabTest;
use App\Models\Appointment;

class LabTestController extends Controller
{
    public function index()
    {
        $labTests = LabTest::with(['appointment.patient.user', 'appointment.doctor.user', 'appointment.service'])->get();
        return view('admin.lab-tests.index', compact('labTests'));
    }

    public function create()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])->get();
        return view('admin.lab-tests.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'test_type' => 'required|in:lab,xray,mri,ct_scan,ultrasound',
            'test_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        LabTest::create([
            'appointment_id' => $request->appointment_id,
            'test_type' => $request->test_type,
            'test_name' => $request->test_name,
            'description' => $request->description,
            'status' => 'requested',
            'requested_by' => auth()->id(),
            'requested_at' => now(),
        ]);

        return redirect()->route('admin.lab-tests.index')->with('success', 'تم إنشاء طلب التحليل بنجاح');
    }

    public function show(LabTest $labTest)
    {
        $labTest->load(['appointment.patient.user', 'appointment.doctor.user', 'appointment.service']);
        return view('admin.lab-tests.show', compact('labTest'));
    }

    public function edit(LabTest $labTest)
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])->get();
        return view('admin.lab-tests.edit', compact('labTest', 'appointments'));
    }

    public function update(Request $request, LabTest $labTest)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'test_type' => 'required|in:lab,xray,mri,ct_scan,ultrasound',
            'test_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:requested,in_progress,completed,cancelled',
        ]);

        $labTest->update($request->all());

        return redirect()->route('admin.lab-tests.index')->with('success', 'تم تحديث طلب التحليل بنجاح');
    }

    public function destroy(LabTest $labTest)
    {
        $labTest->delete();
        return redirect()->route('admin.lab-tests.index')->with('success', 'تم حذف طلب التحليل بنجاح');
    }

    public function uploadResult(Request $request, LabTest $labTest)
    {
        $request->validate([
            'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'result' => 'nullable|string',
        ]);

        if ($request->hasFile('result_file')) {
            $filePath = $request->file('result_file')->store('lab-results', 'public');
            $labTest->update(['result_file' => $filePath]);
        }

        if ($request->result) {
            $labTest->update(['result' => $request->result]);
        }

        if ($labTest->status !== 'completed') {
            $labTest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'performed_by' => auth()->id()
            ]);
        }

        return back()->with('success', 'تم رفع النتيجة بنجاح');
    }
}