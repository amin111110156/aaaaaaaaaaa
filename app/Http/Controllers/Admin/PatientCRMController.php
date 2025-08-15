<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientCRM;
use App\Models\Doctor;

class PatientCRMController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['user', 'crm'])->get();
        return view('admin.patient-crm.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'crm.preferredDoctor.user', 'appointments.doctor.user', 'crm.reminders', 'crm.feedback']);
        return view('admin.patient-crm.show', compact('patient'));
    }

    public function updateProfile(Request $request, Patient $patient)
    {
        $request->validate([
            'preferred_doctor' => 'nullable|exists:doctors,id',
            'preferred_time' => 'nullable|string|max:50',
            'allergies' => 'nullable|array',
            'chronic_conditions' => 'nullable|array',
            'family_history' => 'nullable|array',
            'insurance_info' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        $crmData = $request->only([
            'preferred_doctor',
            'preferred_time',
            'allergies',
            'chronic_conditions',
            'family_history',
            'insurance_info',
            'emergency_contact'
        ]);

        if ($patient->crm) {
            $patient->crm->update($crmData);
        } else {
            $crmData['patient_id'] = $patient->id;
            PatientCRM::create($crmData);
        }

        return back()->with('success', 'تم تحديث ملف المريض بنجاح');
    }

    public function reminders(Patient $patient)
    {
        $reminders = $patient->crm ? $patient->crm->reminders : collect();
        return view('admin.patient-crm.reminders', compact('patient', 'reminders'));
    }

    public function storeReminder(Request $request, Patient $patient)
    {
        $request->validate([
            'reminder_type' => 'required|in:appointment,medication,followup,birthday,general',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_date' => 'required|date',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        if (!$patient->crm) {
            $patient->crm()->create([]);
        }

        $patient->crm->reminders()->create([
            'reminder_type' => $request->reminder_type,
            'title' => $request->title,
            'description' => $request->description,
            'reminder_date' => $request->reminder_date,
            'reminder_time' => $request->reminder_time,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إنشاء التذكير بنجاح');
    }

    public function markReminderAsSent($reminder)
    {
        $reminder = \App\Models\PatientReminder::findOrFail($reminder);
        $reminder->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'تم تحديث حالة التذكير');
    }

    public function feedback(Patient $patient)
    {
        $feedbacks = $patient->crm ? $patient->crm->feedback : collect();
        return view('admin.patient-crm.feedback', compact('patient', 'feedbacks'));
    }

    public function storeFeedback(Request $request, Patient $patient)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ]);

        if (!$patient->crm) {
            $patient->crm()->create([]);
        }

        $patient->crm->feedback()->create([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'category' => $request->category,
            'status' => 'approved',
        ]);

        return back()->with('success', 'تم إضافة التقييم بنجاح');
    }

    public function approveFeedback($feedback)
    {
        $feedback = \App\Models\PatientFeedback::findOrFail($feedback);
        $feedback->update(['status' => 'approved']);
        return back()->with('success', 'تم الموافقة على التقييم');
    }

    public function rejectFeedback($feedback)
    {
        $feedback = \App\Models\PatientFeedback::findOrFail($feedback);
        $feedback->update(['status' => 'rejected']);
        return back()->with('success', 'تم رفض التقييم');
    }

    public function statistics()
    {
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)->count();
        $averageRating = \App\Models\PatientFeedback::avg('rating');
        $totalFeedbacks = \App\Models\PatientFeedback::count();
        $upcomingAppointments = \App\Models\Appointment::where('appointment_time', '>', now())
            ->where('appointment_time', '<', now()->addDays(7))
            ->count();

        return view('admin.patient-crm.statistics', compact(
            'totalPatients',
            'newPatientsThisMonth',
            'averageRating',
            'totalFeedbacks',
            'upcomingAppointments'
        ));
    }

    public function reports()
    {
        $patientsWithHighSpending = PatientCRM::orderBy('total_spent', 'desc')->limit(10)->get();
        $loyalPatients = PatientCRM::orderBy('loyalty_points', 'desc')->limit(10)->get();
        $frequentVisitors = PatientCRM::orderBy('total_visits', 'desc')->limit(10)->get();

        return view('admin.patient-crm.reports', compact(
            'patientsWithHighSpending',
            'loyalPatients',
            'frequentVisitors'
        ));
    }
}