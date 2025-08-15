<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // تعريف الصلاحيات المخصصة
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('doctor', function ($user) {
            return $user->role === 'doctor';
        });

        Gate::define('receptionist', function ($user) {
            return $user->role === 'receptionist';
        });

        Gate::define('patient', function ($user) {
            return $user->role === 'patient';
        });

        // صلاحيات إضافية
        Gate::define('manage-users', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-patients', function ($user) {
            return in_array($user->role, ['admin', 'doctor', 'receptionist']);
        });

        Gate::define('manage-doctors', function ($user) {
            return in_array($user->role, ['admin', 'receptionist']);
        });

        Gate::define('manage-services', function ($user) {
            return in_array($user->role, ['admin', 'receptionist']);
        });

        Gate::define('manage-appointments', function ($user) {
            return in_array($user->role, ['admin', 'doctor', 'receptionist']);
        });

        Gate::define('manage-prescriptions', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });

        Gate::define('manage-invoices', function ($user) {
            return in_array($user->role, ['admin', 'receptionist']);
        });

        Gate::define('manage-payments', function ($user) {
            return in_array($user->role, ['admin', 'receptionist']);
        });

        Gate::define('manage-lab-tests', function ($user) {
            return in_array($user->role, ['admin', 'doctor', 'receptionist']);
        });

        Gate::define('manage-teleconsultations', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });

        Gate::define('manage-pharmacy', function ($user) {
            return in_array($user->role, ['admin', 'pharmacist']);
        });

        Gate::define('manage-employees', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-patient-crm', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });

        Gate::define('manage-reports', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });

        Gate::define('manage-advanced-reports', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-exports', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-notifications', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });

        Gate::define('manage-notification-settings', function ($user) {
            return in_array($user->role, ['admin', 'doctor']);
        });
    }
}