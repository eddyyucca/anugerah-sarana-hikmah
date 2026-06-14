<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReturnItem extends Model
{
    protected $fillable = [
        'supplier_return_id', 'sparepart_id', 'qty_returned',
        'qty_received_original', 'defect_reason', 'condition_notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_returned'           => 'decimal:2',
            'qty_received_original'  => 'decimal:2',
        ];
    }

    public function supplierReturn()
    {
        return $this->belongsTo(SupplierReturn::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
