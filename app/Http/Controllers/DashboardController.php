<?php

namespace App\Http\Controllers;

use App\Models\BusinessGroup;
use App\Models\Company;
use App\Models\User;
use App\Models\EmployeeCompany;
use App\Models\Department;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::where('status', 'active')->count(),
            'total_employees' => User::count(),
            'active_employees' => EmployeeCompany::where('status', 'active')->count(),
            'business_group' => BusinessGroup::first()
        ];

        return view('dashboard', compact('stats'));
    }
}
