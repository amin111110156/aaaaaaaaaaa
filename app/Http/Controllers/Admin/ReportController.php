<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Appointment;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payments = \App\Models\Payment::whereBetween('created_at', [$startDate, $endDate])->get();
        $invoices = \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalRevenue = $payments->sum('amount');
        $totalInvoices = $invoices->sum('total_amount');
        $totalDiscounts = $invoices->sum('discount');
        $totalTaxes = $invoices->sum('tax');

        return view('admin.reports.financial', compact(
            'payments',
            'invoices',
            'totalRevenue',
            'totalInvoices',
            'totalDiscounts',
            'totalTaxes',
            'startDate',
            'endDate'
        ));
    }

    public function doctorPerformance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $doctors = \App\Models\Doctor::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_time', [$startDate, $endDate]);
            }])
            ->with(['user'])
            ->orderBy('appointments_count', 'desc')
            ->get();

        return view('admin.reports.doctor_performance', compact('doctors', 'startDate', 'endDate'));
    }

    // ← الطريقة المفقودة
    public function serviceReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $services = Service::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_time', [$startDate, $endDate]);
            }])
            ->withSum(['appointments as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->whereHas('invoice.payments', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.service_report', compact('services', 'startDate', 'endDate'));
    }
}