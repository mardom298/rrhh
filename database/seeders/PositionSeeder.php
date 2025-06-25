<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            // Recursos Humanos (dept 1, 4, 7)
            ['department_id' => 1, 'title' => 'Gerente de RRHH', 'min_salary' => 8000, 'max_salary' => 12000],
            ['department_id' => 1, 'title' => 'Especialista en RRHH', 'min_salary' => 4000, 'max_salary' => 6000],
            ['department_id' => 4, 'title' => 'Gerente de RRHH', 'min_salary' => 8000, 'max_salary' => 12000],
            ['department_id' => 7, 'title' => 'Gerente de RRHH', 'min_salary' => 8000, 'max_salary' => 12000],
            
            // Ingeniería (dept 2)
            ['department_id' => 2, 'title' => 'Ingeniero Civil Senior', 'min_salary' => 6000, 'max_salary' => 10000],
            ['department_id' => 2, 'title' => 'Ingeniero Civil Junior', 'min_salary' => 3000, 'max_salary' => 5000],
            
            // Operaciones (dept 3)
            ['department_id' => 3, 'title' => 'Supervisor de Obra', 'min_salary' => 4000, 'max_salary' => 6000],
            ['department_id' => 3, 'title' => 'Operario', 'min_salary' => 1500, 'max_salary' => 2500],
            
            // Transporte (dept 5)
            ['department_id' => 5, 'title' => 'Coordinador de Transporte', 'min_salary' => 3500, 'max_salary' => 5000],
            ['department_id' => 5, 'title' => 'Conductor', 'min_salary' => 1800, 'max_salary' => 2800],
            
            // Desarrollo (dept 8)
            ['department_id' => 8, 'title' => 'Desarrollador Senior', 'min_salary' => 7000, 'max_salary' => 12000],
            ['department_id' => 8, 'title' => 'Desarrollador Junior', 'min_salary' => 3000, 'max_salary' => 5000],
        ];

        foreach ($positions as $pos) {
            Position::create([
                'department_id' => $pos['department_id'],
                'title' => $pos['title'],
                'description' => 'Posición de ' . $pos['title'],
                'min_salary' => $pos['min_salary'],
                'max_salary' => $pos['max_salary'],
                'requirements' => ['Experiencia mínima 2 años', 'Título profesional'],
                'responsibilities' => ['Cumplir objetivos', 'Trabajar en equipo'],
                'status' => 'active'
            ]);
        }
    }
}
