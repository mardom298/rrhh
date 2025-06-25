<?php

namespace App\Services;

use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollConcept;
use App\Models\PayrollItem;
use App\Models\AttendanceRecord;
use App\Models\EmployeeLoan;
use Carbon\Carbon;

class PayrollCalculationService
{
    public function calculatePayroll(PayrollPeriod $period): array
    {
        $employees = User::where('company_id', $period->company_id)
            ->where('status', 'active')
            ->with(['attendanceRecords', 'employeeLoans'])
            ->get();

        $results = [];
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        foreach ($employees as $employee) {
            $calculation = $this->calculateEmployeePayroll($employee, $period);
            $results[] = $calculation;
            
            $totalGross += $calculation['gross_salary'];
            $totalDeductions += $calculation['total_deductions'];
            $totalNet += $calculation['net_salary'];
        }

        // Actualizar totales del período
        $period->update([
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
            'status' => 'calculated'
        ]);

        return $results;
    }

    public function calculateEmployeePayroll(User $employee, PayrollPeriod $period): array
    {
        $baseSalary = $employee->salary;
        $workingDays = $this->getWorkingDays($period->start_date, $period->end_date);
        $attendedDays = $this->getAttendedDays($employee, $period->start_date, $period->end_date);
        
        // Cálculos básicos
        $dailySalary = $baseSalary / 30; // Salario diario
        $hourlySalary = $dailySalary / 8; // Salario por hora
        
        // Obtener conceptos de nómina
        $concepts = PayrollConcept::where('company_id', $employee->company_id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        $incomes = [];
        $deductions = [];
        $contributions = [];

        foreach ($concepts as $concept) {
            $amount = $this->calculateConceptAmount($employee, $concept, $period, $baseSalary);
            
            if ($amount > 0) {
                $item = [
                    'concept_id' => $concept->id,
                    'concept_name' => $concept->name,
                    'amount' => $amount,
                    'calculation_details' => [
                        'base_salary' => $baseSalary,
                        'calculation_type' => $concept->calculation_type,
                        'rate' => $concept->value
                    ]
                ];

                match($concept->type) {
                    'income' => $incomes[] = $item,
                    'deduction' => $deductions[] = $item,
                    'contribution' => $contributions[] = $item
                };

                // Guardar item en base de datos
                PayrollItem::updateOrCreate([
                    'payroll_period_id' => $period->id,
                    'user_id' => $employee->id,
                    'payroll_concept_id' => $concept->id
                ], [
                    'quantity' => 1,
                    'rate' => $concept->value,
                    'amount' => $amount,
                    'calculation_details' => $item['calculation_details']
                ]);
            }
        }

        // Cálculos específicos peruanos
        $ctsAmount = $this->calculateCTS($employee, $period, $baseSalary);
        $gratificationAmount = $this->calculateGratification($employee, $period, $baseSalary);
        $vacationAmount = $this->calculateVacation($employee, $period, $baseSalary);

        // Descuentos por préstamos
        $loanDeduction = $this->calculateLoanDeductions($employee, $period);

        $grossSalary = $baseSalary + collect($incomes)->sum('amount');
        $totalDeductions = collect($deductions)->sum('amount') + $loanDeduction;
        $netSalary = $grossSalary - $totalDeductions;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'base_salary' => $baseSalary,
            'working_days' => $workingDays,
            'attended_days' => $attendedDays,
            'incomes' => $incomes,
            'deductions' => $deductions,
            'contributions' => $contributions,
            'cts_amount' => $ctsAmount,
            'gratification_amount' => $gratificationAmount,
            'vacation_amount' => $vacationAmount,
            'loan_deduction' => $loanDeduction,
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary
        ];
    }

