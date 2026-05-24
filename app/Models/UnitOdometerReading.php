<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOdometerReading extends Model
{
    protected $fillable = [
        'unit_id', 'odometer_km', 'delta_km', 'reading_date', 'recorded_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'odometer_km'  => 'decimal:2',
            'delta_km'     => 'decimal:2',
            'reading_date' => 'date',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
