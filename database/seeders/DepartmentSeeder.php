<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['company_id' => 1, 'name' => 'Recursos Humanos', 'manager_id' => 1],
            ['company_id' => 1, 'name' => 'Operaciones', 'manager_id' => 2],
            ['company_id' => 2, 'name' => 'Logística', 'manager_id' => 1],
            ['company_id' => 3, 'name' => 'Desarrollo', 'manager_id' => 2],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'company_id' => $dept['company_id'],
                'name' => $dept['name'],
                'description' => 'Departamento de ' . $dept['name'],
                'manager_id' => $dept['manager_id'],
                'status' => 'active'
            ]);
        }
    }
}
