<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    protected $fillable = [
        'name', 'interval_km', 'alert_before_km',
        'sparepart_id', 'qty_per_service', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'interval_km'     => 'decimal:2',
            'alert_before_km' => 'decimal:2',
            'is_active'       => 'boolean',
        ];
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function lastLogForUnit(int $unitId): ?MaintenanceLog
    {
        return $this->maintenanceLogs()
            ->where('unit_id', $unitId)
            ->latest('service_date')
            ->first();
    }
}
