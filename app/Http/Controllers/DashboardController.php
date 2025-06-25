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
        $user = auth()->user();
        $company = $user->company;

        // Métricas principales
        $totalEmployees = $company->users()->where('status', 'active')->count();
        $presentToday = AttendanceRecord::whereDate('date', today())
            ->whereHas('user', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->where('status', 'present')
            ->count();

        $pendingLeaves = LeaveRequest::whereHas('user', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->where('status', 'pending')
            ->count();

        $birthdaysThisMonth = $company->users()
            ->where('status', 'active')
            ->whereMonth('birth_date', now()->month)
            ->count();

        // Gráfico de asistencia de la semana
        $weeklyAttendance = $this->getWeeklyAttendanceData($company->id);

        // Distribución por departamentos
        $departmentDistribution = $this->getDepartmentDistribution($company->id);

        // Solicitudes de licencia recientes
        $recentLeaveRequests = LeaveRequest::with(['user', 'approver'])
            ->whereHas('user', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->latest()
            ->take(5)
            ->get();

        // Empleados con cumpleaños próximos
        $upcomingBirthdays = $company->users()
            ->where('status', 'active')
            ->whereRaw('DAYOFYEAR(birth_date) >= DAYOFYEAR(CURDATE())')
            ->whereRaw('DAYOFYEAR(birth_date) <= DAYOFYEAR(DATE_ADD(CURDATE(), INTERVAL 7 DAY))')
            ->orderByRaw('DAYOFYEAR(birth_date)')
            ->take(5)
            ->get();

        // Estadísticas generales
        $stats = [
            'total_companies' => Company::count(),
            'total_employees' => User::count(),
            'active_employees' => EmployeeCompany::where('status', 'active')->count(),
            'business_groups' => BusinessGroup::count(),
        ];

        return view('dashboard', compact(
            'totalEmployees',
            'presentToday',
            'pendingLeaves',
            'birthdaysThisMonth',
            'weeklyAttendance',
            'departmentDistribution',
            'recentLeaveRequests',
            'upcomingBirthdays',
            'stats'
        ));
    }

    private function getWeeklyAttendanceData($companyId)
    {
        $startOfWeek = now()->startOfWeek();
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $present = AttendanceRecord::whereDate('date', $date)
                ->whereHas('user', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->where('status', 'present')
                ->count();

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'present' => $present
            ];
        }

        return $data;
    }

    private function getDepartmentDistribution($companyId)
    {
        return Department::where('company_id', $companyId)
            ->withCount(['users' => function($query) {
                $query->where('status', 'active');
            }])
            ->having('users_count', '>', 0)
            ->get()
            ->map(function($department) {
                return [
                    'name' => $department->name,
                    'count' => $department->users_count
                ];
            });
    }
}
