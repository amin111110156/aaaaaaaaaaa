<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AppointmentExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $appointments;

    public function __construct($appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments;
    }

    public function headings(): array
    {
        return [
            'رقم الموعد',
            'اسم المريض',
            'رقم الهاتف',
            'اسم الطبيب',
            'التخصص',
            'اسم الخدمة',
            'سعر الخدمة',
            'تاريخ الموعد',
            'الحالة',
        ];
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->patient->user->name,
            $appointment->patient->phone,
            $appointment->doctor->user->name,
            $appointment->doctor->specialization,
            $appointment->service->name ?? 'بدون خدمة',
            number_format($appointment->service->price ?? 0, 2),
            $appointment->appointment_time->format('Y-m-d H:i'),
            $appointment->status == 'pending' ? 'معلق' : 
            ($appointment->status == 'confirmed' ? 'مؤكد' : 
            ($appointment->status == 'completed' ? 'مكتمل' : 'ملغى')),
        ];
    }

    public function title(): string
    {
        return 'مواعيد العيادة';
    }
}