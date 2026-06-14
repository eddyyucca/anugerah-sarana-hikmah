<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorPerformanceRecord extends Model
{
    protected $fillable = [
        'operator_id', 'unit_id', 'work_order_id', 'year_month',
        'monthly_budget_limit', 'total_cost_at_exceedance', 'excess_amount',
        'recorded_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_budget_limit' => 'decimal:2',
            'total_cost_at_exceedance' => 'decimal:2',
            'excess_amount' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function warningLetter()
    {
        return $this->hasOne(\App\Models\OperatorWarningLetter::class);
    }

    public function scopeForOperator($query, int $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }

    public function scopeForMonth($query, string $yearMonth)
    {
        return $query->where('year_month', $yearMonth);
    }
}
