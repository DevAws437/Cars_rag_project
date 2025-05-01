<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {

        // إنشاء الصلاحيات
        $permissions = [
            'manage users',
            'manage categories',
            'manage cars',
            'manage reservations',
            'manage discounts',
            'manage employees',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء الأدوار وربط الصلاحيات فيها

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo($permissions);

        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $employee->givePermissionTo([
            'manage categories',
            'manage cars',
            'manage reservations',
        ]);
    }
}
