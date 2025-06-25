<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        Position::create([
            'department_id' => 1,
            'title' => 'Gerente de RRHH',
            'description' => 'Responsable de la gestión de recursos humanos',
            'min_salary' => 5000.00,
            'max_salary' => 8000.00,
            'requirements' => ['Título universitario', 'Experiencia 5+ años'],
            'responsibilities' => ['Gestión de personal', 'Reclutamiento'],
            'status' => 'active'
        ]);
    }
}
