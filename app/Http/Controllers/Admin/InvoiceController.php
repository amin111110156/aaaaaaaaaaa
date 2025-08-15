<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Appointment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['appointment.patient.user', 'appointment.service'])->get();
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $appointments = Appointment::with(['patient.user', 'service'])->whereDoesntHave('invoice')->get();
        return view('admin.invoices.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:paid,unpaid,partial',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $total_amount = $appointment->service->price ?? 0;
        
        Invoice::create([
            'appointment_id' => $request->appointment_id,
            'total_amount' => $total_amount,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    public function edit(Invoice $invoice)
    {
        $appointments = Appointment::with(['patient.user', 'service'])->get();
        return view('admin.invoices.edit', compact('invoice', 'appointments'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:paid,unpaid,partial',
        ]);

        $invoice->update($request->all());

        return redirect()->route('admin.invoices.index')->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'تم حذف الفاتورة بنجاح');
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', compact('invoice'));
    }
}