<?php

namespace Database\Seeders;

use App\Models\EmployeeCompany;
use Illuminate\Database\Seeder;

class EmployeeCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Carlos Ballesteros - Gerente en todas las empresas
        EmployeeCompany::create([
            'user_id' => 1,
            'company_id' => 1,
            'department_id' => 1,
            'position_id' => 1,
            'employee_code' => 'BC-001',
            'hire_date' => '2020-01-01',
            'status' => 'active',
            'base_salary' => 12000,
            'total_salary' => 12000,
            'is_primary_company' => true,
            'contract_type' => 'indefinite'
        ]);

        EmployeeCompany::create([
            'user_id' => 1,
            'company_id' => 2,
            'department_id' => 4,
            'position_id' => 3,
            'employee_code' => 'BL-001',
            'hire_date' => '2020-01-01',
            'status' => 'active',
            'base_salary' => 8000,
            'total_salary' => 8000,
            'is_primary_company' => false,
            'contract_type' => 'indefinite'
        ]);

        // María García - RRHH en Construcción y Tecnología
        EmployeeCompany::create([
            'user_id' => 2,
            'company_id' => 1,
            'department_id' => 1,
            'position_id' => 2,
            'employee_code' => 'BC-002',
            'hire_date' => '2021-03-15',
            'status' => 'active',
            'base_salary' => 5000,
            'total_salary' => 5000,
            'is_primary_company' => true,
            'contract_type' => 'indefinite'
        ]);

        EmployeeCompany::create([
            'user_id' => 2,
            'company_id' => 3,
            'department_id' => 7,
            'position_id' => 4,
            'employee_code' => 'BT-001',
            'hire_date' => '2022-01-10',
            'status' => 'active',
            'base_salary' => 4000,
            'total_salary' => 4000,
            'is_primary_company' => false,
            'contract_type' => 'part_time'
        ]);

        // Luis Rodríguez - Consultor en Tecnología
        EmployeeCompany::create([
            'user_id' => 3,
            'company_id' => 3,
            'department_id' => 8,
            'position_id' => 11,
            'employee_code' => 'BT-002',
            'hire_date' => '2023-06-01',
            'status' => 'active',
            'base_salary' => 8000,
            'total_salary' => 8000,
            'is_primary_company' => true,
            'contract_type' => 'consultant'
        ]);
    }
}
