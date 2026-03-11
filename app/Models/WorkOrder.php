<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'wo_number', 'unit_id', 'complaint', 'maintenance_type',
        'technician_id', 'status', 'start_time', 'end_time',
        'downtime_hours', 'labor_cost', 'vendor_cost', 'consumable_cost',
        'action_taken', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'downtime_hours' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'vendor_cost' => 'decimal:2',
            'consumable_cost' => 'decimal:2',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(WorkOrderLog::class);
    }

    public function goodsIssues()
    {
        return $this->hasMany(GoodsIssue::class);
    }

    public function costSummary()
    {
        return $this->hasOne(RepairCostSummary::class);
    }

    public function getSparepartCostAttribute(): float
    {
        return $this->goodsIssues()
            ->where('status', 'posted')
            ->with('items')
            ->get()
            ->flatMap->items
            ->sum('total_price');
    }

    public function getTotalCostAttribute(): float
    {
        return $this->sparepart_cost + $this->labor_cost + $this->vendor_cost + $this->consumable_cost;
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
