<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            // RRHH
            ['title' => 'Gerente de RRHH', 'code' => 'GER-RRHH', 'department_id' => 1, 'min_salary' => 8000, 'max_salary' => 12000],
            ['title' => 'Especialista en RRHH', 'code' => 'ESP-RRHH', 'department_id' => 1, 'min_salary' => 4000, 'max_salary' => 6000],
            ['title' => 'Asistente de RRHH', 'code' => 'ASI-RRHH', 'department_id' => 1, 'min_salary' => 2500, 'max_salary' => 3500],
            
            // Tecnología
            ['title' => 'Gerente de TI', 'code' => 'GER-TI', 'department_id' => 2, 'min_salary' => 10000, 'max_salary' => 15000],
            ['title' => 'Desarrollador Senior', 'code' => 'DEV-SR', 'department_id' => 2, 'min_salary' => 6000, 'max_salary' => 9000],
            ['title' => 'Desarrollador Junior', 'code' => 'DEV-JR', 'department_id' => 2, 'min_salary' => 3000, 'max_salary' => 5000],
            ['title' => 'Analista de Sistemas', 'code' => 'ANA-SIS', 'department_id' => 2, 'min_salary' => 4500, 'max_salary' => 7000],
            
            // Ventas
            ['title' => 'Gerente de Ventas', 'code' => 'GER-VEN', 'department_id' => 3, 'min_salary' => 8000, 'max_salary' => 12000],
            ['title' => 'Ejecutivo de Ventas', 'code' => 'EJE-VEN', 'department_id' => 3, 'min_salary' => 3500, 'max_salary' => 5500],
            ['title' => 'Coordinador de Ventas', 'code' => 'COO-VEN', 'department_id' => 3, 'min_salary' => 4000, 'max_salary' => 6000],
            
            // Marketing
            ['title' => 'Gerente de Marketing', 'code' => 'GER-MKT', 'department_id' => 4, 'min_salary' => 7000, 'max_salary' => 10000],
            ['title' => 'Especialista en Marketing Digital', 'code' => 'ESP-MKT-DIG', 'department_id' => 4, 'min_salary' => 4000, 'max_salary' => 6000],
            ['title' => 'Community Manager', 'code' => 'COM-MAN', 'department_id' => 4, 'min_salary' => 2800, 'max_salary' => 4000],
            
            // Finanzas
            ['title' => 'Gerente de Finanzas', 'code' => 'GER-FIN', 'department_id' => 5, 'min_salary' => 9000, 'max_salary' => 13000],
            ['title' => 'Contador', 'code' => 'CON', 'department_id' => 5, 'min_salary' => 4000, 'max_salary' => 6000],
            ['title' => 'Asistente Contable', 'code' => 'ASI-CON', 'department_id' => 5, 'min_salary' => 2500, 'max_salary' => 3500]
        ];

        foreach ($positions as $position) {
            Position::create([
                'company_id' => 1,
                'department_id' => $position['department_id'],
                'title' => $position['title'],
                'code' => $position['code'],
                'description' => 'Puesto de ' . $position['title'],
                'min_salary' => $position['min_salary'],
                'max_salary' => $position['max_salary'],
                'active' => true
            ]);
        }
    }
}
