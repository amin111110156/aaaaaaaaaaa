<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\AppointmentExport;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportAppointments(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        if ($request->format === 'excel') {
            return Excel::download(
                new AppointmentExport($startDate, $endDate),
                'تقرير_المواعيد_' . now()->format('Y-m-d') . '.xlsx'
            );
        } else {
            $appointments = \App\Models\Appointment::whereBetween('appointment_time', [$startDate, $endDate])
                ->with(['patient.user', 'doctor.user', 'service'])
                ->get();

            $pdf = Pdf::loadView('admin.exports.appointments_pdf', compact('appointments', 'startDate', 'endDate'));
            return $pdf->download('تقرير_المواعيد_' . now()->format('Y-m-d') . '.pdf');
        }
    }

    public function exportFinancial(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        if ($request->format === 'excel') {
            return Excel::download(
                new FinancialReportExport($startDate, $endDate),
                'تقرير_مالي_' . now()->format('Y-m-d') . '.xlsx'
            );
        } else {
            $invoices = \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->with(['appointment.patient.user', 'appointment.service', 'payments'])
                ->get();

            $totalRevenue = $invoices->sum(function($invoice) {
                return $invoice->payments->sum('amount');
            });

            $pdf = Pdf::loadView('admin.exports.financial_pdf', compact('invoices', 'totalRevenue', 'startDate', 'endDate'));
            return $pdf->download('تقرير_مالي_' . now()->format('Y-m-d') . '.pdf');
        }
    }
}