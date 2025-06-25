<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ruc',
        'description',
        'legal_representative',
        'address',
        'phone',
        'email',
        'website',
        'status',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function activeCompanies(): HasMany
    {
        return $this->hasMany(Company::class)->where('status', 'active');
    }
}
