<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'maintenance_item_id', 'unit_id', 'sparepart_id', 'qty_used',
        'odometer_at_service', 'next_service_km', 'service_date',
        'performed_by', 'cost', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'odometer_at_service' => 'decimal:2',
            'next_service_km'     => 'decimal:2',
            'service_date'        => 'date',
            'cost'                => 'decimal:2',
        ];
    }

    public function maintenanceItem()
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
