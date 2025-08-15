<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار
        $adminRole = Role::create(['name' => 'admin']);
        $doctorRole = Role::create(['name' => 'doctor']);
        $receptionistRole = Role::create(['name' => 'receptionist']);
        $patientRole = Role::create(['name' => 'patient']);

        // إنشاء الصلاحيات الأساسية
        $permissions = [
            'manage-users',
            'manage-patients',
            'manage-doctors',
            'manage-services',
            'manage-appointments',
            'manage-prescriptions',
            'manage-invoices',
            'manage-payments',
            'manage-lab-tests',
            'manage-teleconsultations',
            'manage-pharmacy',
            'manage-employees',
            'manage-patient-crm',
            'manage-reports',
            'manage-advanced-reports',
            'manage-exports',
            'manage-notifications',
            'manage-notification-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // تعيين الصلاحيات للأدوار
        $adminRole->givePermissionTo(Permission::all());
        
        $doctorRole->givePermissionTo([
            'manage-patients',
            'manage-appointments',
            'manage-prescriptions',
            'manage-lab-tests',
            'manage-teleconsultations',
            'manage-patient-crm',
            'manage-reports',
            'manage-notifications',
            'manage-notification-settings',
        ]);
        
        $receptionistRole->givePermissionTo([
            'manage-patients',
            'manage-doctors',
            'manage-services',
            'manage-appointments',
            'manage-invoices',
            'manage-payments',
            'manage-lab-tests',
            'manage-notifications',
        ]);
        
        $patientRole->givePermissionTo([
            'manage-appointments',
            'manage-prescriptions',
            'manage-invoices',
            'manage-payments',
            'manage-lab-tests',
            'manage-teleconsultations',
            'manage-notifications',
        ]);
    }
}