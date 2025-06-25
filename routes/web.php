<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Livewire\EmployeeManagement;
use App\Livewire\PayrollManagement;
use App\Livewire\PerformanceEvaluations;
use App\Livewire\RecruitmentManagement;
use App\Livewire\ReportsManagement;
use App\Livewire\GroupDashboard;
use App\Livewire\MultiCompanyEmployeeManagement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas del Sistema de RRHH
    Route::prefix('rrhh')->group(function () {
        // Dashboard del Grupo
        Route::get('/group-dashboard', GroupDashboard::class)->name('rrhh.group-dashboard');
        
        // Gestión de Empleados
        Route::get('/employees', EmployeeManagement::class)->name('rrhh.employees');
        Route::get('/multi-company-employees', MultiCompanyEmployeeManagement::class)->name('rrhh.multi-company-employees');
        
        // Nómina
        Route::get('/payroll', PayrollManagement::class)->name('rrhh.payroll');
        
        // Evaluaciones de Desempeño
        Route::get('/evaluations', PerformanceEvaluations::class)->name('rrhh.evaluations');
        
        // Reclutamiento
        Route::get('/recruitment', RecruitmentManagement::class)->name('rrhh.recruitment');
        
        // Reportes
        Route::get('/reports', ReportsManagement::class)->name('rrhh.reports');
    });
});

require __DIR__.'/auth.php';
