<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Company;
use App\Models\EmployeeCompany;
use App\Models\Department;
use App\Models\Position;

class MultiCompanyEmployeeManagement extends Component
{
    use WithPagination;

    public $selectedEmployee = null;
    public $showModal = false;
    public $showAddCompanyModal = false;
    public $search = '';
    public $selectedCompany = '';
    public $filterMultiCompany = false;

    // Formulario para agregar empleado a empresa
    public $newCompanyId = '';
    public $newEmployeeCode = '';
    public $newDepartmentId = '';
    public $newPositionId = '';
    public $newBaseSalary = '';
    public $newContractType = 'indefinido';
    public $newHireDate = '';
    public $isPrimaryCompany = false;

    protected $rules = [
        'newCompanyId' => 'required|exists:companies,id',
        'newEmployeeCode' => 'required|string|max:50',
        'newDepartmentId' => 'nullable|exists:departments,id',
        'newPositionId' => 'nullable|exists:positions,id',
        'newBaseSalary' => 'required|numeric|min:0',
        'newContractType' => 'required|in:indefinido,plazo_fijo,part_time,practicas,consultor',
        'newHireDate' => 'required|date',
    ];

    public function render()
    {
        $query = User::with(['businessGroup', 'employeeCompanies.company', 'employeeCompanies.department', 'employeeCompanies.position'])
            ->where('business_group_id', auth()->user()->business_group_id);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('dni', 'like', '%' . $this->search . '%')
                  ->orWhere('global_employee_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCompany) {
            $query->whereHas('employeeCompanies', function($q) {
                $q->where('company_id', $this->selectedCompany);
            });
        }

        if ($this->filterMultiCompany) {
            $query->whereHas('employeeCompanies', function($q) {
                $q->where('status', 'active');
            }, '>', 1);
        }

        $employees = $query->paginate(15);

        $companies = Company::where('business_group_id', auth()->user()->business_group_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $departments = $this->newCompanyId ? 
            Department::where('company_id', $this->newCompanyId)->get() : 
            collect();

        $positions = $this->newDepartmentId ? 
            Position::where('department_id', $this->newDepartmentId)->get() : 
            collect();

        return view('livewire.multi-company-employee-management', compact(
            'employees', 
            'companies', 
            'departments', 
            'positions'
        ));
    }

    public function updatedNewCompanyId()
    {
        $this->newDepartmentId = '';
        $this->newPositionId = '';
        
        if ($this->newCompanyId) {
            $company = Company::find($this->newCompanyId);
            $this->newEmployeeCode = $company->company_code . '-' . str_pad($this->selectedEmployee->id, 4, '0', STR_PAD_LEFT);
        }
    }

    public function updatedNewDepartmentId()
    {
        $this->newPositionId = '';
    }

    public function viewEmployee($employeeId)
    {
        $this->selectedEmployee = User::with([
            'employeeCompanies.company',
            'employeeCompanies.department',
            'employeeCompanies.position',
            'employeeCompanies.manager'
        ])->find($employeeId);
        
        $this->showModal = true;
    }

    public function addCompanyToEmployee($employeeId)
    {
        $this->selectedEmployee = User::find($employeeId);
        $this->resetAddCompanyForm();
        $this->showAddCompanyModal = true;
    }

    public function saveEmployeeCompany()
    {
        $this->validate();

        // Verificar que no exista ya esta relación
        $exists = EmployeeCompany::where('user_id', $this->selectedEmployee->id)
            ->where('company_id', $this->newCompanyId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'El empleado ya está asignado a esta empresa.');
            return;
        }

        // Verificar código único por empresa
        $codeExists = EmployeeCompany::where('company_id', $this->newCompanyId)
            ->where('employee_code', $this->newEmployeeCode)
            ->exists();

        if ($codeExists) {
            session()->flash('error', 'El código de empleado ya existe en esta empresa.');
            return;
        }

        // Si es empresa principal, quitar la marca de las otras
        if ($this->isPrimaryCompany) {
            EmployeeCompany::where('user_id', $this->selectedEmployee->id)
                ->update(['is_primary_company' => false]);
        }

        EmployeeCompany::create([
            'user_id' => $this->selectedEmployee->id,
            'company_id' => $this->newCompanyId,
            'employee_code' => $this->newEmployeeCode,
            'department_id' => $this->newDepartmentId ?: null,
            'position_id' => $this->newPositionId ?: null,
            'hire_date' => $this->newHireDate,
            'status' => 'active',
            'base_salary' => $this->newBaseSalary,
            'contract_type' => $this->newContractType,
            'payroll_frequency' => 'monthly',
            'is_primary_company' => $this->isPrimaryCompany,
            'benefits' => [
                'seguro_salud' => true,
                'seguro_vida' => $this->newBaseSalary > 3000
            ]
        ]);

        session()->flash('message', 'Empleado agregado a la empresa exitosamente.');
        $this->showAddCompanyModal = false;
        $this->resetAddCompanyForm();
    }

    public function removeEmployeeFromCompany($employeeCompanyId)
    {
        $employeeCompany = EmployeeCompany::find($employeeCompanyId);
        
        if ($employeeCompany) {
            $employeeCompany->update([
                'status' => 'terminated',
                'termination_date' => now()
            ]);
            
            session()->flash('message', 'Empleado removido de la empresa.');
        }
    }

    public function setPrimaryCompany($employeeCompanyId)
    {
        $employeeCompany = EmployeeCompany::find($employeeCompanyId);
        
        if ($employeeCompany) {
            // Quitar marca principal de todas las empresas del empleado
            EmployeeCompany::where('user_id', $employeeCompany->user_id)
                ->update(['is_primary_company' => false]);
            
            // Marcar como principal
            $employeeCompany->update(['is_primary_company' => true]);
            
            session()->flash('message', 'Empresa principal actualizada.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedEmployee = null;
    }

    public function closeAddCompanyModal()
    {
        $this->showAddCompanyModal = false;
        $this->resetAddCompanyForm();
    }

    private function resetAddCompanyForm()
    {
        $this->newCompanyId = '';
        $this->newEmployeeCode = '';
        $this->newDepartmentId = '';
        $this->newPositionId = '';
        $this->newBaseSalary = '';
        $this->newContractType = 'indefinido';
        $this->newHireDate = now()->format('Y-m-d');
        $this->isPrimaryCompany = false;
    }
}
