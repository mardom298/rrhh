<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeCompany;
use Illuminate\Database\Seeder;

class EmployeeCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $companies = Company::all();
        
        foreach ($users as $user) {
            // Determinar cuántas empresas tendrá este empleado (80% una empresa, 20% múltiples)
            $companyCount = rand(1, 100) <= 80 ? 1 : rand(2, 3);
            $selectedCompanies = $companies->random($companyCount);
            
            $isPrimarySet = false;
            
            foreach ($selectedCompanies as $index => $company) {
                $department = $company->departments()->inRandomOrder()->first();
                $position = $department ? $department->positions()->inRandomOrder()->first() : null;
                
                // Salario base variable por empresa
                $baseSalary = match($company->company_code) {
                    'BALL-CONST' => rand(2500, 8000),
                    'BALL-LOG' => rand(2000, 6000),
                    'BALL-SERV' => rand(1800, 5000),
                    'BALL-INMOB' => rand(3000, 9000),
                    'BALL-TECH' => rand(3500, 12000),
                    default => rand(2000, 6000)
                };
                
                EmployeeCompany::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'employee_code' => $company->company_code . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'department_id' => $department?->id,
                    'position_id' => $position?->id,
                    'manager_id' => $index === 0 ? null : $users->where('id', '!=', $user->id)->random()->id,
                    'hire_date' => now()->subDays(rand(30, 1095)), // Entre 1 mes y 3 años
                    'status' => 'active',
                    'base_salary' => $baseSalary,
                    'salary_components' => [
                        ['name' => 'Bono de productividad', 'amount' => $baseSalary * 0.1],
                        ['name' => 'Movilidad', 'amount' => 200]
                    ],
                    'contract_type' => ['indefinido', 'plazo_fijo', 'part_time'][rand(0, 2)],
                    'payroll_frequency' => 'monthly',
                    'cost_center' => $department?->name ?? 'General',
                    'is_primary_company' => !$isPrimarySet && $index === 0,
                    'benefits' => [
                        'seguro_salud' => true,
                        'seguro_vida' => $baseSalary > 3000,
                        'bonos_anuales' => rand(1, 2)
                    ]
                ]);
                
                if (!$isPrimarySet && $index === 0) {
                    $isPrimarySet = true;
                }
            }
        }
    }
}
