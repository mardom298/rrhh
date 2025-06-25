<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Crear el grupo empresarial primero
            BusinessGroupSeeder::class,
            
            // 2. Crear las empresas del grupo
            GroupCompaniesSeeder::class,
            
            // 3. Crear departamentos y posiciones
            DepartmentSeeder::class,
            PositionSeeder::class,
            
            // 4. Crear usuarios (empleados globales)
            UserSeeder::class,
            
            // 5. Asignar empleados a empresas
            EmployeeCompaniesSeeder::class,
            
            // 6. Crear conceptos de nómina
            PayrollConceptSeeder::class,
            
            // 7. Crear datos de evaluaciones
            PerformanceEvaluationSeeder::class,
        ]);
    }
}
