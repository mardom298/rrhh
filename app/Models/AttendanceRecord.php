<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'worked_hours',
        'overtime_hours',
        'status',
        'notes',
        'location'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateWorkedHours(): void
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            
            $totalMinutes = $checkOut->diffInMinutes($checkIn);
            
            // Restar tiempo de descanso si existe
            if ($this->break_start && $this->break_end) {
                $breakStart = Carbon::parse($this->break_start);
                $breakEnd = Carbon::parse($this->break_end);
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                $totalMinutes -= $breakMinutes;
            }
            
            $this->worked_hours = $totalMinutes;
            
            // Calcular horas extras (más de 8 horas = 480 minutos)
            $this->overtime_hours = max(0, $totalMinutes - 480);
        }
    }

    public function getWorkedHoursFormattedAttribute(): string
    {
        $hours = floor($this->worked_hours / 60);
        $minutes = $this->worked_hours % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
