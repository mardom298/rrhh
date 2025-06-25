<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'department_id',
        'position_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'min_salary',
        'max_salary',
        'employment_type',
        'experience_level',
        'location',
        'remote_allowed',
        'application_deadline',
        'status',
        'vacancies',
        'benefits'
    ];

    protected $casts = [
        'requirements' => 'array',
        'responsibilities' => 'array',
        'benefits' => 'array',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'application_deadline' => 'date',
        'remote_allowed' => 'boolean'
    ];

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

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'paused' => 'Pausado',
            'closed' => 'Cerrado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getEmploymentTypeNameAttribute(): string
    {
        $types = [
            'full_time' => 'Tiempo Completo',
            'part_time' => 'Medio Tiempo',
            'contract' => 'Contrato',
            'internship' => 'Prácticas'
        ];

        return $types[$this->employment_type] ?? $this->employment_type;
    }

    public function getExperienceLevelNameAttribute(): string
    {
        $levels = [
            'entry' => 'Sin Experiencia',
            'junior' => 'Junior (1-2 años)',
            'mid' => 'Semi Senior (3-5 años)',
            'senior' => 'Senior (5+ años)',
            'executive' => 'Ejecutivo'
        ];

        return $levels[$this->experience_level] ?? $this->experience_level;
    }

    public function getApplicationsCountAttribute(): int
    {
        return $this->applications()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'published' && $this->application_deadline >= now();
    }
}
