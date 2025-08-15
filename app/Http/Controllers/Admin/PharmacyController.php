<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pharmacy;
use App\Models\PharmacyMedication;
use App\Models\Sale;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;

class PharmacyController extends Controller
{
    // ================== الصيدليات ==================
    public function pharmacies()
    {
        $pharmacies = Pharmacy::all();
        return view('admin.pharmacy.pharmacies.index', compact('pharmacies'));
    }

    public function createPharmacy()
    {
        return view('admin.pharmacy.pharmacies.create');
    }

    public function storePharmacy(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:100',
            'manager_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Pharmacy::create($request->only([
            'name', 'address', 'phone', 'license_number', 'manager_name', 'status'
        ]));

        return redirect()->route('admin.pharmacy.pharmacies')->with('success', 'تم إنشاء الصيدلية بنجاح');
    }

    public function editPharmacy(Pharmacy $pharmacy)
    {
        return view('admin.pharmacy.pharmacies.edit', compact('pharmacy'));
    }

    public function updatePharmacy(Request $request, Pharmacy $pharmacy)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:100',
            'manager_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $pharmacy->update($request->only([
            'name', 'address', 'phone', 'license_number', 'manager_name', 'status'
        ]));

        return redirect()->route('admin.pharmacy.pharmacies')->with('success', 'تم تحديث الصيدلية بنجاح');
    }

    public function destroyPharmacy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();
        return redirect()->route('admin.pharmacy.pharmacies')->with('success', 'تم حذف الصيدلية بنجاح');
    }

    public function showPharmacy(Pharmacy $pharmacy)
    {
        $pharmacy->load(['medications', 'sales', 'user']);
        return view('admin.pharmacy.pharmacies.show', compact('pharmacy'));
    }

    // ================== الأدوية ==================
    public function medications(Pharmacy $pharmacy)
    {
        $medications = $pharmacy->medications;
        return view('admin.pharmacy.medications.index', compact('pharmacy', 'medications'));
    }

    public function showMedication(Pharmacy $pharmacy, PharmacyMedication $medication)
    {
        return view('admin.pharmacy.medications.show', compact('pharmacy', 'medication'));
    }

    public function createMedication(Pharmacy $pharmacy)
    {
        return view('admin.pharmacy.medications.create', compact('pharmacy'));
    }

    public function storeMedication(Request $request, Pharmacy $pharmacy)
    {
        $request->validate([
            'medication_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'required|string|max:100',
        ]);

        $pharmacy->medications()->create($request->only([
            'medication_name', 'description', 'price', 'stock_quantity', 'expiry_date', 'batch_number'
        ]));

        return redirect()->route('admin.pharmacy.medications', $pharmacy)->with('success', 'تم إضافة الدواء بنجاح');
    }

    public function editMedication(Pharmacy $pharmacy, PharmacyMedication $medication)
    {
        return view('admin.pharmacy.medications.edit', compact('pharmacy', 'medication'));
    }

    public function updateMedication(Request $request, Pharmacy $pharmacy, PharmacyMedication $medication)
    {
        $request->validate([
            'medication_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'required|string|max:100',
        ]);

        $medication->update($request->only([
            'medication_name', 'description', 'price', 'stock_quantity', 'expiry_date', 'batch_number'
        ]));

        return redirect()->route('admin.pharmacy.medications', $pharmacy)->with('success', 'تم تحديث الدواء بنجاح');
    }

    public function destroyMedication(Pharmacy $pharmacy, PharmacyMedication $medication)
    {
        $medication->delete();
        return redirect()->route('admin.pharmacy.medications', $pharmacy)->with('success', 'تم حذف الدواء بنجاح');
    }

    // ================== المبيعات ==================
    public function sales(Pharmacy $pharmacy)
    {
        $sales = $pharmacy->sales;
        return view('admin.pharmacy.sales.index', compact('pharmacy', 'sales'));
    }

    public function createSale(Pharmacy $pharmacy)
    {
        $medications = $pharmacy->medications;
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();

        return view('admin.pharmacy.sales.create', compact('pharmacy', 'medications', 'patients', 'doctors'));
    }

    public function storeSale(Request $request, Pharmacy $pharmacy)
    {
        $request->validate([
            'medication_id' => 'required|exists:pharmacy_medications,id',
            'quantity' => 'required|integer|min:1',
            'sale_price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'patient_id' => 'nullable|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $medication = PharmacyMedication::findOrFail($request->medication_id);

        if ($medication->stock_quantity < $request->quantity) {
            return back()->with('error', 'الكمية المطلوبة أكبر من الكمية المتوفرة في المخزون');
        }

        $pharmacy->sales()->create([
            'medication_id' => $request->medication_id,
            'quantity' => $request->quantity,
            'sale_price' => $request->sale_price,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'total_amount' => $request->quantity * $request->sale_price,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
        ]);

        $medication->decrement('stock_quantity', $request->quantity);

        return redirect()->route('admin.pharmacy.sales', $pharmacy)->with('success', 'تم بيع الدواء بنجاح');
    }
}
