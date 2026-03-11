<?php

namespace App\Services;

use App\Models\Sparepart;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public static function increaseStock(
        int $sparepartId,
        int $qty,
        string $referenceType,
        int $referenceId,
        ?int $warehouseLocationId = null,
        ?float $unitPrice = null
    ): void {
        $sparepart = Sparepart::lockForUpdate()->findOrFail($sparepartId);
        $sparepart->stock_on_hand += $qty;
        $sparepart->save();

        StockMovement::create([
            'movement_date' => now()->toDateString(),
            'sparepart_id' => $sparepartId,
            'warehouse_location_id' => $warehouseLocationId,
            'movement_type' => 'in',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'qty_in' => $qty,
            'qty_out' => 0,
            'balance_after' => $sparepart->stock_on_hand,
            'unit_price' => $unitPrice ?? $sparepart->unit_price,
            'created_at' => now(),
        ]);
    }

    public static function decreaseStock(
        int $sparepartId,
        int $qty,
        string $referenceType,
        int $referenceId,
        ?int $warehouseLocationId = null,
        ?float $unitPrice = null
    ): void {
        $sparepart = Sparepart::lockForUpdate()->findOrFail($sparepartId);

        if ($sparepart->stock_on_hand < $qty) {
            throw new \Exception("Insufficient stock for {$sparepart->part_number}. Available: {$sparepart->stock_on_hand}, Requested: {$qty}");
        }

        $sparepart->stock_on_hand -= $qty;
        $sparepart->save();

        StockMovement::create([
            'movement_date' => now()->toDateString(),
            'sparepart_id' => $sparepartId,
            'warehouse_location_id' => $warehouseLocationId,
            'movement_type' => 'out',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'qty_in' => 0,
            'qty_out' => $qty,
            'balance_after' => $sparepart->stock_on_hand,
            'unit_price' => $unitPrice ?? $sparepart->unit_price,
            'created_at' => now(),
        ]);
    }
}
