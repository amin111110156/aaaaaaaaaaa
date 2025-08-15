<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use Carbon\Carbon;

class AdvancedReportController extends Controller
{
    public function patientFlow(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // تحليل تدفق المرضى
        $monthlyPatients = Patient::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // تحليل المواعيد
        $monthlyAppointments = Appointment::whereBetween('appointment_time', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(appointment_time, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.patient_flow', compact(
            'monthlyPatients',
            'monthlyAppointments',
            'startDate',
            'endDate'
        ));
    }

    public function revenueAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

        // تحليل الإيرادات الشهرية
        $monthlyRevenue = \App\Models\Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('month', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.reports.revenue_analysis', compact(
            'monthlyRevenue',
            'startDate',
            'endDate'
        ));
    }

    public function doctorWorkload(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // تحليل حمل عمل الأطباء
        $doctors = \App\Models\Doctor::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_time', [$startDate, $endDate]);
            }])
            ->with(['user'])
            ->orderBy('appointments_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.doctor_workload', compact(
            'doctors',
            'startDate',
            'endDate'
        ));
    }
}