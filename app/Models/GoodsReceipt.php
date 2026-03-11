<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'gr_number', 'purchase_order_id', 'receipt_date',
        'remarks', 'status', 'posted_by', 'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'posted_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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
