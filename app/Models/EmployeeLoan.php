<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_number',
        'amount',
        'balance',
        'monthly_payment',
        'installments',
        'paid_installments',
        'start_date',
        'end_date',
        'interest_rate',
        'status',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingInstallmentsAttribute(): int
    {
        return $this->installments - $this->paid_installments;
    }

    public function getProgressPercentageAttribute(): float
    {
        return ($this->paid_installments / $this->installments) * 100;
    }
}
