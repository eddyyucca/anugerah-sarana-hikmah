<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\Sparepart;
use App\Models\WarehouseLocation;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with('warehouseLocation:id,name', 'conductor:id,name')->withCount('items');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('opname_number', 'like', "%{$request->search}%");

        $opnames = $query->latest()->paginate(25)->withQueryString();
        return view('stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $locations = WarehouseLocation::orderBy('name')->get();
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'stock_on_hand', 'uom']);
        return view('stock-opname.create', compact('locations', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.physical_qty' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $opname = StockOpname::create([
                'opname_number' => DocumentNumberService::generateSO(),
                'opname_date' => $request->opname_date,
                'warehouse_location_id' => $request->warehouse_location_id,
                'status' => 'draft',
                'remarks' => $request->remarks,
                'conducted_by' => auth()->id() ?? 1,
            ]);

            foreach ($request->items as $item) {
                $sp = Sparepart::find($item['sparepart_id']);
                $opname->items()->create([
                    'sparepart_id' => $item['sparepart_id'],
                    'system_qty' => $sp->stock_on_hand,
                    'physical_qty' => $item['physical_qty'],
                    'difference' => $item['physical_qty'] - $sp->stock_on_hand,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('stock-opname.index')->with('success', 'Stock opname created.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load('items.sparepart', 'warehouseLocation', 'conductor', 'approver');
        return view('stock-opname.show', compact('stockOpname'));
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') return back()->with('error', 'Already processed.');

        DB::transaction(function () use ($stockOpname) {
            foreach ($stockOpname->items as $item) {
                if ($item->difference !== 0) {
                    $sp = Sparepart::lockForUpdate()->find($item->sparepart_id);
                    $sp->stock_on_hand = $item->physical_qty;
                    $sp->save();
                }
            }

            $stockOpname->update([
                'status' => 'completed',
                'approved_by' => auth()->id() ?? 1,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock opname approved. Stock adjusted.');
    }
}
