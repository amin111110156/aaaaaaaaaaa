<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice.appointment.patient.user'])->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::with(['appointment.patient.user', 'appointment.service'])->where('status', '!=', 'paid')->get();
        return view('admin.payments.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,bank',
            'notes' => 'nullable|string',
        ]);

        Payment::create($request->all());

        // تحديث حالة الفاتورة
        $invoice = Invoice::findOrFail($request->invoice_id);
        $total_paid = $invoice->payments()->sum('amount');
        
        if ($total_paid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($total_paid > 0) {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('admin.payments.index')->with('success', 'تم تسجيل الدفع بنجاح');
    }

    public function edit(Payment $payment)
    {
        $invoices = Invoice::all();
        return view('admin.payments.edit', compact('payment', 'invoices'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,bank',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('admin.payments.index')->with('success', 'تم تحديث الدفع بنجاح');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')->with('success', 'تم حذف الدفع بنجاح');
    }
}