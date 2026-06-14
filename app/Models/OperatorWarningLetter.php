<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorWarningLetter extends Model
{
    protected $fillable = [
        'letter_no', 'letter_date', 'operator_performance_record_id',
        'operator_id', 'unit_id', 'work_order_id', 'year_month',
        'budget_limit', 'total_cost', 'excess_amount',
        'violation_description', 'created_by', 'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'letter_date'     => 'date',
            'budget_limit'    => 'decimal:2',
            'total_cost'      => 'decimal:2',
            'excess_amount'   => 'decimal:2',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function performanceRecord()
    {
        return $this->belongsTo(OperatorPerformanceRecord::class, 'operator_performance_record_id');
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
}
