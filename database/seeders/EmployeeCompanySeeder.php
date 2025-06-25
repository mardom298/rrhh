<?php

namespace Database\Seeders;

use App\Models\EmployeeCompany;
use Illuminate\Database\Seeder;

class EmployeeCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Carlos trabaja en Construcción (principal) y Tecnología
        EmployeeCompany::create([
            'user_id' => 1,
            'company_id' => 1,
            'department_id' => 1,
            'position_id' => 1,
            'employee_code' => 'CONST-001',
            'hire_date' => '2020-01-15',
            'status' => 'active',
            'base_salary' => 6000.00,
            'total_salary' => 7200.00,
            'is_primary_company' => true,
            'contract_type' => 'indefinite'
        ]);

        EmployeeCompany::create([
            'user_id' => 1,
            'company_id' => 3,
            'department_id' => 4,
            'position_id' => 5,
            'employee_code' => 'TECH-001',
            'hire_date' => '2022-06-01',
            'status' => 'active',
            'base_salary' => 2000.00,
            'total_salary' => 2400.00,
            'is_primary_company' => false,
            'contract_type' => 'part_time'
        ]);

        // María trabaja en Logística
        EmployeeCompany::create([
            'user_id' => 2,
            'company_id' => 2,
            'department_id' => 3,
            'position_id' => 4,
            'employee_code' => 'LOG-001',
            'hire_date' => '2021-03-01',
            'status' => 'active',
            'base_salary' => 3500.00,
            'total_salary' => 4200.00,
            'is_primary_company' => true,
            'contract_type' => 'indefinite'
        ]);
    }
}
