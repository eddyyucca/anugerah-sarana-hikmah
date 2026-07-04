<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Sparepart::with('category:id,name')->where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('part_number', 'like', "%{$s}%")
                ->orWhere('part_name', 'like', "%{$s}%")
                ->orWhere('bin_location', 'like', "%{$s}%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'low'   => $query->whereColumn('stock_on_hand', '<=', 'minimum_stock')->where('stock_on_hand', '>', 0),
                'empty' => $query->where('stock_on_hand', 0),
                'ok'    => $query->whereColumn('stock_on_hand', '>', 'minimum_stock'),
                default => null,
            };
        }

        $spareparts = $query->orderByRaw('CASE WHEN stock_on_hand <= minimum_stock THEN 0 ELSE 1 END')
            ->orderBy('part_name')
            ->paginate(30)
            ->withQueryString();

        $categories = SparepartCategory::orderBy('name')->get(['id', 'name']);

        // KPI summary
        $summary = Sparepart::where('is_active', true)->selectRaw('
            COUNT(*) as total_items,
            SUM(stock_on_hand) as total_qty,
            SUM(stock_on_hand * unit_price) as total_value,
            SUM(CASE WHEN stock_on_hand = 0 THEN 1 ELSE 0 END) as empty_count,
            SUM(CASE WHEN stock_on_hand > 0 AND stock_on_hand <= minimum_stock THEN 1 ELSE 0 END) as low_count
        ')->first();

        // Mutasi terbaru (10 transaksi)
        $recentMovements = StockMovement::with('sparepart:id,part_number,part_name')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('stock-inventory.index', compact('spareparts', 'categories', 'summary', 'recentMovements'));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with('sparepart:id,part_number,part_name', 'warehouseLocation:id,name');

        if ($request->filled('search')) {
            $query->whereHas('sparepart', fn($q) => $q
                ->where('part_number', 'like', "%{$request->search}%")
                ->orWhere('part_name', 'like', "%{$request->search}%"));
        }
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }
        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        $movements = $query->latest('created_at')->paginate(30)->withQueryString();

        $referenceTypes = StockMovement::distinct()->pluck('reference_type')->sort()->values();

        return view('stock-inventory.movements', compact('movements', 'referenceTypes'));
    }
}
