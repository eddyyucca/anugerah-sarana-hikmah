<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairCostSummary extends Model
{
    protected $fillable = [
        'work_order_id', 'unit_id', 'sparepart_cost',
        'labor_cost', 'vendor_cost', 'consumable_cost', 'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'sparepart_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'vendor_cost' => 'decimal:2',
            'consumable_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
