<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sparepart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'part_number', 'part_name', 'category_id', 'unit_price',
        'minimum_stock', 'stock_on_hand', 'uom', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(SparepartCategory::class, 'category_id');
    }

    public function isLowStock(): bool
    {
        return $this->stock_on_hand <= $this->minimum_stock;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_on_hand', '<=', 'minimum_stock');
    }
}
