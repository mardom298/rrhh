<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;

class EmployeeManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $departmentFilter = '';
    public $statusFilter = '';
    public $showModal = false;
    public $editingEmployee = null;

    // Propiedades del formulario
    public $employee_code = '';
    public $dni = '';
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $birth_date = '';
    public $gender = '';
    public $address = '';
    public $department_id = '';
    public $position_id = '';
    public $manager_id = '';
    public $hire_date = '';
    public $salary = '';
    public $status = 'active';

    protected $rules = [
        'employee_code' => 'required|string|max:20',
        'dni' => 'required|string|size:8',
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|in:M,F,O',
        'address' => 'nullable|string|max:255',
        'department_id' => 'nullable|exists:departments,id',
        'position_id' => 'nullable|exists:positions,id',
        'manager_id' => 'nullable|exists:users,id',
        'hire_date' => 'nullable|date',
        'salary' => 'nullable|numeric|min:0',
        'status' => 'required|in:active,inactive,terminated'
    ];

    public function render()
    {
        $employees = User::with(['department', 'position', 'manager'])
            ->where('company_id', auth()->user()->company_id)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('employee_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->departmentFilter, function($query) {
                $query->where('department_id', $this->departmentFilter);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('first_name')
            ->paginate(10);

        $departments = Department::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('title')
            ->get();

        $managers = User::where('company_id', auth()->user()->company_id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.employee-management', compact(
            'employees', 
            'departments', 
            'positions', 
            'managers'
        ));
    }

    public function openModal($employeeId = null)
    {
        $this->resetForm();
        
        if ($employeeId) {
            $this->editingEmployee = User::find($employeeId);
            $this->fillForm();
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingEmployee = null;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'company_id' => auth()->user()->company_id,
            'employee_code' => $this->employee_code,
            'dni' => $this->dni,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'address' => $this->address,
            'department_id' => $this->department_id ?: null,
            'position_id' => $this->position_id ?: null,
            'manager_id' => $this->manager_id ?: null,
            'hire_date' => $this->hire_date,
            'salary' => $this->salary,
            'status' => $this->status,
        ];

        if ($this->editingEmployee) {
            $this->editingEmployee->update($data);
            session()->flash('message', 'Empleado actualizado exitosamente.');
        } else {
            $data['password'] = bcrypt('password123'); // Contraseña temporal
            User::create($data);
            session()->flash('message', 'Empleado creado exitosamente.');
        }

        $this->closeModal();
    }

    private function resetForm()
    {
        $this->employee_code = '';
        $this->dni = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->phone = '';
        $this->birth_date = '';
        $this->gender = '';
        $this->address = '';
        $this->department_id = '';
        $this->position_id = '';
        $this->manager_id = '';
        $this->hire_date = '';
        $this->salary = '';
        $this->status = 'active';
    }

    private function fillForm()
    {
        $this->employee_code = $this->editingEmployee->employee_code;
        $this->dni = $this->editingEmployee->dni;
        $this->first_name = $this->editingEmployee->first_name;
        $this->last_name = $this->editingEmployee->last_name;
        $this->email = $this->editingEmployee->email;
        $this->phone = $this->editingEmployee->phone;
        $this->birth_date = $this->editingEmployee->birth_date?->format('Y-m-d');
        $this->gender = $this->editingEmployee->gender;
        $this->address = $this->editingEmployee->address;
        $this->department_id = $this->editingEmployee->department_id;
        $this->position_id = $this->editingEmployee->position_id;
        $this->manager_id = $this->editingEmployee->manager_id;
        $this->hire_date = $this->editingEmployee->hire_date?->format('Y-m-d');
        $this->salary = $this->editingEmployee->salary;
        $this->status = $this->editingEmployee->status;
    }
}
