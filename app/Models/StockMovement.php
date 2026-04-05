<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'movement_date', 'sparepart_id', 'warehouse_location_id',
        'movement_type', 'reference_type', 'reference_id',
        'qty_in', 'qty_out', 'balance_after', 'unit_price', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'unit_price' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }
}
