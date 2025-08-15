<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AdvancedReportController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\LabTestController;
use App\Http\Controllers\Admin\TeleconsultationController;
use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PatientCRMController;

// الصفحة الرئيسية
Route::get('/', function () {
    if (auth()->check()) {
        switch (auth()->user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'doctor':
                return redirect()->route('doctor.dashboard');
            case 'receptionist':
                return redirect()->route('receptionist.dashboard');
            case 'patient':
                return redirect()->route('patient.dashboard');
            default:
                return redirect()->route('login');
        }
    } else {
        return redirect()->route('login');
    }
})->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Users Management
    Route::resource('admin/users', UserController::class);

    // Patients Management
    Route::resource('admin/patients', PatientController::class);

    // Doctors Management
    Route::resource('admin/doctors', DoctorController::class);

    // Services Management
    Route::resource('admin/services', ServiceController::class);

    // Appointments Management
    Route::resource('admin/appointments', AppointmentController::class);

    // Prescriptions Management
    Route::resource('admin/prescriptions', PrescriptionController::class);

    // Invoices Management
    Route::resource('admin/invoices', InvoiceController::class);

    // Payments Management
    Route::resource('admin/payments', PaymentController::class);

    // Lab Tests Management
    Route::resource('admin/lab-tests', LabTestController::class);
    Route::post('/admin/lab-tests/{labTest}/upload-result', [LabTestController::class, 'uploadResult'])->name('admin.lab-tests.uploadResult');

    // Teleconsultations Management
    Route::resource('admin/teleconsultations', TeleconsultationController::class);
    Route::post('/admin/teleconsultations/{teleconsultation}/start', [TeleconsultationController::class, 'startConsultation'])->name('admin.teleconsultations.start');
    Route::post('/admin/teleconsultations/{teleconsultation}/end', [TeleconsultationController::class, 'endConsultation'])->name('admin.teleconsultations.end');

    // Pharmacy Management
    Route::get('/admin/pharmacy/pharmacies', [PharmacyController::class, 'pharmacies'])->name('admin.pharmacy.pharmacies');
    Route::get('/admin/pharmacy/pharmacies/create', [PharmacyController::class, 'createPharmacy'])->name('admin.pharmacy.pharmacies.create');
    Route::post('/admin/pharmacy/pharmacies', [PharmacyController::class, 'storePharmacy'])->name('admin.pharmacy.pharmacies.store');
    Route::get('/admin/pharmacy/pharmacies/{pharmacy}/edit', [PharmacyController::class, 'editPharmacy'])->name('admin.pharmacy.pharmacies.edit');
    Route::put('/admin/pharmacy/pharmacies/{pharmacy}', [PharmacyController::class, 'updatePharmacy'])->name('admin.pharmacy.pharmacies.update');
    Route::delete('/admin/pharmacy/pharmacies/{pharmacy}', [PharmacyController::class, 'destroyPharmacy'])->name('admin.pharmacy.pharmacies.destroy');

    Route::get('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'medications'])->name('admin.pharmacy.medications');
    Route::get('/admin/pharmacy/{pharmacy}/medications/create', [PharmacyController::class, 'createMedication'])->name('admin.pharmacy.medications.create');
    Route::post('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'storeMedication'])->name('admin.pharmacy.medications.store');

    Route::get('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'sales'])->name('admin.pharmacy.sales');
    Route::get('/admin/pharmacy/{pharmacy}/sales/create', [PharmacyController::class, 'createSale'])->name('admin.pharmacy.sales.create');
    Route::post('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'storeSale'])->name('admin.pharmacy.sales.store');

    // Employees Management
    Route::resource('admin/employees', EmployeeController::class);
    Route::get('/admin/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('admin.employees.attendance');
    Route::post('/admin/employees/{employee}/attendance', [EmployeeController::class, 'recordAttendance'])->name('admin.employees.recordAttendance');
    Route::get('/admin/employees/{employee}/leaves', [EmployeeController::class, 'leaves'])->name('admin.employees.leaves');
    Route::post('/admin/employees/{employee}/leaves', [EmployeeController::class, 'storeLeave'])->name('admin.employees.storeLeave');
    Route::post('/admin/employees/leaves/{leave}/approve', [EmployeeController::class, 'approveLeave'])->name('admin.employees.approveLeave');
    Route::post('/admin/employees/leaves/{leave}/reject', [EmployeeController::class, 'rejectLeave'])->name('admin.employees.rejectLeave');
    Route::get('/admin/employees/{employee}/salaries', [EmployeeController::class, 'salaries'])->name('admin.employees.salaries');
    Route::post('/admin/employees/{employee}/salaries', [EmployeeController::class, 'storeSalary'])->name('admin.employees.storeSalary');
    Route::post('/admin/employees/salaries/{salary}/pay', [EmployeeController::class, 'paySalary'])->name('admin.employees.paySalary');

    // Patient CRM Management
    Route::get('/admin/patient-crm', [PatientCRMController::class, 'index'])->name('admin.patient-crm.index');
    Route::get('/admin/patient-crm/{patient}', [PatientCRMController::class, 'show'])->name('admin.patient-crm.show');
    Route::post('/admin/patient-crm/{patient}/update-profile', [PatientCRMController::class, 'updateProfile'])->name('admin.patient-crm.updateProfile');
    Route::get('/admin/patient-crm/{patient}/reminders', [PatientCRMController::class, 'reminders'])->name('admin.patient-crm.reminders');
    Route::post('/admin/patient-crm/{patient}/reminders', [PatientCRMController::class, 'storeReminder'])->name('admin.patient-crm.storeReminder');
    Route::post('/admin/patient-crm/reminders/{reminder}/sent', [PatientCRMController::class, 'markReminderAsSent'])->name('admin.patient-crm.markReminderAsSent');
    Route::get('/admin/patient-crm/{patient}/feedback', [PatientCRMController::class, 'feedback'])->name('admin.patient-crm.feedback');
    Route::post('/admin/patient-crm/{patient}/feedback', [PatientCRMController::class, 'storeFeedback'])->name('admin.patient-crm.storeFeedback');
    Route::post('/admin/patient-crm/feedback/{feedback}/approve', [PatientCRMController::class, 'approveFeedback'])->name('admin.patient-crm.approveFeedback');
    Route::post('/admin/patient-crm/feedback/{feedback}/reject', [PatientCRMController::class, 'rejectFeedback'])->name('admin.patient-crm.rejectFeedback');
    Route::get('/admin/patient-crm/statistics', [PatientCRMController::class, 'statistics'])->name('admin.patient-crm.statistics');
    Route::get('/admin/patient-crm/reports', [PatientCRMController::class, 'reports'])->name('admin.patient-crm.reports');

    // Reports Management
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/financial', [ReportController::class, 'financial'])->name('admin.reports.financial');
    Route::get('/admin/reports/doctor-performance', [ReportController::class, 'doctorPerformance'])->name('admin.reports.doctorPerformance');
    Route::get('/admin/reports/service-report', [ReportController::class, 'serviceReport'])->name('admin.reports.serviceReport');
    
    // Advanced Reports Management
    Route::get('/admin/reports/patient-flow', [AdvancedReportController::class, 'patientFlow'])->name('admin.reports.patientFlow');
    Route::get('/admin/reports/revenue-analysis', [AdvancedReportController::class, 'revenueAnalysis'])->name('admin.reports.revenueAnalysis');
    Route::get('/admin/reports/doctor-workload', [AdvancedReportController::class, 'doctorWorkload'])->name('admin.reports.doctorWorkload');

    // Exports Management
    Route::post('/admin/export/appointments', [ExportController::class, 'exportAppointments'])->name('admin.export.appointments');
    Route::post('/admin/export/financial', [ExportController::class, 'exportFinancial'])->name('admin.export.financial');

    // Notifications Management
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.readAll');
    Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Notification Settings Management
    Route::get('/admin/notifications/settings', [NotificationSettingController::class, 'index'])->name('admin.notifications.settings');
    Route::put('/admin/notifications/settings', [NotificationSettingController::class, 'update'])->name('admin.notifications.settings.update');
});

// Doctor Routes
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->name('doctor.dashboard');
    
    // Doctor Appointments
    Route::resource('doctor/appointments', Doctor\AppointmentController::class);
    
    // Doctor Patients
    Route::resource('doctor/patients', Doctor\PatientController::class);
    
    // Doctor Prescriptions
    Route::resource('doctor/prescriptions', Doctor\PrescriptionController::class);
    
    // Doctor Lab Tests
    Route::resource('doctor/lab-tests', Doctor\LabTestController::class);
    Route::post('/doctor/lab-tests/{labTest}/upload-result', [Doctor\LabTestController::class, 'uploadResult'])->name('doctor.lab-tests.uploadResult');
    
    // Doctor Teleconsultations
    Route::resource('doctor/teleconsultations', Doctor\TeleconsultationController::class);
    Route::post('/doctor/teleconsultations/{teleconsultation}/start', [Doctor\TeleconsultationController::class, 'startConsultation'])->name('doctor.teleconsultations.start');
    Route::post('/doctor/teleconsultations/{teleconsultation}/end', [Doctor\TeleconsultationController::class, 'endConsultation'])->name('doctor.teleconsultations.end');
    
    // Doctor Pharmacy
    Route::get('/doctor/pharmacy/pharmacies', [Doctor\PharmacyController::class, 'pharmacies'])->name('doctor.pharmacy.pharmacies');
    Route::get('/doctor/pharmacy/pharmacies/create', [Doctor\PharmacyController::class, 'createPharmacy'])->name('doctor.pharmacy.pharmacies.create');
    Route::post('/doctor/pharmacy/pharmacies', [Doctor\PharmacyController::class, 'storePharmacy'])->name('doctor.pharmacy.pharmacies.store');
    Route::get('/doctor/pharmacy/pharmacies/{pharmacy}/edit', [Doctor\PharmacyController::class, 'editPharmacy'])->name('doctor.pharmacy.pharmacies.edit');
    Route::put('/doctor/pharmacy/pharmacies/{pharmacy}', [Doctor\PharmacyController::class, 'updatePharmacy'])->name('doctor.pharmacy.pharmacies.update');
    Route::delete('/doctor/pharmacy/pharmacies/{pharmacy}', [Doctor\PharmacyController::class, 'destroyPharmacy'])->name('doctor.pharmacy.pharmacies.destroy');

    Route::get('/doctor/pharmacy/{pharmacy}/medications', [Doctor\PharmacyController::class, 'medications'])->name('doctor.pharmacy.medications');
    Route::get('/doctor/pharmacy/{pharmacy}/medications/create', [Doctor\PharmacyController::class, 'createMedication'])->name('doctor.pharmacy.medications.create');
    Route::post('/doctor/pharmacy/{pharmacy}/medications', [Doctor\PharmacyController::class, 'storeMedication'])->name('doctor.pharmacy.medications.store');

    Route::get('/doctor/pharmacy/{pharmacy}/sales', [Doctor\PharmacyController::class, 'sales'])->name('doctor.pharmacy.sales');
    Route::get('/doctor/pharmacy/{pharmacy}/sales/create', [Doctor\PharmacyController::class, 'createSale'])->name('doctor.pharmacy.sales.create');
    Route::post('/doctor/pharmacy/{pharmacy}/sales', [Doctor\PharmacyController::class, 'storeSale'])->name('doctor.pharmacy.sales.store');

    // Doctor Employees
    Route::resource('doctor/employees', Doctor\EmployeeController::class);
    Route::get('/doctor/employees/{employee}/attendance', [Doctor\EmployeeController::class, 'attendance'])->name('doctor.employees.attendance');
    Route::post('/doctor/employees/{employee}/attendance', [Doctor\EmployeeController::class, 'recordAttendance'])->name('doctor.employees.recordAttendance');
    Route::get('/doctor/employees/{employee}/leaves', [Doctor\EmployeeController::class, 'leaves'])->name('doctor.employees.leaves');
    Route::post('/doctor/employees/{employee}/leaves', [Doctor\EmployeeController::class, 'storeLeave'])->name('doctor.employees.storeLeave');
    Route::post('/doctor/employees/leaves/{leave}/approve', [Doctor\EmployeeController::class, 'approveLeave'])->name('doctor.employees.approveLeave');
    Route::post('/doctor/employees/leaves/{leave}/reject', [Doctor\EmployeeController::class, 'rejectLeave'])->name('doctor.employees.rejectLeave');
    Route::get('/doctor/employees/{employee}/salaries', [Doctor\EmployeeController::class, 'salaries'])->name('doctor.employees.salaries');
    Route::post('/doctor/employees/{employee}/salaries', [Doctor\EmployeeController::class, 'storeSalary'])->name('doctor.employees.storeSalary');
    Route::post('/doctor/employees/salaries/{salary}/pay', [Doctor\EmployeeController::class, 'paySalary'])->name('doctor.employees.paySalary');

    // Doctor Patient CRM
    Route::get('/doctor/patient-crm', [Doctor\PatientCRMController::class, 'index'])->name('doctor.patient-crm.index');
    Route::get('/doctor/patient-crm/{patient}', [Doctor\PatientCRMController::class, 'show'])->name('doctor.patient-crm.show');
    Route::post('/doctor/patient-crm/{patient}/update-profile', [Doctor\PatientCRMController::class, 'updateProfile'])->name('doctor.patient-crm.updateProfile');
    Route::get('/doctor/patient-crm/{patient}/reminders', [Doctor\PatientCRMController::class, 'reminders'])->name('doctor.patient-crm.reminders');
    Route::post('/doctor/patient-crm/{patient}/reminders', [Doctor\PatientCRMController::class, 'storeReminder'])->name('doctor.patient-crm.storeReminder');
    Route::post('/doctor/patient-crm/reminders/{reminder}/sent', [Doctor\PatientCRMController::class, 'markReminderAsSent'])->name('doctor.patient-crm.markReminderAsSent');
    Route::get('/doctor/patient-crm/{patient}/feedback', [Doctor\PatientCRMController::class, 'feedback'])->name('doctor.patient-crm.feedback');
    Route::post('/doctor/patient-crm/{patient}/feedback', [Doctor\PatientCRMController::class, 'storeFeedback'])->name('doctor.patient-crm.storeFeedback');
    Route::post('/doctor/patient-crm/feedback/{feedback}/approve', [Doctor\PatientCRMController::class, 'approveFeedback'])->name('doctor.patient-crm.approveFeedback');
    Route::post('/doctor/patient-crm/feedback/{feedback}/reject', [Doctor\PatientCRMController::class, 'rejectFeedback'])->name('doctor.patient-crm.rejectFeedback');
    Route::get('/doctor/patient-crm/statistics', [Doctor\PatientCRMController::class, 'statistics'])->name('doctor.patient-crm.statistics');
    Route::get('/doctor/patient-crm/reports', [Doctor\PatientCRMController::class, 'reports'])->name('doctor.patient-crm.reports');

    // Doctor Reports
    Route::get('/doctor/reports', [Doctor\ReportController::class, 'index'])->name('doctor.reports.index');
    Route::get('/doctor/reports/financial', [Doctor\ReportController::class, 'financial'])->name('doctor.reports.financial');
    Route::get('/doctor/reports/doctor-performance', [Doctor\ReportController::class, 'doctorPerformance'])->name('doctor.reports.doctorPerformance');
    Route::get('/doctor/reports/service-report', [Doctor\ReportController::class, 'serviceReport'])->name('doctor.reports.serviceReport');
    
    // Doctor Advanced Reports
    Route::get('/doctor/reports/patient-flow', [Doctor\AdvancedReportController::class, 'patientFlow'])->name('doctor.reports.patientFlow');
    Route::get('/doctor/reports/revenue-analysis', [Doctor\AdvancedReportController::class, 'revenueAnalysis'])->name('doctor.reports.revenueAnalysis');
    Route::get('/doctor/reports/doctor-workload', [Doctor\AdvancedReportController::class, 'doctorWorkload'])->name('doctor.reports.doctorWorkload');

    // Doctor Exports
    Route::post('/doctor/export/appointments', [Doctor\ExportController::class, 'exportAppointments'])->name('doctor.export.appointments');
    Route::post('/doctor/export/financial', [Doctor\ExportController::class, 'exportFinancial'])->name('doctor.export.financial');

    // Doctor Notifications
    Route::get('/doctor/notifications', [Doctor\NotificationController::class, 'index'])->name('doctor.notifications.index');
    Route::post('/doctor/notifications/{id}/read', [Doctor\NotificationController::class, 'markAsRead'])->name('doctor.notifications.read');
    Route::post('/doctor/notifications/read-all', [Doctor\NotificationController::class, 'markAllAsRead'])->name('doctor.notifications.readAll');
    Route::delete('/doctor/notifications/{id}', [Doctor\NotificationController::class, 'destroy'])->name('doctor.notifications.destroy');

    // Doctor Notification Settings
    Route::get('/doctor/notifications/settings', [Doctor\NotificationSettingController::class, 'index'])->name('doctor.notifications.settings');
    Route::put('/doctor/notifications/settings', [Doctor\NotificationSettingController::class, 'update'])->name('doctor.notifications.settings.update');
});

// Receptionist Routes
Route::middleware(['auth', 'role:receptionist'])->group(function () {
    Route::get('/receptionist/dashboard', function () {
        return view('receptionist.dashboard');
    })->name('receptionist.dashboard');
    
    // Receptionist Appointments
    Route::resource('receptionist/appointments', Receptionist\AppointmentController::class);
    
    // Receptionist Patients
    Route::resource('receptionist/patients', Receptionist\PatientController::class);
    
    // Receptionist Doctors
    Route::resource('receptionist/doctors', Receptionist\DoctorController::class);
    
    // Receptionist Services
    Route::resource('receptionist/services', Receptionist\ServiceController::class);
    
    // Receptionist Prescriptions
    Route::resource('receptionist/prescriptions', Receptionist\PrescriptionController::class);
    
    // Receptionist Invoices
    Route::resource('receptionist/invoices', Receptionist\InvoiceController::class);
    
    // Receptionist Payments
    Route::resource('receptionist/payments', Receptionist\PaymentController::class);
    
    // Receptionist Lab Tests
    Route::resource('receptionist/lab-tests', Receptionist\LabTestController::class);
    Route::post('/receptionist/lab-tests/{labTest}/upload-result', [Receptionist\LabTestController::class, 'uploadResult'])->name('receptionist.lab-tests.uploadResult');
    
    // Receptionist Teleconsultations
    Route::resource('receptionist/teleconsultations', Receptionist\TeleconsultationController::class);
    Route::post('/receptionist/teleconsultations/{teleconsultation}/start', [Receptionist\TeleconsultationController::class, 'startConsultation'])->name('receptionist.teleconsultations.start');
    Route::post('/receptionist/teleconsultations/{teleconsultation}/end', [Receptionist\TeleconsultationController::class, 'endConsultation'])->name('receptionist.teleconsultations.end');
});

// Patient Routes
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', function () {
        return view('patient.dashboard');
    })->name('patient.dashboard');
    
    // Patient Appointments
    Route::resource('patient/appointments', Patient\AppointmentController::class);
    
    // Patient Prescriptions
    Route::resource('patient/prescriptions', Patient\PrescriptionController::class);
    
    // Patient Invoices
    Route::resource('patient/invoices', Patient\InvoiceController::class);
    
    // Patient Payments
    Route::resource('patient/payments', Patient\PaymentController::class);
    
    // Patient Lab Tests
    Route::resource('patient/lab-tests', Patient\LabTestController::class);
    
    // Patient Teleconsultations
    Route::resource('patient/teleconsultations', Patient\TeleconsultationController::class);
});

// Global Notification Routes (for all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    Route::get('/notifications/settings', [NotificationSettingController::class, 'index'])->name('notifications.settings');
    Route::put('/notifications/settings', [NotificationSettingController::class, 'update'])->name('notifications.settings.update');
});