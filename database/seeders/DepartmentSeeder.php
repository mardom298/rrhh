<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Recursos Humanos', 'code' => 'RRHH'],
            ['name' => 'Tecnología', 'code' => 'TI'],
            ['name' => 'Ventas', 'code' => 'VEN'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Finanzas', 'code' => 'FIN'],
            ['name' => 'Operaciones', 'code' => 'OPS'],
            ['name' => 'Administración', 'code' => 'ADM'],
            ['name' => 'Logística', 'code' => 'LOG'],
            ['name' => 'Calidad', 'code' => 'CAL'],
            ['name' => 'Legal', 'code' => 'LEG']
        ];

        foreach ($departments as $dept) {
            Department::create([
                'company_id' => 1,
                'name' => $dept['name'],
                'code' => $dept['code'],
                'description' => 'Departamento de ' . $dept['name'],
                'active' => true
            ]);
        }
    }
}
