<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeCompany;

class EmployeeCompanySeeder extends Seeder
{
    public function run(): void
    {
        EmployeeCompany::create([
            'user_id' => 1,
            'company_id' => 1,
            'department_id' => 1,
            'position_id' => 1,
            'employee_code' => 'BC-001',
            'hire_date' => '2024-01-01',
            'base_salary' => 6000.00,
            'total_salary' => 6000.00,
            'is_primary_company' => true,
            'status' => 'active'
        ]);
    }
}
