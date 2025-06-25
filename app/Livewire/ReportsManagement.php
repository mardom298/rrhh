<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ReportService;
use App\Models\Department;
use App\Models\PayrollPeriod;
use Carbon\Carbon;

class ReportsManagement extends Component
{
    public $reportType = 'attendance';
    public $reportData = null;
    public $isLoading = false;

    // Parámetros comunes
    public $start_date = '';
    public $end_date = '';
    public $department_id = '';

    // Parámetros específicos
    public $period_id = ''; // Para reportes de nómina
    public $evaluation_period = ''; // Para reportes de evaluación

    public function mount()
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
        $this->evaluation_period = now()->format('Y-Q\Q');
    }

    public function render()
    {
        $departments = Department::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $payrollPeriods = PayrollPeriod::where('company_id', auth()->user()->company_id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('livewire.reports-management', compact('departments', 'payrollPeriods'));
    }

    public function generateReport()
    {
        $this->isLoading = true;
        $this->reportData = null;

        try {
            $reportService = new ReportService();
            
            $params = [
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'department_id' => $this->department_id ?: null,
            ];

            switch ($this->reportType) {
                case 'attendance':
                    $this->reportData = $reportService->generateAttendanceReport($params);
                    break;
                    
                case 'payroll':
                    if (!$this->period_id) {
                        session()->flash('error', 'Debe seleccionar un período de nómina.');
                        return;
                    }
                    $params['period_id'] = $this->period_id;
                    $this->reportData = $reportService->generatePayrollReport($params);
                    break;
                    
                case 'performance':
                    $params['period'] = $this->evaluation_period;
                    $this->reportData = $reportService->generatePerformanceReport($params);
                    break;
                    
                case 'recruitment':
                    $this->reportData = $reportService->generateRecruitmentReport($params);
                    break;
                    
                case 'training':
                    $this->reportData = $reportService->generateTrainingReport($params);
                    break;
                    
                default:
                    session()->flash('error', 'Tipo de reporte no válido.');
                    return;
            }

            session()->flash('message', 'Reporte generado exitosamente.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function exportReport($format = 'pdf')
    {
        if (!$this->reportData) {
            session()->flash('error', 'Debe generar el reporte primero.');
            return;
        }

        // Aquí implementarías la lógica de exportación
        // Por ejemplo, usando DomPDF para PDF o PhpSpreadsheet para Excel
        session()->flash('message', "Reporte exportado en formato {$format}.");
    }

    public function clearReport()
    {
        $this->reportData = null;
    }

    public function updatedReportType()
    {
        $this->clearReport();
    }
}
