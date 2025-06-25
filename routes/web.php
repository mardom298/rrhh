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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
