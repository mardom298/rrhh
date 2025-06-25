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
        'ruc',
        'business_name',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'status',
        'tax_settings',
        'payroll_settings'
    ];

    protected $casts = [
        'tax_settings' => 'array',
        'payroll_settings' => 'array'
    ];

    public function businessGroup(): BelongsTo
    {
        return $this->belongsTo(BusinessGroup::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employeeCompanies(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class);
    }

    public function activeEmployees(): HasMany
    {
        return $this->hasMany(EmployeeCompany::class)->where('status', 'active');
    }
}
