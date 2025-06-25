<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'job_application_id',
        'stage',
        'stage_date',
        'interviewer_id',
        'score',
        'notes',
        'feedback',
        'result',
        'next_stage_date'
    ];

    protected $casts = [
        'stage_date' => 'date',
        'next_stage_date' => 'date',
        'feedback' => 'array'
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getStageNameAttribute(): string
    {
        $stages = [
            'screening' => 'Revisión de CV',
            'phone_interview' => 'Entrevista Telefónica',
            'technical_test' => 'Prueba Técnica',
            'interview_1' => 'Primera Entrevista',
            'interview_2' => 'Segunda Entrevista',
            'reference_check' => 'Verificación de Referencias',
            'offer' => 'Oferta Laboral',
            'hired' => 'Contratado',
            'rejected' => 'Rechazado'
        ];

        return $stages[$this->stage] ?? $this->stage;
    }

    public function getResultNameAttribute(): string
    {
        $results = [
            'pass' => 'Aprobado',
            'fail' => 'Rechazado',
            'pending' => 'Pendiente'
        ];

        return $results[$this->result] ?? $this->result;
    }
}
