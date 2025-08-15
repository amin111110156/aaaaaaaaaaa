<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pharmacy;
use App\Models\PharmacySale;
use App\Models\PharmacyMedication;
use App\Models\Patient;

class PharmacySaleController extends Controller
{
    public function index(Pharmacy $pharmacy)
    {
        $sales = $pharmacy->sales()->with(['patient.user'])->orderBy('created_at', 'desc')->get();
        return view('admin.pharmacy.sales.index', compact('pharmacy', 'sales'));
    }

    public function create(Pharmacy $pharmacy)
    {
        $patients = Patient::with('user')->get();
        $medications = $pharmacy->medications;
        return view('admin.pharmacy.sales.create', compact('pharmacy', 'patients', 'medications'));
    }

    public function store(Request $request, Pharmacy $pharmacy)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medications' => 'required|array',
            'medications.*.medication_id' => 'required|exists:pharmacy_medications,id',
            'medications.*.quantity' => 'required|integer|min:1',
            'medications.*.unit_price' => 'required|numeric|min:0',
            'medications.*.total_price' => 'required|numeric|min:0',
            'medications.*.instructions' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $totalAmount = collect($request->medications)->sum('total_price');
        $discount = $request->discount ?? 0;
        $tax = $request->tax ?? 0;
        $finalTotal = $totalAmount - $discount + $tax;

        $sale = $pharmacy->sales()->create([
            'patient_id' => $request->patient_id,
            'total_amount' => $finalTotal,
            'discount' => $discount,
            'tax' => $tax,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($request->medications as $item) {
            $sale->items()->create([
                'medication_id' => $item['medication_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'instructions' => $item['instructions'] ?? null,
            ]);
        }

        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم إنشاء عملية البيع بنجاح');
    }

    public function show(Pharmacy $pharmacy, PharmacySale $sale)
    {
        $sale->load(['pharmacy', 'patient.user', 'items.medication']);
        return view('admin.pharmacy.sales.show', compact('pharmacy', 'sale'));
    }

    public function edit(Pharmacy $pharmacy, PharmacySale $sale)
    {
        $patients = Patient::with('user')->get();
        $medications = $pharmacy->medications;
        $sale->load(['items.medication']);
        return view('admin.pharmacy.sales.edit', compact('pharmacy', 'sale', 'patients', 'medications'));
    }

    public function update(Request $request, Pharmacy $pharmacy, PharmacySale $sale)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medications' => 'required|array',
            'medications.*.medication_id' => 'required|exists:pharmacy_medications,id',
            'medications.*.quantity' => 'required|integer|min:1',
            'medications.*.unit_price' => 'required|numeric|min:0',
            'medications.*.total_price' => 'required|numeric|min:0',
            'medications.*.instructions' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid,partial,cancelled',
        ]);

        $totalAmount = collect($request->medications)->sum('total_price');
        $discount = $request->discount ?? 0;
        $tax = $request->tax ?? 0;
        $finalTotal = $totalAmount - $discount + $tax;

        $sale->update([
            'patient_id' => $request->patient_id,
            'total_amount' => $finalTotal,
            'discount' => $discount,
            'tax' => $tax,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // حذف العناصر القديمة
        $sale->items()->delete();

        // إنشاء العناصر الجديدة
        foreach ($request->medications as $item) {
            $sale->items()->create([
                'medication_id' => $item['medication_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'instructions' => $item['instructions'] ?? null,
            ]);
        }

        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم تحديث عملية البيع بنجاح');
    }

    public function destroy(Pharmacy $pharmacy, PharmacySale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم حذف عملية البيع بنجاح');
    }

    public function pay(Request $request, Pharmacy $pharmacy, PharmacySale $sale)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,wallet',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        if ($request->amount_paid >= $sale->total_amount) {
            $sale->update(['status' => 'paid']);
        } elseif ($request->amount_paid > 0) {
            $sale->update(['status' => 'partial']);
        }

        // إنشاء دفعة
        $sale->payments()->create([
            'amount' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'status' => 'completed',
        ]);

        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم دفع عملية البيع بنجاح');
    }
}