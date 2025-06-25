<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluator_id',
        'period',
        'type',
        'evaluation_date',
        'due_date',
        'status',
        'overall_score',
        'scores',
        'strengths',
        'areas_for_improvement',
        'goals',
        'development_plan',
        'evaluator_comments',
        'employee_comments',
        'submitted_at',
        'reviewed_at'
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'due_date' => 'date',
        'overall_score' => 'decimal:2',
        'scores' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function getTypeNameAttribute(): string
    {
        $types = [
            'self' => 'Autoevaluación',
            'supervisor' => 'Evaluación de Supervisor',
            'peer' => 'Evaluación de Pares',
            '360' => 'Evaluación 360°',
            'customer' => 'Evaluación de Cliente'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'draft' => 'Borrador',
            'submitted' => 'Enviado',
            'reviewed' => 'Revisado',
            'approved' => 'Aprobado',
            'completed' => 'Completado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function calculateOverallScore(): void
    {
        if (!$this->scores || empty($this->scores)) {
            return;
        }

        $total = 0;
        $count = 0;

        foreach ($this->scores as $competency => $score) {
            if (is_numeric($score)) {
                $total += $score;
                $count++;
            }
        }

        $this->overall_score = $count > 0 ? round($total / $count, 2) : 0;
        $this->save();
    }
}
