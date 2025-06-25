<?php

namespace App\Jobs;

use App\Models\PayrollPeriod;
use App\Services\PayrollCalculationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private PayrollPeriod $payrollPeriod
    ) {}

    public function handle(PayrollCalculationService $payrollService): void
    {
        try {
            Log::info("Iniciando cálculo de nómina para período: {$this->payrollPeriod->name}");
            
            $results = $payrollService->calculatePayroll($this->payrollPeriod);
            
            Log::info("Nómina calculada exitosamente", [
                'period_id' => $this->payrollPeriod->id,
                'employees_processed' => count($results),
                'total_gross' => $this->payrollPeriod->fresh()->total_gross
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al calcular nómina", [
                'period_id' => $this->payrollPeriod->id,
                'error' => $e->getMessage()
            ]);
            
            $this->payrollPeriod->update(['status' => 'draft']);
            throw $e;
        }
    }
}