    private function calculateConceptAmount(User $employee, PayrollConcept $concept, PayrollPeriod $period, float $baseSalary): float
    {
        return match($concept->code) {
            'SUELDO_BASICO' => $baseSalary,
            'ASIGNACION_FAMILIAR' => $this->hasChildren($employee) ? 102.50 : 0, // Monto 2024
            'HORAS_EXTRAS' => $this->calculateOvertimeHours($employee, $period) * ($baseSalary / 240 * 1.25),
            'ESSALUD' => $baseSalary * 0.09, // 9% EsSalud
            'ONP' => $baseSalary * 0.13, // 13% ONP
            'AFP_APORTE' => $baseSalary * 0.10, // 10% AFP (promedio)
            'AFP_COMISION' => $baseSalary * 0.0169, // Comisión AFP promedio
            'AFP_SEGURO' => $baseSalary * 0.0074, // Seguro AFP
            'QUINTA_CATEGORIA' => $this->calculateIncomeTax($employee, $baseSalary),
            default => $concept->calculateAmount($baseSalary)
        };
    }

    private function calculateCTS(User $employee, PayrollPeriod $period, float $baseSalary): float
    {
        // CTS se calcula semestralmente (Mayo y Noviembre)
        if (!in_array($period->start_date->month, [5, 11])) {
            return 0;
        }

        $monthsWorked = $this->getMonthsWorked($employee, $period);
        $ctsBase = $baseSalary + ($baseSalary / 6); // Incluye 1/6 de gratificación
        
        return ($ctsBase * $monthsWorked) / 12;
    }

    private function calculateGratification(User $employee, PayrollPeriod $period, float $baseSalary): float
    {
        // Gratificaciones en Julio y Diciembre
        if (!in_array($period->start_date->month, [7, 12])) {
            return 0;
        }

        $monthsWorked = min(6, $this->getMonthsWorked($employee, $period));
        return ($baseSalary * $monthsWorked) / 6;
    }

    private function calculateVacation(User $employee, PayrollPeriod $period, float $baseSalary): float
    {
        // Cálculo de vacaciones truncas o proporcionales
        $yearsWorked = $employee->years_of_service;
        if ($yearsWorked < 1) {
            return 0;
        }

        $vacationDays = 30; // 30 días por año en Perú
        $dailySalary = $baseSalary / 30;
        
        return $dailySalary * $vacationDays;
    }

    private function calculateLoanDeductions(User $employee, PayrollPeriod $period): float
    {
        return $employee->employeeLoans()
            ->where('status', 'active')
            ->sum('monthly_payment');
    }

    private function calculateOvertimeHours(User $employee, PayrollPeriod $period): float
    {
        return AttendanceRecord::where('user_id', $employee->id)
            ->whereBetween('date', [$period->start_date, $period->end_date])
            ->sum('overtime_hours') / 60; // Convertir minutos a horas
    }

    private function calculateIncomeTax(User $employee, float $baseSalary): float
    {
        // Cálculo simplificado del impuesto a la renta de quinta categoría
        $annualSalary = $baseSalary * 12;
        $uit = 5150; // UIT 2024
        $exemptAmount = 7 * $uit; // 7 UIT exentas

        if ($annualSalary <= $exemptAmount) {
            return 0;
        }

        $taxableAmount = $annualSalary - $exemptAmount;
        $monthlyTaxableAmount = $taxableAmount / 12;

        // Escala progresiva simplificada
        if ($monthlyTaxableAmount <= 5 * $uit / 12) {
            return $monthlyTaxableAmount * 0.08;
        } elseif ($monthlyTaxableAmount <= 20 * $uit / 12) {
            return ($monthlyTaxableAmount * 0.14) - (5 * $uit / 12 * 0.06);
        } elseif ($monthlyTaxableAmount <= 35 * $uit / 12) {
            return ($monthlyTaxableAmount * 0.17) - (20 * $uit / 12 * 0.03) - (5 * $uit / 12 * 0.06);
        } else {
            return ($monthlyTaxableAmount * 0.20) - (35 * $uit / 12 * 0.03) - (20 * $uit / 12 * 0.03) - (5 * $uit / 12 * 0.06);
        }
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

    private function getAttendedDays(User $employee, Carbon $startDate, Carbon $endDate): int
    {
        return AttendanceRecord::where('user_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();
    }

    private function getMonthsWorked(User $employee, PayrollPeriod $period): int
    {
        $hireDate = $employee->hire_date;
        $periodStart = $period->start_date;
        
        return $hireDate->diffInMonths($periodStart);
    }

    private function hasChildren(User $employee): bool
    {
        // En un sistema real, esto vendría de una tabla de dependientes
        // Por ahora retornamos false como ejemplo
        return false;
    }
}
