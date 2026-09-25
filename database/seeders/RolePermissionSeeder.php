<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // রোল তৈরি
        $roles = ['Super Admin', 'Admin', 'Teacher', 'Student', 'Guardian', 'Accountant', 'Librarian', 'Staff'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // পারমিশন তৈরি (মডিউল ভিত্তিক)
        $permissions = [
            'institute-settings', 'academic-year', 'class-manage', 'section-manage',
            'group-manage', 'subject-manage', 'student-manage', 'teacher-manage',
            'attendance-manage', 'exam-manage', 'result-manage', 'fee-manage',
            'user-manage', 'role-manage', 'report-view', 'sms-send',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Super Admin কে সব পারমিশন
        Role::findByName('Super Admin')->syncPermissions(Permission::all());

        // Admin কে প্রায় সব (role-manage ছাড়া)
        Role::findByName('Admin')->syncPermissions(
            Permission::whereNotIn('name', ['role-manage'])->get()
        );

        // Teacher পারমিশন
        Role::findByName('Teacher')->syncPermissions([
            'attendance-manage', 'exam-manage', 'result-manage', 'report-view',
        ]);

        // Student পারমিশন
        Role::findByName('Student')->syncPermissions(['report-view']);

        // Guardian পারমিশন
        Role::findByName('Guardian')->syncPermissions(['report-view']);

        // Accountant পারমিশন
        Role::findByName('Accountant')->syncPermissions(['fee-manage', 'report-view']);

        // সিস্টেমের প্রথম Super Admin ইউজার
        $admin = User::firstOrCreate(
            ['email' => 'admin@usms.test'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
                'is_active' => true,
            ]
        );
        $admin->assignRole('Super Admin');
    }
}
