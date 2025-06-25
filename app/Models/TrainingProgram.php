<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'type',
        'category',
        'duration_hours',
        'cost',
        'provider',
        'start_date',
        'end_date',
        'max_participants',
        'requirements',
        'objectives',
        'certification_available',
        'status'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'requirements' => 'array',
        'objectives' => 'array',
        'certification_available' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function getTypeNameAttribute(): string
    {
        $types = [
            'online' => 'En Línea',
            'presencial' => 'Presencial',
            'hibrido' => 'Híbrido'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'tecnico' => 'Técnico',
            'liderazgo' => 'Liderazgo',
            'soft_skills' => 'Habilidades Blandas',
            'compliance' => 'Cumplimiento',
            'seguridad' => 'Seguridad'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'in_progress' => 'En Progreso',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getCompletedCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }

    public function getAvailableSpotsAttribute(): int
    {
        if (!$this->max_participants) {
            return 999;
        }
        
        return max(0, $this->max_participants - $this->enrolled_count);
    }
}
