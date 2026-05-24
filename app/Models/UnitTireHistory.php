<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitTireHistory extends Model
{
    protected $table = 'unit_tire_history';

    protected $fillable = [
        'unit_tire_id', 'unit_id', 'position_number', 'position_label',
        'odo_at_install', 'odo_at_remove', 'km_used',
        'installed_at', 'removed_at', 'removed_reason',
    ];

    protected function casts(): array
    {
        return [
            'odo_at_install' => 'decimal:2',
            'odo_at_remove'  => 'decimal:2',
            'km_used'        => 'decimal:2',
            'installed_at'   => 'date',
            'removed_at'     => 'date',
        ];
    }

    public function unitTire() { return $this->belongsTo(UnitTire::class); }
    public function unit()     { return $this->belongsTo(Unit::class); }
}
