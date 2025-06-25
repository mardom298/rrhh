<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'position_id',
        'employee_code',
        'hire_date',
        'termination_date',
        'base_salary',
        'total_salary',
        'benefits',
        'is_primary_company',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'termination_date' => 'date',
            'base_salary' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'benefits' => 'array',
            'is_primary_company' => 'boolean',
        ];
    }

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
}
