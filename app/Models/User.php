<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'business_group_id',
        'global_employee_id',
        'dni',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employee_type',
        'bank_account',
        'avatar',
        'certifications',
        'skills',
        'tax_id',
        'settings'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'certifications' => 'array',
        'skills' => 'array',
        'settings' => 'array'
    ];

    public function businessGroup(): BelongsTo
    {
        return $this->belongsTo(BusinessGroup::class);
    }

    public function employeeCompanies(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class);
    }

    public function activeEmployeeCompanies(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class)->where('status', 'active');
    }

    public function primaryCompany(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class)->where('is_primary_company', true);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    public function getCompaniesListAttribute(): string
    {
        return $this->activeEmployeeCompanies->pluck('company.name')->join(', ');
    }

    public function getPrimaryCompanyNameAttribute(): string
    {
        $primary = $this->primaryCompany->first();
        return $primary ? $primary->company->name : 'Sin empresa principal';
    }

    public function getTotalSalaryAttribute(): float
    {
        return $this->activeEmployeeCompanies->sum('total_salary');
    }

    public function getEmployeeTypeNameAttribute(): string
    {
        $types = [
            'permanent' => 'Permanente',
            'temporary' => 'Temporal',
            'consultant' => 'Consultor',
            'intern' => 'Practicante'
        ];

        return $types[$this->employee_type] ?? $this->employee_type;
    }

    // Método para obtener información de empleado en una empresa específica
    public function getEmployeeDataForCompany($companyId): ?EmployeeCompany
    {
        return $this->employeeCompanies()->where('company_id', $companyId)->first();
    }

    // Método para verificar si el usuario tiene acceso a una empresa
    public function hasAccessToCompany($companyId): bool
    {
        return $this->employeeCompanies()->where('company_id', $companyId)->exists();
    }
}
