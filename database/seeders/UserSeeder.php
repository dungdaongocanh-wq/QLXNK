<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminDept = Department::where('code', 'ADMIN')->first();
        $xnkDept   = Department::where('code', 'XNK')->first();

        User::firstOrCreate(
            ['email' => 'admin@qlxnk.local'],
            [
                'name'          => 'Quản Trị Viên',
                'password'      => Hash::make('password'),
                'department_id' => $adminDept?->id,
                'role'          => 'admin',
                'is_active'     => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'nhanvien1@qlxnk.local'],
            [
                'name'          => 'Nguyễn Văn An',
                'password'      => Hash::make('password'),
                'department_id' => $xnkDept?->id,
                'role'          => 'staff',
                'is_active'     => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@qlxnk.local'],
            [
                'name'          => 'Trần Thị Bình',
                'password'      => Hash::make('password'),
                'department_id' => $xnkDept?->id,
                'role'          => 'manager',
                'is_active'     => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'kho@qlxnk.local'],
            [
                'name'          => 'Lê Văn Cường',
                'password'      => Hash::make('password'),
                'department_id' => Department::where('code', 'KHO')->first()?->id,
                'role'          => 'staff',
                'is_active'     => true,
            ]
        );
    }
}
