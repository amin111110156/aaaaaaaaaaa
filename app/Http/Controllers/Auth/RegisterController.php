<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:patient,doctor,receptionist,admin',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // إنشاء إعدادات الإشعارات بأمان
        try {
            if (!$user->notificationSetting) {
                $user->notificationSetting()->create([
                    'whatsapp_enabled' => false,
                    'telegram_enabled' => false,
                    'financial_report_enabled' => false,
                    'doctor_report_enabled' => false,
                    'patient_reminder_enabled' => false
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to create notification setting: ' . $e->getMessage());
        }

        // إذا كان مستخدم مريض، أنشئ سجل المريض
        if ($request->role === 'patient' && $request->phone && $request->dob && $request->gender) {
            Patient::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);
        }

        // إذا كان مستخدم طبيب، أنشئ سجل الطبيب
        if ($request->role === 'doctor' && $request->specialization && $request->license_number) {
            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'phone' => $request->phone ?? '',
            ]);
        }

        return redirect()->route('login')->with('success', 'تم إنشاء الحساب بنجاح. يمكنك تسجيل الدخول الآن.');
    }
}