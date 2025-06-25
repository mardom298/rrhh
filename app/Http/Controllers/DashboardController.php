<?php

namespace App\Http\Controllers;

use App\Models\BusinessGroup;
use App\Models\Company;
use App\Models\User;
use App\Models\EmployeeCompany;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $businessGroup = BusinessGroup::with(['companies', 'users'])->first();
        
        $stats = [
            'total_companies' => Company::where('status', 'active')->count(),
            'total_employees' => User::count(),
            'active_employees' => EmployeeCompany::where('status', 'active')->count(),
            'total_departments' => \App\Models\Department::where('status', 'active')->count(),
        ];

        $recentEmployees = User::with(['businessGroup', 'activeEmployeeCompanies.company'])
            ->latest()
            ->take(5)
            ->get();

        $companiesData = Company::with(['activeEmployees'])
            ->where('status', 'active')
            ->get()
            ->map(function ($company) {
                return [
                    'name' => $company->name,
                    'employees_count' => $company->activeEmployees->count(),
                    'total_salary' => $company->activeEmployees->sum('total_salary')
                ];
            });

        return view('dashboard', compact('businessGroup', 'stats', 'recentEmployees', 'companiesData'));
    }
}
