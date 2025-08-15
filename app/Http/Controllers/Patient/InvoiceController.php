<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;

class InvoiceController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        $invoices = Invoice::whereHas('appointment', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with(['appointment.service', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $patient = auth()->user()->patient;
        if ($invoice->appointment->patient_id != $patient->id) {
            abort(403);
        }
        
        return view('patient.invoices.show', compact('invoice'));
    }

    public function pay(Invoice $invoice)
    {
        $patient = auth()->user()->patient;
        if ($invoice->appointment->patient_id != $patient->id) {
            abort(403);
        }
        
        return view('patient.invoices.pay', compact('invoice'));
    }

    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . ($invoice->total_amount - $invoice->payments()->sum('amount')),
            'method' => 'required|in:cash,card,bank',
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'notes' => 'مدفوع من قبل المريض عبر البوابة',
        ]);

        // تحديث حالة الفاتورة
        $total_paid = $invoice->payments()->sum('amount');
        if ($total_paid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('patient.invoices.show', $invoice)->with('success', 'تم تسجيل الدفع بنجاح');
    }
}