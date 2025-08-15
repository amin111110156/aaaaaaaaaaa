<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('user')->get();
        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
        ]);

        // إنشاء مستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'patient',
        ]);

        // إنشاء مريض
        Patient::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'تم إنشاء المريض بنجاح');
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.doctor.user', 'appointments.service']);
        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->user->id,
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
        ]);

        $patient->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $patient->update($request->all());

        return redirect()->route('admin.patients.index')->with('success', 'تم تحديث المريض بنجاح');
    }

    public function destroy(Patient $patient)
    {
        $patient->user->delete();
        return redirect()->route('admin.patients.index')->with('success', 'تم حذف المريض بنجاح');
    }
}