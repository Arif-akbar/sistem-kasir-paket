<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'awb',
        'origin_branch_id',
        'destination_branch_id',
        'sender_name',
        'sender_phone',
        'sender_address',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'destination_city',
        'zone_code',
        'service_type',
        'content_description',
        'actual_weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'volumetric_weight_kg',
        'billable_weight_kg',
        'declared_value',
        'status',
        'inspection_label',
        'inspection_confidence',
        'sla_due_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'actual_weight_kg' => 'decimal:2',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'volumetric_weight_kg' => 'decimal:2',
            'billable_weight_kg' => 'decimal:2',
            'declared_value' => 'decimal:2',
            'inspection_confidence' => 'decimal:2',
            'sla_due_at' => 'datetime',
            'delivered_at' => 'datetime',
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

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function manifests(): BelongsToMany
    {
        return $this->belongsToMany(Manifest::class)
            ->withPivot(['loaded_by', 'scanned_at'])
            ->withTimestamps();
    }
}
