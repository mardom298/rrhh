<?php

namespace Database\Seeders;

use App\Models\PerformanceEvaluation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PerformanceEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('company_id', 1)
            ->where('status', 'active')
            ->where('id', '>', 1) // Excluir admin
            ->get();

        $admin = User::find(1);

        foreach ($employees as $employee) {
            // Crear evaluación del trimestre anterior
            PerformanceEvaluation::create([
                'user_id' => $employee->id,
                'evaluator_id' => $admin->id,
                'period' => '2024-Q1',
                'type' => 'supervisor',
                'evaluation_date' => now()->subMonths(2),
                'due_date' => now()->subMonths(2)->addWeeks(2),
                'status' => 'completed',
                'overall_score' => fake()->randomFloat(2, 3.0, 5.0),
                'scores' => [
                    'comunicacion' => fake()->numberBetween(3, 5),
                    'trabajo_equipo' => fake()->numberBetween(3, 5),
                    'liderazgo' => fake()->numberBetween(2, 5),
                    'iniciativa' => fake()->numberBetween(3, 5),
                    'calidad_trabajo' => fake()->numberBetween(3, 5),
                    'puntualidad' => fake()->numberBetween(4, 5),
                    'adaptabilidad' => fake()->numberBetween(3, 5),
                    'conocimiento_tecnico' => fake()->numberBetween(3, 5)
                ],
                'strengths' => 'Demuestra excelente capacidad de trabajo en equipo y comunicación efectiva con colegas y clientes.',
                'areas_for_improvement' => 'Puede mejorar en la toma de iniciativa para proyectos nuevos y desarrollo de habilidades de liderazgo.',
                'goals' => 'Liderar al menos un proyecto durante el próximo trimestre y completar capacitación en liderazgo.',
                'development_plan' => 'Inscribirse en curso de liderazgo y asumir responsabilidades de mentoría con empleados junior.',
                'evaluator_comments' => 'Empleado valioso con gran potencial de crecimiento.',
                'submitted_at' => now()->subMonths(2)->addDays(10),
                'reviewed_at' => now()->subMonths(2)->addDays(15)
            ]);

            // Crear evaluación actual en progreso
            PerformanceEvaluation::create([
                'user_id' => $employee->id,
                'evaluator_id' => $admin->id,
                'period' => '2024-Q2',
                'type' => 'supervisor',
                'evaluation_date' => now(),
                'due_date' => now()->addWeeks(2),
                'status' => 'draft'
            ]);
        }
    }
}
