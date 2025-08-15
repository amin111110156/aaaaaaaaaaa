<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_month',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'payment_date',
        'payment_method',
        'status'
    ];

    protected $casts = [
        'salary_month' => 'date',
        'payment_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}