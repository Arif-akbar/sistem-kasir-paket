<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address_line',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'operating_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'operating_hours' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function originPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'origin_branch_id');
    }

    public function destinationPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'destination_branch_id');
    }
}
