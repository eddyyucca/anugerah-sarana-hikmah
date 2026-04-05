<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    protected $fillable = ['sparepart_id', 'warehouse_location_id', 'qty'];

    public function sparepart() { return $this->belongsTo(Sparepart::class); }
    public function warehouseLocation() { return $this->belongsTo(WarehouseLocation::class); }

    public static function getStock(int $sparepartId, int $locationId): int
    {
        return self::where('sparepart_id', $sparepartId)
            ->where('warehouse_location_id', $locationId)
            ->value('qty') ?? 0;
    }

    public static function adjustStock(int $sparepartId, int $locationId, int $qtyChange): void
    {
        $stock = self::firstOrCreate(
            ['sparepart_id' => $sparepartId, 'warehouse_location_id' => $locationId],
            ['qty' => 0]
        );
        $stock->qty = max(0, $stock->qty + $qtyChange);
        $stock->save();
    }
}
