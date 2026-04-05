<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsIssue extends Model
{
    protected $fillable = [
        'gi_number', 'work_order_id', 'issue_date',
        'remarks', 'status', 'posted_by', 'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'posted_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(GoodsIssueItem::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function postedByUser()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
