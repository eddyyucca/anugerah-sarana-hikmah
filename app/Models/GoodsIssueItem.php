<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsIssueItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'goods_issue_id', 'sparepart_id', 'warehouse_location_id',
        'qty_issued', 'unit_price', 'total_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function goodsIssue()
    {
        return $this->belongsTo(GoodsIssue::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }
}
