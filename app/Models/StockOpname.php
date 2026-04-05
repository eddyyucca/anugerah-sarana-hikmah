<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_number', 'opname_date', 'warehouse_location_id',
        'status', 'remarks', 'conducted_by', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return ['opname_date' => 'date', 'approved_at' => 'datetime'];
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    public function conductor()
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
