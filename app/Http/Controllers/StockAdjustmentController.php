<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Models\WarehouseLocation;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with('sparepart:id,part_number,part_name', 'warehouseLocation:id,name')
            ->where('reference_type', 'adjustment');

        if ($request->filled('search')) {
            $query->whereHas('sparepart', function ($q) use ($request) {
                $q->where('part_number', 'like', "%{$request->search}%")
                  ->orWhere('part_name', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('movement_type', $request->type);
        }

        $adjustments = $query->latest('created_at')->paginate(25)->withQueryString();
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $spareparts = Sparepart::active()->orderBy('part_name')
            ->get(['id', 'part_number', 'part_name', 'stock_on_hand', 'unit_price', 'uom']);
        $locations = WarehouseLocation::orderBy('name')->get(['id', 'name']);
        return view('stock-adjustments.create', compact('spareparts', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustment_date' => 'required|date',
            'reason'          => 'required|string|max:500',
            'items'           => 'required|array|min:1',
            'items.*.sparepart_id'         => 'required|exists:spareparts,id',
            'items.*.warehouse_location_id'=> 'nullable|exists:warehouse_locations,id',
            'items.*.qty'                  => 'required|integer|not_in:0',
            'items.*.unit_price'           => 'nullable|numeric|min:0',
            'items.*.notes'                => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $sp = Sparepart::lockForUpdate()->findOrFail($item['sparepart_id']);
                $qty = (int) $item['qty'];
                $price = !empty($item['unit_price']) ? (float)$item['unit_price'] : $sp->unit_price;
                $locId = $item['warehouse_location_id'] ?? null;

                if ($qty > 0) {
                    $sp->stock_on_hand += $qty;
                    $sp->save();
                    StockMovement::create([
                        'movement_date'        => $request->adjustment_date,
                        'sparepart_id'         => $sp->id,
                        'warehouse_location_id'=> $locId,
                        'movement_type'        => 'in',
                        'reference_type'       => 'adjustment',
                        'reference_id'         => 0,
                        'qty_in'               => $qty,
                        'qty_out'              => 0,
                        'balance_after'        => $sp->stock_on_hand,
                        'unit_price'           => $price,
                        'created_at'           => now(),
                    ]);
                } elseif ($qty < 0) {
                    $absQty = abs($qty);
                    if ($sp->stock_on_hand < $absQty) {
                        throw new \Exception("Stok tidak cukup untuk {$sp->part_number}. Tersedia: {$sp->stock_on_hand}, Dikurangi: {$absQty}");
                    }
                    $sp->stock_on_hand -= $absQty;
                    $sp->save();
                    StockMovement::create([
                        'movement_date'        => $request->adjustment_date,
                        'sparepart_id'         => $sp->id,
                        'warehouse_location_id'=> $locId,
                        'movement_type'        => 'out',
                        'reference_type'       => 'adjustment',
                        'reference_id'         => 0,
                        'qty_in'               => 0,
                        'qty_out'              => $absQty,
                        'balance_after'        => $sp->stock_on_hand,
                        'unit_price'           => $price,
                        'created_at'           => now(),
                    ]);
                }
            }
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
}
