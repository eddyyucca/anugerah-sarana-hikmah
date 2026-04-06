<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'stock_opname_id', 'sparepart_id', 'system_qty', 'physical_qty',
        'difference', 'notes', 'is_counted', 'counted_by', 'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_counted' => 'boolean',
            'counted_at' => 'datetime',
        ];
    }

    public function stockOpname() { return $this->belongsTo(StockOpname::class); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
    public function counter() { return $this->belongsTo(User::class, 'counted_by'); }
}
