<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'payment_date',
        'type',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'settings'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_date' => 'date',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
        'settings' => 'array'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->payrollItems()->distinct('user_id')->count();
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'draft' => 'Borrador',
            'calculated' => 'Calculado',
            'approved' => 'Aprobado',
            'paid' => 'Pagado',
            'closed' => 'Cerrado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getTypeNameAttribute(): string
    {
        $types = [
            'monthly' => 'Mensual',
            'gratification' => 'Gratificación',
            'cts' => 'CTS',
            'bonus' => 'Bono'
        ];

        return $types[$this->type] ?? $this->type;
    }
}
