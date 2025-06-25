<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'dni',
        'address',
        'resume_path',
        'cover_letter',
        'expected_salary',
        'status',
        'score',
        'evaluation_notes',
        'applied_at'
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
        'evaluation_notes' => 'array',
        'applied_at' => 'datetime'
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function recruitmentProcesses(): HasMany
    {
        return $this->hasMany(RecruitmentProcess::class);
    }

    public function interviewSchedules(): HasMany
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'applied' => 'Aplicado',
            'screening' => 'Revisión',
            'interview' => 'Entrevista',
            'test' => 'Prueba Técnica',
            'offer' => 'Oferta',
            'hired' => 'Contratado',
            'rejected' => 'Rechazado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getCurrentStage()
    {
        return $this->recruitmentProcesses()
            ->orderBy('stage_date', 'desc')
            ->first();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'applied' => 'bg-blue-100 text-blue-800',
            'screening' => 'bg-yellow-100 text-yellow-800',
            'interview' => 'bg-purple-100 text-purple-800',
            'test' => 'bg-indigo-100 text-indigo-800',
            'offer' => 'bg-green-100 text-green-800',
            'hired' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
