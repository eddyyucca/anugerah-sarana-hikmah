<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'pr_number', 'request_date', 'request_by', 'remarks',
        'status', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
