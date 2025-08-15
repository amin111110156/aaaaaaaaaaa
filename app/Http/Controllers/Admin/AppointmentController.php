<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'service'])->get();
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        $services = Service::all();
        return view('admin.appointments.create', compact('patients', 'doctors', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create($request->all());

        return redirect()->route('admin.appointments.index')->with('success', 'تم حجز الموعد بنجاح');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'service']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        $services = Service::all();
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($request->all());

        return redirect()->route('admin.appointments.index')->with('success', 'تم تحديث الموعد بنجاح');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')->with('success', 'تم حذف الموعد بنجاح');
    }

    // ← الطريقة المحدثة لإدارة الحالة
    public function manage(Appointment $appointment)
    {
        return view('admin.appointments.manage', compact('appointment'));
    }

    // ← الطريقة لتحديث الحالة وإنشاء الفاتورة تلقائيًا
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        // تحديث الحالة
        $appointment->update([
            'status' => $request->status,
        ]);

        // إذا كان الموعد قد حضر (completed) - إنشاء الفاتورة والمدفوعات تلقائيًا
        if ($request->status === 'completed') {
            // التحقق من عدم وجود فاتورة مسبقة
            if (!$appointment->invoice) {
                // إنشاء فاتورة تلقائيًا
                $invoice = Invoice::create([
                    'appointment_id' => $appointment->id,
                    'total_amount' => $appointment->service->price ?? 0,
                    'discount' => 0,
                    'tax' => 0,
                    'status' => 'paid', // الفاتورة مدفوعة تلقائيًا
                ]);

                // إنشاء دفعة تلقائيًا
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $appointment->service->price ?? 0,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                ]);
            }
        }

        // إذا تم تأجيل أو إلغاء الموعد - لا تضف ничего
        if (in_array($request->status, ['pending', 'cancelled'])) {
            // لا حاجة لإضافة أي شيء
        }

        return redirect()->route('admin.appointments.index')->with('success', 'تم تحديث حالة الموعد بنجاح');
    }
}