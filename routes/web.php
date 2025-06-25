<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/employees', function () {
    $employees = \App\Models\User::with(['businessGroup', 'activeEmployeeCompanies.company', 'activeEmployeeCompanies.department', 'activeEmployeeCompanies.position'])
        ->paginate(10);
    
    return view('employees.index', compact('employees'));
})->name('employees.index');

Route::get('/companies', function () {
    $companies = \App\Models\Company::with(['businessGroup', 'activeEmployees', 'departments'])
        ->where('status', 'active')
        ->paginate(10);
    
    return view('companies.index', compact('companies'));
})->name('companies.index');
