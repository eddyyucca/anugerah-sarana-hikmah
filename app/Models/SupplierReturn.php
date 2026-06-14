<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReturn extends Model
{
    protected $fillable = [
        'return_no', 'return_date', 'supplier_id', 'goods_receipt_id',
        'purchase_order_id', 'return_reason', 'notes', 'status',
        'confirmed_by', 'confirmed_at', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'return_date'  => 'date',
            'confirmed_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierReturnItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'confirmed' => 'Dikonfirmasi',
            'sent'      => 'Terkirim',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'secondary',
            'confirmed' => 'warning',
            'sent'      => 'success',
            default     => 'secondary',
        };
    }
}
