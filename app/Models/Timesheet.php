<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'ts_number', 'p2h_check_id', 'unit_id', 'operator_id',
        'shift_date', 'shift', 'hour_meter_start', 'hour_meter_end',
        'km_end', 'km_traveled', 'working_hours', 'retase', 'notes', 'submitted_by',
    ];

    protected function casts(): array
    {
        return [
            'shift_date'        => 'date',
            'hour_meter_start'  => 'decimal:2',
            'hour_meter_end'    => 'decimal:2',
            'km_end'            => 'decimal:2',
            'km_traveled'       => 'decimal:2',
            'working_hours'     => 'decimal:2',
            'retase'            => 'integer',
        ];
    }

    public function p2h()
    {
        return $this->belongsTo(P2hCheck::class, 'p2h_check_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
