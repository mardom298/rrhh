<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'employee_code',
        'department_id',
        'position_id',
        'manager_id',
        'hire_date',
        'termination_date',
        'status',
        'base_salary',
        'salary_components',
        'contract_type',
        'payroll_frequency',
        'cost_center',
        'is_primary_company',
        'benefits'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'base_salary' => 'decimal:2',
        'salary_components' => 'array',
        'benefits' => 'array',
        'is_primary_company' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'user_id', 'user_id')
            ->whereHas('user.employeeCompanies', function($query) {
                $query->where('company_id', $this->company_id);
            });
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'user_id', 'user_id')
            ->whereHas('payrollPeriod', function($query) {
                $query->where('company_id', $this->company_id);
            });
    }

    public function getTotalSalaryAttribute(): float
    {
        $total = $this->base_salary;
        
        if ($this->salary_components) {
            foreach ($this->salary_components as $component) {
                $total += $component['amount'] ?? 0;
            }
        }
        
        return $total;
    }

    public function getYearsOfServiceAttribute(): int
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : 0;
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'terminated' => 'Terminado',
            'suspended' => 'Suspendido'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getContractTypeNameAttribute(): string
    {
        $types = [
            'indefinido' => 'Indefinido',
            'plazo_fijo' => 'Plazo Fijo',
            'part_time' => 'Medio Tiempo',
            'practicas' => 'Prácticas',
            'consultor' => 'Consultor'
        ];

        return $types[$this->contract_type] ?? $this->contract_type;
    }
}
