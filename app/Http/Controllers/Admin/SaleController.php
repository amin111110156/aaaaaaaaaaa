<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Pharmacy;
use App\Models\PharmacyMedication;

class SaleController extends Controller
{
    public function index(Pharmacy $pharmacy)
    {
        $sales = $pharmacy->sales()->with(['medication', 'pharmacy'])->get();
        return view('admin.pharmacy.sales.index', compact('pharmacy', 'sales'));
    }

    public function create(Pharmacy $pharmacy)
    {
        $medications = $pharmacy->medications;
        return view('admin.pharmacy.sales.create', compact('pharmacy', 'medications'));
    }

    public function store(Request $request, Pharmacy $pharmacy)
    {
        $request->validate([
            'medication_id' => 'required|exists:pharmacy_medications,id',
            'quantity' => 'required|integer|min:1',
            'sale_price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $medication = PharmacyMedication::findOrFail($request->medication_id);

        if ($medication->stock_quantity < $request->quantity) {
            return back()->with('error', 'الكمية المطلوبة أكبر من الكمية المتوفرة في المخزون');
        }

        $sale = $pharmacy->sales()->create([
            'medication_id' => $request->medication_id,
            'quantity' => $request->quantity,
            'sale_price' => $request->sale_price,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'total_amount' => $request->quantity * $request->sale_price,
            'status' => 'completed',
        ]);

        // تحديث الكمية في المخزون
        $medication->decrement('stock_quantity', $request->quantity);

        return back()->with('success', 'تم بيع الدواء بنجاح');
    }

    public function show(Pharmacy $pharmacy, Sale $sale)
    {
        $sale->load(['medication', 'pharmacy']);
        return view('admin.pharmacy.sales.show', compact('pharmacy', 'sale'));
    }

    public function edit(Pharmacy $pharmacy, Sale $sale)
    {
        $medications = $pharmacy->medications;
        return view('admin.pharmacy.sales.edit', compact('pharmacy', 'sale', 'medications'));
    }

    public function update(Request $request, Pharmacy $pharmacy, Sale $sale)
    {
        $request->validate([
            'medication_id' => 'required|exists:pharmacy_medications,id',
            'quantity' => 'required|integer|min:1',
            'sale_price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $sale->update($request->all());

        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم تحديث عملية البيع بنجاح');
    }

    public function destroy(Pharmacy $pharmacy, Sale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم حذف عملية البيع بنجاح');
    }
}