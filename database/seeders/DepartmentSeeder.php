<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['company_id' => 1, 'name' => 'Recursos Humanos'],
            ['company_id' => 1, 'name' => 'Administración'],
            ['company_id' => 2, 'name' => 'Operaciones'],
            ['company_id' => 3, 'name' => 'Desarrollo'],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'company_id' => $dept['company_id'],
                'name' => $dept['name'],
                'description' => 'Departamento de ' . $dept['name'],
                'manager_id' => 1,
                'budget' => 50000.00,
                'status' => 'active'
            ]);
        }
    }
}
