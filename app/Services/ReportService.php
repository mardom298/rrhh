<?php

namespace App\Services;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\PayrollPeriod;
use App\Models\PerformanceEvaluation;
use App\Models\JobApplication;
use App\Models\TrainingEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function generateAttendanceReport(array $params): array
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);
        $departmentId = $params['department_id'] ?? null;

        $query = AttendanceRecord::with(['user.department'])
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('user', function($q) use ($departmentId) {
                $q->where('company_id', auth()->user()->company_id);
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            });

        $records = $query->get();

        // Estadísticas generales
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $workingDays = $this->getWorkingDays($startDate, $endDate);
        
        $stats = [
            'total_employees' => $records->pluck('user_id')->unique()->count(),
            'total_records' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'late_days' => $records->where('status', 'late')->count(),
            'attendance_rate' => $records->count() > 0 ? 
                round(($records->where('status', 'present')->count() / $records->count()) * 100, 2) : 0,
            'punctuality_rate' => $records->count() > 0 ? 
                round((($records->where('status', 'present')->count() - $records->where('status', 'late')->count()) / $records->count()) * 100, 2) : 0,
            'total_hours_worked' => round($records->sum('worked_hours') / 60, 2),
            'total_overtime_hours' => round($records->sum('overtime_hours') / 60, 2)
        ];

        // Datos por empleado
        $employeeData = $records->groupBy('user_id')->map(function($userRecords) use ($workingDays) {
            $user = $userRecords->first()->user;
            $presentDays = $userRecords->where('status', 'present')->count();
            $lateDays = $userRecords->where('status', 'late')->count();
            $absentDays = $workingDays - $userRecords->count();

            return [
                'employee_name' => $user->full_name,
                'department' => $user->department->name ?? 'Sin departamento',
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'attendance_rate' => round(($presentDays / $workingDays) * 100, 2),
                'punctuality_rate' => $presentDays > 0 ? round((($presentDays - $lateDays) / $presentDays) * 100, 2) : 0,
                'total_hours' => round($userRecords->sum('worked_hours') / 60, 2),
                'overtime_hours' => round($userRecords->sum('overtime_hours') / 60, 2)
            ];
        })->values();

        // Datos por departamento
        $departmentData = $records->groupBy('user.department.name')->map(function($deptRecords, $deptName) {
            $employees = $deptRecords->pluck('user_id')->unique()->count();
            $presentDays = $deptRecords->where('status', 'present')->count();
            
            return [
                'department' => $deptName ?: 'Sin departamento',
                'employees' => $employees,
                'present_days' => $presentDays,
                'absent_days' => $deptRecords->where('status', 'absent')->count(),
                'late_days' => $deptRecords->where('status', 'late')->count(),
                'attendance_rate' => $deptRecords->count() > 0 ? 
                    round(($presentDays / $deptRecords->count()) * 100, 2) : 0
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y'),
                'working_days' => $workingDays
            ],
            'stats' => $stats,
            'employee_data' => $employeeData,
            'department_data' => $departmentData
        ];
    }

    public function generatePayrollReport(array $params): array
    {
        $periodId = $params['period_id'];
        $period = PayrollPeriod::with(['payrollItems.user.department', 'payrollItems.payrollConcept'])
            ->find($periodId);

        if (!$period) {
            throw new \Exception('Período de nómina no encontrado');
        }

        // Estadísticas generales
        $stats = [
            'period_name' => $period->name,
            'total_employees' => $period->payrollItems->pluck('user_id')->unique()->count(),
            'total_gross' => $period->total_gross,
            'total_deductions' => $period->total_deductions,
            'total_net' => $period->total_net,
            'average_salary' => $period->payrollItems->pluck('user_id')->unique()->count() > 0 ?
                round($period->total_net / $period->payrollItems->pluck('user_id')->unique()->count(), 2) : 0
        ];

        // Datos por empleado
        $employeeData = $period->payrollItems->groupBy('user_id')->map(function($items) {
            $user = $items->first()->user;
            $incomes = $items->where('payrollConcept.type', 'income')->sum('amount');
            $deductions = $items->where('payrollConcept.type', 'deduction')->sum('amount');
            
            return [
                'employee_name' => $user->full_name,
                'department' => $user->department->name ?? 'Sin departamento',
                'base_salary' => $user->salary,
                'gross_salary' => $incomes,
                'total_deductions' => $deductions,
                'net_salary' => $incomes - $deductions,
                'items' => $items->map(function($item) {
                    return [
                        'concept' => $item->payrollConcept->name,
                        'type' => $item->payrollConcept->type,
                        'amount' => $item->amount
                    ];
                })
            ];
        })->values();

        // Datos por concepto
        $conceptData = $period->payrollItems->groupBy('payrollConcept.name')->map(function($items, $conceptName) {
            return [
                'concept' => $conceptName,
                'type' => $items->first()->payrollConcept->type,
                'total_amount' => $items->sum('amount'),
                'employee_count' => $items->count(),
                'average_amount' => round($items->avg('amount'), 2)
            ];
        })->values();

        return [
            'period' => $period->only(['name', 'start_date', 'end_date', 'payment_date']),
            'stats' => $stats,
            'employee_data' => $employeeData,
            'concept_data' => $conceptData
        ];
    }

    public function generatePerformanceReport(array $params): array
    {
        $period = $params['period'];
        $departmentId = $params['department_id'] ?? null;

        $query = PerformanceEvaluation::with(['user.department', 'evaluator'])
            ->where('period', $period)
            ->whereHas('user', function($q) use ($departmentId) {
                $q->where('company_id', auth()->user()->company_id);
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            });

        $evaluations = $query->get();

        // Estadísticas generales
        $stats = [
            'total_evaluations' => $evaluations->count(),
            'completed_evaluations' => $evaluations->where('status', 'completed')->count(),
            'pending_evaluations' => $evaluations->whereIn('status', ['draft', 'submitted'])->count(),
            'average_score' => round($evaluations->where('overall_score', '>', 0)->avg('overall_score'), 2),
            'completion_rate' => $evaluations->count() > 0 ? 
                round(($evaluations->where('status', 'completed')->count() / $evaluations->count()) * 100, 2) : 0
        ];

        // Distribución de puntuaciones
        $scoreDistribution = [
            'excellent' => $evaluations->where('overall_score', '>=', 4.5)->count(),
            'good' => $evaluations->whereBetween('overall_score', [3.5, 4.49])->count(),
            'satisfactory' => $evaluations->whereBetween('overall_score', [2.5, 3.49])->count(),
            'needs_improvement' => $evaluations->where('overall_score', '<', 2.5)->where('overall_score', '>', 0)->count(),
            'not_evaluated' => $evaluations->where('overall_score', 0)->count()
        ];

        // Datos por empleado
        $employeeData = $evaluations->map(function($evaluation) {
            return [
                'employee_name' => $evaluation->user->full_name,
                'department' => $evaluation->user->department->name ?? 'Sin departamento',
                'evaluator' => $evaluation->evaluator->full_name,
                'overall_score' => $evaluation->overall_score,
                'status' => $evaluation->status_name,
                'evaluation_date' => $evaluation->evaluation_date->format('d/m/Y'),
                'competency_scores' => $evaluation->scores
            ];
        });

        // Análisis por competencias
        $competencyAnalysis = [];
        if ($evaluations->count() > 0) {
            $allScores = $evaluations->pluck('scores')->filter()->flatten(1);
            $competencies = $allScores->keys()->unique();
            
            foreach ($competencies as $competency) {
                $scores = $evaluations->pluck('scores')->filter()
                    ->map(function($scores) use ($competency) {
                        return $scores[$competency] ?? null;
                    })->filter();
                
                if ($scores->count() > 0) {
                    $competencyAnalysis[] = [
                        'competency' => $competency,
                        'average_score' => round($scores->avg(), 2),
                        'min_score' => $scores->min(),
                        'max_score' => $scores->max(),
                        'evaluations_count' => $scores->count()
                    ];
                }
            }
        }

        return [
            'period' => $period,
            'stats' => $stats,
            'score_distribution' => $scoreDistribution,
            'employee_data' => $employeeData,
            'competency_analysis' => $competencyAnalysis
        ];
    }

    public function generateRecruitmentReport(array $params): array
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);
        $departmentId = $params['department_id'] ?? null;

        $query = JobApplication::with(['jobPosting.department'])
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->whereHas('jobPosting', function($q) use ($departmentId) {
                $q->where('company_id', auth()->user()->company_id);
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            });

        $applications = $query->get();

        // Estadísticas generales
        $stats = [
            'total_applications' => $applications->count(),
            'hired_candidates' => $applications->where('status', 'hired')->count(),
            'rejected_candidates' => $applications->where('status', 'rejected')->count(),
            'in_process' => $applications->whereNotIn('status', ['hired', 'rejected'])->count(),
            'conversion_rate' => $applications->count() > 0 ? 
                round(($applications->where('status', 'hired')->count() / $applications->count()) * 100, 2) : 0,
            'average_time_to_hire' => $this->calculateAverageTimeToHire($applications->where('status', 'hired'))
        ];

        // Distribución por estado
        $statusDistribution = $applications->groupBy('status')->map(function($group, $status) {
            return [
                'status' => $status,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $group->count()) * 100, 2)
            ];
        })->values();

        // Datos por oferta laboral
        $jobPostingData = $applications->groupBy('jobPosting.title')->map(function($group, $title) {
            $hired = $group->where('status', 'hired')->count();
            return [
                'job_title' => $title,
                'department' => $group->first()->jobPosting->department->name ?? 'Sin departamento',
                'total_applications' => $group->count(),
                'hired' => $hired,
                'rejected' => $group->where('status', 'rejected')->count(),
                'conversion_rate' => $group->count() > 0 ? round(($hired / $group->count()) * 100, 2) : 0
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y')
            ],
            'stats' => $stats,
            'status_distribution' => $statusDistribution,
            'job_posting_data' => $jobPostingData
        ];
    }

    public function generateTrainingReport(array $params): array
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);
        $departmentId = $params['department_id'] ?? null;

        $query = TrainingEnrollment::with(['trainingProgram', 'user.department'])
            ->whereBetween('enrolled_at', [$startDate, $endDate])
            ->whereHas('user', function($q) use ($departmentId) {
                $q->where('company_id', auth()->user()->company_id);
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            });

        $enrollments = $query->get();

        // Estadísticas generales
        $stats = [
            'total_enrollments' => $enrollments->count(),
            'completed_trainings' => $enrollments->where('status', 'completed')->count(),
            'in_progress' => $enrollments->where('status', 'in_progress')->count(),
            'dropped_out' => $enrollments->where('status', 'dropped')->count(),
            'completion_rate' => $enrollments->count() > 0 ? 
                round(($enrollments->where('status', 'completed')->count() / $enrollments->count()) * 100, 2) : 0,
            'average_score' => round($enrollments->where('score', '>', 0)->avg('score'), 2),
            'certifications_earned' => $enrollments->where('certification_earned', true)->count()
        ];

        // Datos por programa
        $programData = $enrollments->groupBy('trainingProgram.title')->map(function($group, $title) {
            $completed = $group->where('status', 'completed')->count();
            return [
                'program_title' => $title,
                'category' => $group->first()->trainingProgram->category_name,
                'total_enrolled' => $group->count(),
                'completed' => $completed,
                'completion_rate' => $group->count() > 0 ? round(($completed / $group->count()) * 100, 2) : 0,
                'average_score' => round($group->where('score', '>', 0)->avg('score'), 2),
                'certifications' => $group->where('certification_earned', true)->count()
            ];
        })->values();

        // Datos por empleado
        $employeeData = $enrollments->groupBy('user_id')->map(function($group) {
            $user = $group->first()->user;
            return [
                'employee_name' => $user->full_name,
                'department' => $user->department->name ?? 'Sin departamento',
                'total_trainings' => $group->count(),
                'completed_trainings' => $group->where('status', 'completed')->count(),
                'average_score' => round($group->where('score', '>', 0)->avg('score'), 2),
                'certifications_earned' => $group->where('certification_earned', true)->count(),
                'total_hours' => $group->sum('trainingProgram.duration_hours')
            ];
        })->values();

        return [
            'period' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y')
            ],
            'stats' => $stats,
            'program_data' => $programData,
            'employee_data' => $employeeData
        ];
    }

    private function getWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    private function calculateAverageTimeToHire(Collection $hiredApplications): float
    {
        if ($hiredApplications->isEmpty()) {
            return 0;
        }

        $totalDays = $hiredApplications->sum(function($application) {
            return $application->applied_at->diffInDays($application->updated_at);
        });

        return round($totalDays / $hiredApplications->count(), 1);
    }
}
