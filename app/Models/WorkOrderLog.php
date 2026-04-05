<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'work_order_id', 'activity_time', 'activity_type', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return ['activity_time' => 'datetime'];
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
