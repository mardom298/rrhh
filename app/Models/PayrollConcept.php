<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollConcept extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'type',
        'calculation_type',
        'value',
        'formula',
        'taxable',
        'affects_cts',
        'affects_gratification',
        'affects_vacation',
        'active',
        'order'
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'taxable' => 'boolean',
        'affects_cts' => 'boolean',
        'affects_gratification' => 'boolean',
        'affects_vacation' => 'boolean',
        'active' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getTypeNameAttribute(): string
    {
        $types = [
            'income' => 'Ingreso',
            'deduction' => 'Descuento',
            'contribution' => 'Aporte'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function calculateAmount($baseSalary, $quantity = 1, $customRate = null): float
    {
        $rate = $customRate ?? $this->value;

        return match($this->calculation_type) {
            'fixed' => $rate * $quantity,
            'percentage' => ($baseSalary * $rate / 100) * $quantity,
            'formula' => $this->evaluateFormula($baseSalary, $quantity),
            default => 0
        };
    }

    private function evaluateFormula($baseSalary, $quantity): float
    {
        // Implementación básica de evaluación de fórmulas
        // En producción se podría usar una librería más robusta
        $formula = str_replace([
            '{salary}',
            '{quantity}',
            '{value}'
        ], [
            $baseSalary,
            $quantity,
            $this->value
        ], $this->formula);

        // Evaluación segura de fórmulas matemáticas básicas
        try {
            return eval("return $formula;");
        } catch (Exception $e) {
            return 0;
        }
    }
}
