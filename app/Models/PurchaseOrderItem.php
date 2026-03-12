<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'purchase_order_id', 'sparepart_id', 'qty', 'unit_price', 'total_price',
        'qty_received', 'qty_outstanding',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function getQtyRemainingAttribute(): int
    {
        return max(0, $this->qty - $this->qty_received);
    }

    public function isFullyReceived(): bool
    {
        return $this->qty_received >= $this->qty;
    }
}
