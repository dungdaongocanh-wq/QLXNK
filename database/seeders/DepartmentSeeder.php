<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Phòng Xuất Nhập Khẩu', 'code' => 'XNK'],
            ['name' => 'Phòng Kho Vận',         'code' => 'KHO'],
            ['name' => 'Phòng Kinh Doanh',       'code' => 'KD'],
            ['name' => 'Ban Quản Trị',            'code' => 'ADMIN'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
