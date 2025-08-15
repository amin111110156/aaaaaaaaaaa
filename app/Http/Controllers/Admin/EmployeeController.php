<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeLeave;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'birth_date' => 'required|date',
            'address' => 'required|string',
        ]);

        // إنشاء مستخدم للنظام
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // كلمة مرور افتراضية
            'role' => 'receptionist', // يمكن تعديله لاحقاً
        ]);

        // إنشاء موظف
        Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'position' => $request->position,
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'تم إنشاء الموظف بنجاح');
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . $employee->user_id,
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $employee->user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees.index')->with('success', 'تم تحديث الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        $employee->user->delete();
        return redirect()->route('admin.employees.index')->with('success', 'تم حذف الموظف بنجاح');
    }

    // إدارة الحضور والانصراف
    public function attendance(Employee $employee)
    {
        $attendances = EmployeeAttendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(30);
        return view('admin.employees.attendance', compact('employee', 'attendances'));
    }

    public function recordAttendance(Request $request, Employee $employee)
    {
        $request->validate([
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,early_leave',
        ]);

        EmployeeAttendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $request->date],
            [
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $request->status,
            ]
        );

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
    }

    // إدارة الإجازات
    public function leaves(Employee $employee)
    {
        $leaves = EmployeeLeave::where('employee_id', $employee->id)->get();
        return view('admin.employees.leaves', compact('employee', 'leaves'));
    }

    public function storeLeave(Request $request, Employee $employee)
    {
        $request->validate([
            'leave_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        EmployeeLeave::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    public function approveLeave(EmployeeLeave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم الموافقة على الإجازة');
    }

    public function rejectLeave(EmployeeLeave $leave)
    {
        $leave->update(['status' => 'rejected']);
        return back()->with('success', 'تم رفض الإجازة');
    }

    // إدارة الرواتب
    public function salaries(Employee $employee)
    {
        $salaries = EmployeeSalary::where('employee_id', $employee->id)->get();
        return view('admin.employees.salaries', compact('employee', 'salaries'));
    }

    public function storeSalary(Request $request, Employee $employee)
    {
        $request->validate([
            'salary_month' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $netSalary = $request->basic_salary + ($request->allowances ?? 0) - ($request->deductions ?? 0);

        EmployeeSalary::create([
            'employee_id' => $employee->id,
            'salary_month' => $request->salary_month,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances ?? 0,
            'deductions' => $request->deductions ?? 0,
            'net_salary' => $netSalary,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إضافة الراتب بنجاح');
    }

    public function paySalary(EmployeeSalary $salary)
    {
        $salary->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        return back()->with('success', 'تم دفع الراتب بنجاح');
    }
}