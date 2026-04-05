<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitAvailabilityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'unit_id', 'date', 'scheduled_hours', 'downtime_hours',
        'available_hours', 'availability_percent', 'reference_type', 'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'scheduled_hours' => 'decimal:2',
            'downtime_hours' => 'decimal:2',
            'available_hours' => 'decimal:2',
            'availability_percent' => 'decimal:2',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
