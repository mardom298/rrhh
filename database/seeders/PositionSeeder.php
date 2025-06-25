<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['department_id' => 1, 'title' => 'Gerente de RRHH', 'min_salary' => 5000, 'max_salary' => 8000],
            ['department_id' => 1, 'title' => 'Analista de RRHH', 'min_salary' => 2500, 'max_salary' => 4000],
            ['department_id' => 2, 'title' => 'Supervisor de Obras', 'min_salary' => 3000, 'max_salary' => 5000],
            ['department_id' => 3, 'title' => 'Coordinador Logístico', 'min_salary' => 2800, 'max_salary' => 4500],
            ['department_id' => 4, 'title' => 'Desarrollador Senior', 'min_salary' => 4000, 'max_salary' => 7000],
        ];

        foreach ($positions as $pos) {
            Position::create([
                'department_id' => $pos['department_id'],
                'title' => $pos['title'],
                'description' => 'Posición de ' . $pos['title'],
                'min_salary' => $pos['min_salary'],
                'max_salary' => $pos['max_salary'],
                'requirements' => ['Experiencia mínima 2 años', 'Estudios superiores'],
                'responsibilities' => ['Gestión de equipo', 'Reportes mensuales'],
                'status' => 'active'
            ]);
        }
    }
}
