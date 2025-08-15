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
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Patients
    Route::get('/admin/patients', [PatientController::class, 'index'])->name('admin.patients.index');
    Route::get('/admin/patients/create', [PatientController::class, 'create'])->name('admin.patients.create');
    Route::post('/admin/patients', [PatientController::class, 'store'])->name('admin.patients.store');
    Route::get('/admin/patients/{patient}', [PatientController::class, 'show'])->name('admin.patients.show');
    Route::get('/admin/patients/{patient}/edit', [PatientController::class, 'edit'])->name('admin.patients.edit');
    Route::put('/admin/patients/{patient}', [PatientController::class, 'update'])->name('admin.patients.update');
    Route::delete('/admin/patients/{patient}', [PatientController::class, 'destroy'])->name('admin.patients.destroy');

    // Doctors
    Route::get('/admin/doctors', [DoctorController::class, 'index'])->name('admin.doctors.index');
    Route::get('/admin/doctors/create', [DoctorController::class, 'create'])->name('admin.doctors.create');
    Route::post('/admin/doctors', [DoctorController::class, 'store'])->name('admin.doctors.store');
    Route::get('/admin/doctors/{doctor}', [DoctorController::class, 'show'])->name('admin.doctors.show');
    Route::get('/admin/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('admin.doctors.edit');
    Route::put('/admin/doctors/{doctor}', [DoctorController::class, 'update'])->name('admin.doctors.update');
    Route::delete('/admin/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('admin.doctors.destroy');

    // Services
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/{service}', [ServiceController::class, 'show'])->name('admin.services.show');
    Route::get('/admin/services/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

    // Appointments
    Route::get('/admin/appointments', [AppointmentController::class, 'index'])->name('admin.appointments.index');
    Route::get('/admin/appointments/create', [AppointmentController::class, 'create'])->name('admin.appointments.create');
    Route::post('/admin/appointments', [AppointmentController::class, 'store'])->name('admin.appointments.store');
    Route::get('/admin/appointments/{appointment}', [AppointmentController::class, 'show'])->name('admin.appointments.show');
    Route::get('/admin/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('admin.appointments.edit');
    Route::put('/admin/appointments/{appointment}', [AppointmentController::class, 'update'])->name('admin.appointments.update');
    Route::delete('/admin/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('admin.appointments.destroy');


 //  توجيه جديد لإدارة الحالة
    Route::get('/admin/appointments/{appointment}/manage', [AppointmentController::class, 'manage'])->name('admin.appointments.manage');
    Route::put('/admin/appointments/{appointment}/update-status', [AppointmentController::class, 'updateStatus'])->name('admin.appointments.update-status'); // ← التوجيه المفقود
    

    // Prescriptions
    Route::get('/admin/prescriptions', [PrescriptionController::class, 'index'])->name('admin.prescriptions.index');
    Route::get('/admin/prescriptions/create', [PrescriptionController::class, 'create'])->name('admin.prescriptions.create');
    Route::post('/admin/prescriptions', [PrescriptionController::class, 'store'])->name('admin.prescriptions.store');
    Route::get('/admin/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('admin.prescriptions.show');
    Route::get('/admin/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('admin.prescriptions.edit');
    Route::put('/admin/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('admin.prescriptions.update');
    Route::delete('/admin/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('admin.prescriptions.destroy');


Route::post('/admin/prescriptions/{prescription}/medications', [PrescriptionController::class, 'storeMedication'])->name('admin.prescriptions.medications.store');
Route::delete('/admin/prescriptions/{prescription}/medications/{medication}', [PrescriptionController::class, 'destroyMedication'])->name('admin.prescriptions.medications.destroy');




    // Invoices
    Route::get('/admin/invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('/admin/invoices/create', [InvoiceController::class, 'create'])->name('admin.invoices.create');
    Route::post('/admin/invoices', [InvoiceController::class, 'store'])->name('admin.invoices.store');
    Route::get('/admin/invoices/{invoice}', [InvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::get('/admin/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('admin.invoices.edit');
    Route::put('/admin/invoices/{invoice}', [InvoiceController::class, 'update'])->name('admin.invoices.update');
    Route::delete('/admin/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('admin.invoices.destroy');

    // Payments
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/admin/payments/create', [PaymentController::class, 'create'])->name('admin.payments.create');
    Route::post('/admin/payments', [PaymentController::class, 'store'])->name('admin.payments.store');
    Route::get('/admin/payments/{payment}', [PaymentController::class, 'show'])->name('admin.payments.show');
    Route::get('/admin/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('admin.payments.edit');
    Route::put('/admin/payments/{payment}', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::delete('/admin/payments/{payment}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    // Lab Tests
    Route::get('/admin/lab-tests', [LabTestController::class, 'index'])->name('admin.lab-tests.index');
    Route::get('/admin/lab-tests/create', [LabTestController::class, 'create'])->name('admin.lab-tests.create');
    Route::post('/admin/lab-tests', [LabTestController::class, 'store'])->name('admin.lab-tests.store');
    Route::get('/admin/lab-tests/{labTest}', [LabTestController::class, 'show'])->name('admin.lab-tests.show');
    Route::get('/admin/lab-tests/{labTest}/edit', [LabTestController::class, 'edit'])->name('admin.lab-tests.edit');
    Route::put('/admin/lab-tests/{labTest}', [LabTestController::class, 'update'])->name('admin.lab-tests.update');
    Route::delete('/admin/lab-tests/{labTest}', [LabTestController::class, 'destroy'])->name('admin.lab-tests.destroy');
    Route::post('/admin/lab-tests/{labTest}/upload-result', [LabTestController::class, 'uploadResult'])->name('admin.lab-tests.uploadResult');

    // Teleconsultations
    Route::get('/admin/teleconsultations', [TeleconsultationController::class, 'index'])->name('admin.teleconsultations.index');
    Route::get('/admin/teleconsultations/create', [TeleconsultationController::class, 'create'])->name('admin.teleconsultations.create');
    Route::post('/admin/teleconsultations', [TeleconsultationController::class, 'store'])->name('admin.teleconsultations.store');
    Route::get('/admin/teleconsultations/{teleconsultation}', [TeleconsultationController::class, 'show'])->name('admin.teleconsultations.show');
    Route::get('/admin/teleconsultations/{teleconsultation}/edit', [TeleconsultationController::class, 'edit'])->name('admin.teleconsultations.edit');
    Route::put('/admin/teleconsultations/{teleconsultation}', [TeleconsultationController::class, 'update'])->name('admin.teleconsultations.update');
    Route::delete('/admin/teleconsultations/{teleconsultation}', [TeleconsultationController::class, 'destroy'])->name('admin.teleconsultations.destroy');
    Route::post('/admin/teleconsultations/{teleconsultation}/start', [TeleconsultationController::class, 'startConsultation'])->name('admin.teleconsultations.start');
    Route::post('/admin/teleconsultations/{teleconsultation}/end', [TeleconsultationController::class, 'endConsultation'])->name('admin.teleconsultations.end');

   // Pharmacy
    Route::get('/admin/pharmacy/pharmacies', [PharmacyController::class, 'pharmacies'])->name('admin.pharmacy.pharmacies');
    Route::get('/admin/pharmacy/pharmacies/create', [PharmacyController::class, 'createPharmacy'])->name('admin.pharmacy.pharmacies.create');
    Route::post('/admin/pharmacy/pharmacies', [PharmacyController::class, 'storePharmacy'])->name('admin.pharmacy.pharmacies.store');
    Route::get('/admin/pharmacy/pharmacies/{pharmacy}', [PharmacyController::class, 'showPharmacy'])->name('admin.pharmacy.pharmacies.show'); // ← التوجيه المفقود
    Route::get('/admin/pharmacy/pharmacies/{pharmacy}/edit', [PharmacyController::class, 'editPharmacy'])->name('admin.pharmacy.pharmacies.edit');
    Route::put('/admin/pharmacy/pharmacies/{pharmacy}', [PharmacyController::class, 'updatePharmacy'])->name('admin.pharmacy.pharmacies.update');
    Route::delete('/admin/pharmacy/pharmacies/{pharmacy}', [PharmacyController::class, 'destroyPharmacy'])->name('admin.pharmacy.pharmacies.destroy');

    Route::get('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'medications'])->name('admin.pharmacy.medications');
    Route::get('/admin/pharmacy/{pharmacy}/medications/create', [PharmacyController::class, 'createMedication'])->name('admin.pharmacy.medications.create');
    Route::post('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'storeMedication'])->name('admin.pharmacy.medications.store');

    Route::get('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'sales'])->name('admin.pharmacy.sales');
    Route::get('/admin/pharmacy/{pharmacy}/sales/create', [PharmacyController::class, 'createSale'])->name('admin.pharmacy.sales.create');
    Route::post('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'storeSale'])->name('admin.pharmacy.sales.store');



// Pharmacy Medications
    Route::get('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'medications'])->name('admin.pharmacy.medications');
    Route::get('/admin/pharmacy/{pharmacy}/medications/create', [PharmacyController::class, 'createMedication'])->name('admin.pharmacy.medications.create');
    Route::post('/admin/pharmacy/{pharmacy}/medications', [PharmacyController::class, 'storeMedication'])->name('admin.pharmacy.medications.store');
    Route::get('/admin/pharmacy/{pharmacy}/medications/{medication}', [PharmacyController::class, 'showMedication'])->name('admin.pharmacy.medications.show'); // ← التوجيه المفقود
    Route::get('/admin/pharmacy/{pharmacy}/medications/{medication}/edit', [PharmacyController::class, 'editMedication'])->name('admin.pharmacy.medications.edit');
    Route::put('/admin/pharmacy/{pharmacy}/medications/{medication}', [PharmacyController::class, 'updateMedication'])->name('admin.pharmacy.medications.update');
    Route::delete('/admin/pharmacy/{pharmacy}/medications/{medication}', [PharmacyController::class, 'destroyMedication'])->name('admin.pharmacy.medications.destroy');




    Route::get('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'sales'])->name('admin.pharmacy.sales');
    Route::get('/admin/pharmacy/{pharmacy}/sales/create', [PharmacyController::class, 'createSale'])->name('admin.pharmacy.sales.create');
    Route::post('/admin/pharmacy/{pharmacy}/sales', [PharmacyController::class, 'storeSale'])->name('admin.pharmacy.sales.store');

    // Employees
    Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('/admin/employees/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/admin/employees/{employee}', [EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::get('/admin/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/admin/employees/{employee}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/admin/employees/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');
    Route::get('/admin/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('admin.employees.attendance');
    Route::post('/admin/employees/{employee}/attendance', [EmployeeController::class, 'recordAttendance'])->name('admin.employees.recordAttendance');
    Route::get('/admin/employees/{employee}/leaves', [EmployeeController::class, 'leaves'])->name('admin.employees.leaves');
    Route::post('/admin/employees/{employee}/leaves', [EmployeeController::class, 'storeLeave'])->name('admin.employees.storeLeave');
    Route::post('/admin/employees/leaves/{leave}/approve', [EmployeeController::class, 'approveLeave'])->name('admin.employees.approveLeave');
    Route::post('/admin/employees/leaves/{leave}/reject', [EmployeeController::class, 'rejectLeave'])->name('admin.employees.rejectLeave');
    Route::get('/admin/employees/{employee}/salaries', [EmployeeController::class, 'salaries'])->name('admin.employees.salaries');
    Route::post('/admin/employees/{employee}/salaries', [EmployeeController::class, 'storeSalary'])->name('admin.employees.storeSalary');
    Route::post('/admin/employees/salaries/{salary}/pay', [EmployeeController::class, 'paySalary'])->name('admin.employees.paySalary');

    // Patient CRM
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

    // Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/financial', [ReportController::class, 'financial'])->name('admin.reports.financial');
    Route::get('/admin/reports/doctor-performance', [ReportController::class, 'doctorPerformance'])->name('admin.reports.doctor-performance');
    Route::get('/admin/reports/service-report', [ReportController::class, 'serviceReport'])->name('admin.reports.service-report');
    
    // Advanced Reports
    Route::get('/admin/reports/patient-flow', [AdvancedReportController::class, 'patientFlow'])->name('admin.reports.patient-flow'); // ← التوجيه المفقود
    Route::get('/admin/reports/revenue-analysis', [AdvancedReportController::class, 'revenueAnalysis'])->name('admin.reports.revenue-analysis');
    Route::get('/admin/reports/doctor-workload', [AdvancedReportController::class, 'doctorWorkload'])->name('admin.reports.doctor-workload');

  
    Route::get('/admin/reports/doctor-performance', [AdvancedReportController::class, 'doctorPerformance'])->name('admin.reports.doctor-performance');

    // Exports
    Route::post('/admin/export/appointments', [ExportController::class, 'exportAppointments'])->name('admin.export.appointments');
    Route::post('/admin/export/financial', [ExportController::class, 'exportFinancial'])->name('admin.export.financial');

    // Notifications
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.readAll');
    Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Notification Settings
    Route::get('/admin/notifications/settings', [NotificationSettingController::class, 'index'])->name('admin.notifications.settings');
    Route::put('/admin/notifications/settings', [NotificationSettingController::class, 'update'])->name('admin.notifications.settings.update');
});

// Doctor Routes
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->name('doctor.dashboard');
});

// Receptionist Routes
Route::middleware(['auth', 'role:receptionist'])->group(function () {
    Route::get('/receptionist/dashboard', function () {
        return view('receptionist.dashboard');
    })->name('receptionist.dashboard');
});

// Patient Routes
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', function () {
        return view('patient.dashboard');
    })->name('patient.dashboard');
});