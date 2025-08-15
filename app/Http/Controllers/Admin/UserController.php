<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index()
    {
        $users = User::with(['patient', 'doctor'])->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * عرض نموذج إنشاء مستخدم جديد
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * حفظ المستخدم الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,receptionist,patient',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
        ]);

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // إنشاء سجل إشعارات للمستخدم
        $user->notificationSetting()->create([
            'whatsapp_enabled' => false,
            'telegram_enabled' => false,
            'financial_report_enabled' => false,
            'doctor_report_enabled' => false,
            'patient_reminder_enabled' => false
        ]);

        // إنشاء سجل مرتبط حسب الدور
        switch ($request->role) {
            case 'patient':
                if ($request->phone && $request->dob && $request->gender) {
                    Patient::create([
                        'user_id' => $user->id,
                        'phone' => $request->phone,
                        'dob' => $request->dob,
                        'gender' => $request->gender,
                        'address' => $request->address,
                    ]);
                }
                break;
            
            case 'doctor':
                if ($request->specialization && $request->license_number) {
                    Doctor::create([
                        'user_id' => $user->id,
                        'specialization' => $request->specialization,
                        'license_number' => $request->license_number,
                        'phone' => $request->phone ?? '',
                    ]);
                }
                break;
        }

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * عرض تفاصيل المستخدم
     */
    public function show(User $user)
    {
        $user->load(['patient', 'doctor', 'notificationSetting']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * عرض نموذج تعديل المستخدم
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * تحديث المستخدم
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,doctor,receptionist,patient',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // تحديث بيانات المستخدم الأساسية
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // تحديث بيانات إضافية حسب الدور
        switch ($request->role) {
            case 'patient':
                if ($user->patient) {
                    $user->patient->update([
                        'phone' => $request->phone,
                        'dob' => $request->dob,
                        'gender' => $request->gender,
                        'address' => $request->address,
                    ]);
                } else {
                    if ($request->phone && $request->dob && $request->gender) {
                        Patient::create([
                            'user_id' => $user->id,
                            'phone' => $request->phone,
                            'dob' => $request->dob,
                            'gender' => $request->gender,
                            'address' => $request->address,
                        ]);
                    }
                }
                break;
            
            case 'doctor':
                if ($user->doctor) {
                    $user->doctor->update([
                        'specialization' => $request->specialization,
                        'license_number' => $request->license_number,
                        'phone' => $request->phone ?? '',
                    ]);
                } else {
                    if ($request->specialization && $request->license_number) {
                        Doctor::create([
                            'user_id' => $user->id,
                            'specialization' => $request->specialization,
                            'license_number' => $request->license_number,
                            'phone' => $request->phone ?? '',
                        ]);
                    }
                }
                break;
        }

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * حذف المستخدم
     */
    public function destroy(User $user)
    {
        // حذف السجلات المرتبطة أولاً
        if ($user->patient) {
            $user->patient->delete();
        }
        
        if ($user->doctor) {
            $user->doctor->delete();
        }
        
        if ($user->notificationSetting) {
            $user->notificationSetting->delete();
        }

        // حذف المستخدم
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * تفعيل المستخدم
     */
    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return redirect()->route('admin.users.index')->with('success', 'تم تفعيل المستخدم بنجاح');
    }

    /**
     * تعطيل المستخدم
     */
    public function deactivate(User $user)
    {
        $user->update(['status' => 'inactive']);
        return redirect()->route('admin.users.index')->with('success', 'تم تعطيل المستخدم بنجاح');
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'password123'; // كلمة مرور افتراضية
        $user->update(['password' => Hash::make($newPassword)]);
        
        return redirect()->route('admin.users.index')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. كلمة المرور الجديدة: ' . $newPassword);
    }

    /**
     * بحث عن المستخدمين
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('role', 'LIKE', "%{$query}%")
            ->with(['patient', 'doctor'])
            ->get();

        return response()->json($users);
    }

    /**
     * تصفية المستخدمين حسب الدور
     */
    public function filterByRole($role)
    {
        $users = User::where('role', $role)
            ->with(['patient', 'doctor'])
            ->get();

        return view('admin.users.index', compact('users'));
    }
}