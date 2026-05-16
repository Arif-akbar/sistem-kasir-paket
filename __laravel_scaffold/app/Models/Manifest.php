<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Manifest extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifest_no',
        'origin_branch_id',
        'destination_branch_id',
        'driver_name',
        'vehicle_number',
        'status',
        'dispatched_at',
        'arrived_at',
        'printed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
            'arrived_at' => 'datetime',
            'printed_at' => 'datetime',
        ];
    }

    public function originBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class)
            ->withPivot(['loaded_by', 'scanned_at'])
            ->withTimestamps();
    }
}
