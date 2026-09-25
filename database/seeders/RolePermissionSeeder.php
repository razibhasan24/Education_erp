<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
 

public function run()
{
    // Define permissions
    $permissions = [
        'manage-institution', 'manage-academic-year', 'manage-class',
        'manage-section', 'manage-student', 'manage-teacher',
        'manage-attendance', 'manage-exam', 'manage-fee',
        'view-report', 'issue-certificate',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }

    // Define roles
    $superAdmin = Role::create(['name' => 'super-admin']);
    $admin = Role::create(['name' => 'admin']);
    $teacher = Role::create(['name' => 'teacher']);
    $student = Role::create(['name' => 'student']);
    $parent = Role::create(['name' => 'parent']);
    $accountant = Role::create(['name' => 'accountant']);

    // Assign permissions to roles
    $superAdmin->givePermissionTo(Permission::all());
    $admin->givePermissionTo([
        'manage-academic-year', 'manage-class', 'manage-section',
        'manage-student', 'manage-teacher', 'manage-attendance',
        'manage-exam', 'manage-fee', 'view-report', 'issue-certificate',
    ]);
    $teacher->givePermissionTo([
        'manage-attendance', 'manage-exam', 'view-report',
    ]);
    $accountant->givePermissionTo([
        'manage-fee', 'view-report',
    ]);
    $student->givePermissionTo(['view-report']);
    $parent->givePermissionTo(['view-report']);
}
}