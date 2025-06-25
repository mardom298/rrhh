<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            // Ballesteros Construcción
            ['company_id' => 1, 'name' => 'Recursos Humanos', 'manager_id' => 1],
            ['company_id' => 1, 'name' => 'Ingeniería', 'manager_id' => 2],
            ['company_id' => 1, 'name' => 'Operaciones', 'manager_id' => 3],
            
            // Ballesteros Logística
            ['company_id' => 2, 'name' => 'Recursos Humanos', 'manager_id' => 1],
            ['company_id' => 2, 'name' => 'Transporte', 'manager_id' => 2],
            ['company_id' => 2, 'name' => 'Almacén', 'manager_id' => 3],
            
            // Ballesteros Tecnología
            ['company_id' => 3, 'name' => 'Recursos Humanos', 'manager_id' => 1],
            ['company_id' => 3, 'name' => 'Desarrollo', 'manager_id' => 2],
            ['company_id' => 3, 'name' => 'Soporte Técnico', 'manager_id' => 3],
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
