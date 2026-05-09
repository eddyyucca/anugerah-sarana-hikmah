<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitMonthlyCost extends Model
{
    protected $fillable = [
        'unit_id', 'year_month', 'total_cost', 'work_order_count',
        'is_over_budget', 'exceeded_at',
    ];

    protected function casts(): array
    {
        return [
            'total_cost' => 'decimal:2',
            'is_over_budget' => 'boolean',
            'exceeded_at' => 'datetime',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeOverBudget($query)
    {
        return $query->where('is_over_budget', true);
    }

    public function scopeForMonth($query, string $yearMonth)
    {
        return $query->where('year_month', $yearMonth);
    }
}
