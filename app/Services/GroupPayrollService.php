<?php

namespace App\Services;

use App\Models\BusinessGroup;
use App\Models\Company;
use App\Models\PayrollPeriod;
use App\Models\EmployeeCompany;
use App\Models\PayrollItem;
use Carbon\Carbon;

class GroupPayrollService
{
    public function createGroupPayrollPeriod(BusinessGroup $group, array $params): PayrollPeriod
    {
        // Crear período maestro para el grupo
        $masterPeriod = PayrollPeriod::create([
            'company_id' => null, // Período del grupo
            'business_group_id' => $group->id,
            'name' => $params['name'] . ' - Consolidado Grupo',
            'period_code' => $params['period_code'] . '-GROUP',
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
            'payment_date' => $params['payment_date'],
            'type' => $params['type'],
            'consolidation_status' => 'group_consolidated',
            'status' => 'draft'
        ]);

        // Crear períodos individuales para cada empresa
        foreach ($group->companies()->where('active', true)->get() as $company) {
            PayrollPeriod::create([
                'company_id' => $company->id,
                'name' => $params['name'] . ' - ' . $company->name,
                'period_code' => $params['period_code'] . '-' . $company->company_code,
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
                'payment_date' => $params['payment_date'],
                'type' => $params['type'],
                'consolidation_status' => 'individual',
                'parent_period_id' => $masterPeriod->id,
                'status' => 'draft'
            ]);
        }

        return $masterPeriod;
    }

    public function calculateGroupPayroll(PayrollPeriod $masterPeriod): array
    {
        $results = [];
        $groupTotals = [
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'total_employees' => 0
        ];

        // Calcular nómina para cada empresa del grupo
        $companyPeriods = PayrollPeriod::where('parent_period_id', $masterPeriod->id)->get();
        
        foreach ($companyPeriods as $period) {
            $payrollService = new PayrollCalculationService();
            $companyResult = $payrollService->calculatePayroll($period);
            
            $results[$period->company->company_code] = [
                'company' => $period->company,
                'period' => $period,
                'results' => $companyResult,
                'totals' => [
                    'gross' => $period->fresh()->total_gross,
                    'deductions' => $period->fresh()->total_deductions,
                    'net' => $period->fresh()->total_net,
                    'employees' => count($companyResult)
                ]
            ];

            // Acumular totales del grupo
            $groupTotals['total_gross'] += $period->fresh()->total_gross;
            $groupTotals['total_deductions'] += $period->fresh()->total_deductions;
            $groupTotals['total_net'] += $period->fresh()->total_net;
            $groupTotals['total_employees'] += count($companyResult);
        }

        // Actualizar período maestro con totales consolidados
        $masterPeriod->update([
            'total_gross' => $groupTotals['total_gross'],
            'total_deductions' => $groupTotals['total_deductions'],
            'total_net' => $groupTotals['total_net'],
            'consolidation_data' => $results,
            'status' => 'calculated'
        ]);

        return [
            'master_period' => $masterPeriod,
            'group_totals' => $groupTotals,
            'company_results' => $results
        ];
    }

    public function getEmployeeConsolidatedPayroll($userId, $periodId): array
    {
        $user = User::find($userId);
        $masterPeriod = PayrollPeriod::find($periodId);
        
        $employeeData = [];
        $totalSalary = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        // Obtener datos de nómina de todas las empresas donde trabaja el empleado
        foreach ($user->activeEmployeeCompanies as $employeeCompany) {
            $companyPeriod = PayrollPeriod::where('parent_period_id', $masterPeriod->id)
                ->where('company_id', $employeeCompany->company_id)
                ->first();

            if ($companyPeriod) {
                $payrollItems = PayrollItem::where('payroll_period_id', $companyPeriod->id)
                    ->where('user_id', $userId)
                    ->with('payrollConcept')
                    ->get();

                $companyGross = $payrollItems->where('payrollConcept.type', 'income')->sum('amount');
                $companyDeductions = $payrollItems->where('payrollConcept.type', 'deduction')->sum('amount');
                $companyNet = $companyGross - $companyDeductions;

                $employeeData[] = [
                    'company' => $employeeCompany->company,
                    'employee_code' => $employeeCompany->employee_code,
                    'base_salary' => $employeeCompany->base_salary,
                    'gross_salary' => $companyGross,
                    'deductions' => $companyDeductions,
                    'net_salary' => $companyNet,
                    'items' => $payrollItems
                ];

                $totalSalary += $companyGross;
                $totalDeductions += $companyDeductions;
                $totalNet += $companyNet;
            }
        }

        return [
            'employee' => $user,
            'period' => $masterPeriod,
            'company_data' => $employeeData,
            'consolidated_totals' => [
                'total_gross' => $totalSalary,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet
            ]
        ];
    }

    public function generateGroupReport(BusinessGroup $group, array $params): array
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);

        // Estadísticas del grupo
        $groupStats = [
            'total_companies' => $group->companies()->where('active', true)->count(),
            'total_employees' => $group->users()->count(),
            'active_employees' => EmployeeCompany::whereHas('company', function($q) use ($group) {
                $q->where('business_group_id', $group->id);
            })->where('status', 'active')->count(),
            'total_payroll' => $group->total_payroll,
            'average_salary' => $group->users()->count() > 0 ? 
                round($group->total_payroll / $group->users()->count(), 2) : 0
        ];

        // Datos por empresa
        $companyData = $group->companies()->where('active', true)->get()->map(function($company) {
            return [
                'company_name' => $company->name,
                'company_code' => $company->company_code,
                'company_type' => $company->company_type_name,
                'employees_count' => $company->active_employees_count,
                'total_payroll' => $company->total_payroll,
                'average_salary' => $company->active_employees_count > 0 ? 
                    round($company->total_payroll / $company->active_employees_count, 2) : 0
            ];
        });

        // Empleados multi-empresa
        $multiCompanyEmployees = User::where('business_group_id', $group->id)
            ->whereHas('employeeCompanies', function($q) {
                $q->where('status', 'active');
            }, '>', 1)
            ->with(['employeeCompanies.company'])
            ->get()
            ->map(function($user) {
                return [
                    'employee_name' => $user->full_name,
                    'global_id' => $user->global_employee_id,
                    'companies' => $user->activeEmployeeCompanies->map(function($ec) {
                        return [
                            'company_name' => $ec->company->name,
                            'employee_code' => $ec->employee_code,
                            'position' => $ec->position->title ?? 'Sin puesto',
                            'salary' => $ec->total_salary,
                            'is_primary' => $ec->is_primary_company
                        ];
                    }),
                    'total_salary' => $user->total_salary
                ];
            });

        return [
            'group' => $group,
            'period' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y')
            ],
            'group_stats' => $groupStats,
            'company_data' => $companyData,
            'multi_company_employees' => $multiCompanyEmployees
        ];
    }
}
