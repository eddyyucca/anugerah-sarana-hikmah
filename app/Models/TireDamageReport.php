<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TireDamageReport extends Model
{
    protected $fillable = [
        'report_no', 'unit_tire_id', 'unit_id', 'report_date',
        'km_at_damage', 'km_used_when_damaged', 'installed_at',
        'damage_type', 'damage_description', 'is_warranty_claim',
        'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'report_date'          => 'date',
            'installed_at'         => 'date',
            'km_at_damage'         => 'decimal:2',
            'km_used_when_damaged' => 'decimal:2',
            'is_warranty_claim'    => 'boolean',
            'approved_at'          => 'datetime',
        ];
    }

    public function unitTire()
    {
        return $this->belongsTo(UnitTire::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public static function damageTypeLabel(string $type): string
    {
        return match($type) {
            'puncture'            => 'Bocor (Puncture)',
            'sidewall'            => 'Dinding Robek (Sidewall)',
            'bead'                => 'Bead Rusak',
            'tread'               => 'Tapak Aus (Tread)',
            'manufacturing_defect'=> 'Cacat Produksi',
            'other'               => 'Lainnya',
            default               => ucfirst($type),
        };
    }
}
