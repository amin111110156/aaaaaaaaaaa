<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * عرض قائمة الأطباء
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * عرض نموذج إنشاء طبيب جديد
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * حفظ الطبيب الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|max:100|unique:doctors,license_number',
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string',
        ]);

        // إنشاء مستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        // إنشاء طبيب
        Doctor::create([
            'user_id' => $user->id,
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'تم إنشاء الطبيب بنجاح');
    }

    /**
     * عرض تفاصيل الطبيب
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'appointments.patient.user', 'appointments.service']);
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * عرض نموذج تعديل الطبيب
     */
    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * تحديث الطبيب
     */
    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user->id,
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|max:100|unique:doctors,license_number,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string',
        ]);

        // تحديث المستخدم
        $doctor->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // تحديث الطبيب
        $doctor->update([
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'تم تحديث الطبيب بنجاح');
    }

    /**
     * حذف الطبيب
     */
    public function destroy(Doctor $doctor)
    {
        // حذف المستخدم المرتبط
        $doctor->user->delete();
        
        // حذف الطبيب
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'تم حذف الطبيب بنجاح');
    }
}