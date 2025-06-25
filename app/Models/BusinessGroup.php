<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'ruc_group',
        'description',
        'logo',
        'settings',
        'active'
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getTotalEmployeesAttribute(): int
    {
        return $this->users()->count();
    }

    public function getActiveCompaniesAttribute(): int
    {
        return $this->companies()->where('active', true)->count();
    }

    public function getTotalPayrollAttribute(): float
    {
        return $this->companies()
            ->with(['employeeCompanies'])
            ->get()
            ->sum(function($company) {
                return $company->employeeCompanies->where('status', 'active')->sum('base_salary');
            });
    }
}
