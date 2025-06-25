<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BusinessGroup;
use App\Models\Company;
use App\Models\User;
use App\Models\EmployeeCompany;
use App\Models\PayrollPeriod;

class GroupDashboard extends Component
{
    public $selectedGroup;
    public $selectedCompany = '';
    public $viewMode = 'group'; // group, company, employee

    public function mount()
    {
        // Obtener el grupo del usuario actual
        $this->selectedGroup = auth()->user()->businessGroup;
    }

    public function render()
    {
        $groupStats = $this->getGroupStatistics();
        $companyStats = $this->getCompanyStatistics();
        $recentActivity = $this->getRecentActivity();
        $multiCompanyEmployees = $this->getMultiCompanyEmployees();

        $companies = $this->selectedGroup->companies()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.group-dashboard', compact(
            'groupStats',
            'companyStats', 
            'recentActivity',
            'multiCompanyEmployees',
            'companies'
        ));
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function selectCompany($companyId)
    {
        $this->selectedCompany = $companyId;
        $this->viewMode = 'company';
    }

    private function getGroupStatistics(): array
    {
        return [
            'total_companies' => $this->selectedGroup->companies()->where('active', true)->count(),
            'total_employees' => $this->selectedGroup->users()->count(),
            'active_employees' => EmployeeCompany::whereHas('company', function($q) {
                $q->where('business_group_id', $this->selectedGroup->id);
            })->where('status', 'active')->count(),
            'total_payroll' => $this->selectedGroup->total_payroll,
            'multi_company_employees' => User::where('business_group_id', $this->selectedGroup->id)
                ->whereHas('employeeCompanies', function($q) {
                    $q->where('status', 'active');
                }, '>', 1)->count(),
            'pending_payrolls' => PayrollPeriod::whereHas('company', function($q) {
                $q->where('business_group_id', $this->selectedGroup->id);
            })->where('status', 'draft')->count()
        ];
    }

    private function getCompanyStatistics(): array
    {
        return $this->selectedGroup->companies()
            ->where('active', true)
            ->get()
            ->map(function($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->company_code,
                    'type' => $company->company_type_name,
                    'employees' => $company->active_employees_count,
                    'payroll' => $company->total_payroll,
                    'departments' => $company->departments()->count(),
                    'recent_hires' => EmployeeCompany::where('company_id', $company->id)
                        ->where('hire_date', '>=', now()->subDays(30))
                        ->count()
                ];
            })->toArray();
    }

    private function getRecentActivity(): array
    {
        $activities = [];

        // Nuevas contrataciones
        $recentHires = EmployeeCompany::with(['user', 'company'])
            ->whereHas('company', function($q) {
                $q->where('business_group_id', $this->selectedGroup->id);
            })
            ->where('hire_date', '>=', now()->subDays(7))
            ->orderBy('hire_date', 'desc')
            ->take(5)
            ->get();

        foreach ($recentHires as $hire) {
            $activities[] = [
                'type' => 'hire',
                'message' => "Nuevo empleado: {$hire->user->full_name} en {$hire->company->name}",
                'date' => $hire->hire_date,
                'icon' => 'user-plus',
                'color' => 'green'
            ];
        }

        // Nóminas procesadas
        $recentPayrolls = PayrollPeriod::with('company')
            ->whereHas('company', function($q) {
                $q->where('business_group_id', $this->selectedGroup->id);
            })
            ->where('updated_at', '>=', now()->subDays(7))
            ->where('status', 'calculated')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentPayrolls as $payroll) {
            $activities[] = [
                'type' => 'payroll',
                'message' => "Nómina procesada: {$payroll->name} - {$payroll->company->name}",
                'date' => $payroll->updated_at,
                'icon' => 'currency-dollar',
                'color' => 'blue'
            ];
        }

        // Ordenar por fecha
        usort($activities, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    private function getMultiCompanyEmployees(): array
    {
        return User::where('business_group_id', $this->selectedGroup->id)
            ->whereHas('employeeCompanies', function($q) {
                $q->where('status', 'active');
            }, '>', 1)
            ->with(['employeeCompanies.company', 'employeeCompanies.position'])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'global_id' => $user->global_employee_id,
                    'companies_count' => $user->activeEmployeeCompanies->count(),
                    'companies' => $user->activeEmployeeCompanies->map(function($ec) {
                        return [
                            'name' => $ec->company->name,
                            'code' => $ec->employee_code,
                            'position' => $ec->position->title ?? 'Sin puesto',
                            'is_primary' => $ec->is_primary_company
                        ];
                    }),
                    'total_salary' => $user->total_salary
                ];
            })->toArray();
    }
}
