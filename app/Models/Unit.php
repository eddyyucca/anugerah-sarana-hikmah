<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'unit_code', 'unit_model', 'unit_type', 'category_id',
        'department', 'current_status', 'hour_meter', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hour_meter' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function availabilityLogs()
    {
        return $this->hasMany(UnitAvailabilityLog::class);
    }

    public function repairCosts()
    {
        return $this->hasMany(RepairCostSummary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('current_status', $status);
    }
}
