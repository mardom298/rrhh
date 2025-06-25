<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeNameAttribute(): string
    {
        $types = [
            'vacation' => 'Vacaciones',
            'sick' => 'Licencia Médica',
            'maternity' => 'Licencia de Maternidad',
            'paternity' => 'Licencia de Paternidad',
            'personal' => 'Licencia Personal',
            'bereavement' => 'Licencia por Duelo'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'pending' => 'Pendiente',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'cancelled' => 'Cancelado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
