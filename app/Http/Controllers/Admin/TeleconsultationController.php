<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teleconsultation;
use App\Models\Patient;
use App\Models\Doctor;

class TeleconsultationController extends Controller
{
    public function index()
    {
        $teleconsultations = Teleconsultation::with(['patient.user', 'doctor.user'])->get();
        return view('admin.teleconsultations.index', compact('teleconsultations'));
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.teleconsultations.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $meetingLink = 'https://meet.jit.si/clinic-' . uniqid();

        Teleconsultation::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'meeting_link' => $meetingLink,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.teleconsultations.index')->with('success', 'تم جدولة الاستشارة عن بُعد بنجاح');
    }

    public function show(Teleconsultation $teleconsultation)
    {
        $teleconsultation->load(['patient.user', 'doctor.user']);
        return view('admin.teleconsultations.show', compact('teleconsultation'));
    }

    public function edit(Teleconsultation $teleconsultation)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.teleconsultations.edit', compact('teleconsultation', 'patients', 'doctors'));
    }

    public function update(Request $request, Teleconsultation $teleconsultation)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $teleconsultation->update($request->all());

        return redirect()->route('admin.teleconsultations.index')->with('success', 'تم تحديث الاستشارة بنجاح');
    }

    public function destroy(Teleconsultation $teleconsultation)
    {
        $teleconsultation->delete();
        return redirect()->route('admin.teleconsultations.index')->with('success', 'تم حذف الاستشارة بنجاح');
    }

    public function startConsultation(Teleconsultation $teleconsultation)
    {
        if ($teleconsultation->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن بدء هذه الاستشارة');
        }

        $teleconsultation->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        return redirect()->route('admin.teleconsultations.show', $teleconsultation)->with('success', 'تم بدء الاستشارة');
    }

    public function endConsultation(Teleconsultation $teleconsultation)
    {
        if ($teleconsultation->status !== 'in_progress') {
            return back()->with('error', 'لا يمكن إنهاء هذه الاستشارة');
        }

        $duration = $teleconsultation->started_at ? now()->diffInMinutes($teleconsultation->started_at) : 0;

        $teleconsultation->update([
            'status' => 'completed',
            'ended_at' => now(),
            'duration' => $duration
        ]);

        return redirect()->route('admin.teleconsultations.index')->with('success', 'تم إنهاء الاستشارة');
    }
}