<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitTire extends Model
{
    protected $fillable = [
        'sparepart_id', 'serial_number', 'unit_id', 'position_number', 'position_label',
        'total_km', 'km_limit', 'odo_when_installed', 'installed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_km'          => 'decimal:2',
            'km_limit'          => 'decimal:2',
            'odo_when_installed'=> 'decimal:2',
            'installed_at'      => 'date',
        ];
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function histories()
    {
        return $this->hasMany(UnitTireHistory::class)->latest('installed_at');
    }

    public function getIsInstalledAttribute(): bool
    {
        return $this->unit_id !== null;
    }

    // total_km sudah terupdate langsung oleh OdometerService::recordReading()
    // odo_when_installed hanya dipakai untuk menghitung km_used saat ban dilepas
    public function getCurrentKmAttribute(): float
    {
        return $this->total_km;
    }

    public function getRemainingKmAttribute(): float
    {
        return max(0, $this->km_limit - $this->total_km);
    }

    public function getUsagePercentAttribute(): float
    {
        if ($this->km_limit <= 0) return 0;
        return min(100, round(($this->total_km / $this->km_limit) * 100, 1));
    }
}
