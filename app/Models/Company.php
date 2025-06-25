<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_group_id',
        'name',
        'company_code',
        'ruc',
        'company_type',
        'address',
        'phone',
        'email',
        'logo',
        'legal_representative',
        'economic_activity',
        'settings',
        'tax_settings',
        'active'
    ];

    protected $casts = [
        'settings' => 'array',
        'tax_settings' => 'array',
        'active' => 'boolean'
    ];

    public function businessGroup(): BelongsTo
    {
        return $this->belongsTo(BusinessGroup::class);
    }

    public function employeeCompanies(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function payrollPeriods(): HasMany
    {
        return $this->hasMany(PayrollPeriod::class);
    }

    public function getActiveEmployeesCountAttribute(): int
    {
        return $this->employeeCompanies()->where('status', 'active')->count();
    }

    public function getTotalPayrollAttribute(): float
    {
        return $this->employeeCompanies()->where('status', 'active')->sum('base_salary');
    }

    public function getCompanyTypeNameAttribute(): string
    {
        $types = [
            'principal' => 'Empresa Principal',
            'subsidiary' => 'Subsidiaria',
            'branch' => 'Sucursal'
        ];

        return $types[$this->company_type] ?? $this->company_type;
    }
}
