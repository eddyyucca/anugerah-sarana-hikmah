<?php

namespace App\Http\Controllers;

use App\Models\WarehouseTransfer;
use App\Models\WarehouseStock;
use App\Models\Sparepart;
use App\Models\WarehouseLocation;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseTransfer::with('fromLocation:id,name', 'toLocation:id,name', 'creator:id,name')
            ->withCount('items');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('transfer_number', 'like', "%{$request->search}%");

        $transfers = $query->latest()->paginate(25)->withQueryString();
        return view('warehouse-transfer.index', compact('transfers'));
    }

    public function create()
    {
        $locations = WarehouseLocation::orderBy('name')->get();
        $spareparts = Sparepart::active()->where('stock_on_hand', '>', 0)->orderBy('part_name')
            ->get(['id', 'part_number', 'part_name', 'stock_on_hand', 'uom']);
        return view('warehouse-transfer.create', compact('locations', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_location_id' => 'required|exists:warehouse_locations,id',
            'to_location_id' => 'required|exists:warehouse_locations,id|different:from_location_id',
            'transfer_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $transfer = WarehouseTransfer::create([
                'transfer_number' => DocumentNumberService::generateWT(),
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'transfer_date' => $request->transfer_date,
                'status' => 'draft',
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $transfer->items()->create($item);
            }
        });

        return redirect()->route('warehouse-transfer.index')->with('success', 'Transfer created.');
    }

    public function show(WarehouseTransfer $warehouseTransfer)
    {
        $warehouseTransfer->load(
            'items.sparepart', 'fromLocation', 'toLocation',
            'creator:id,name', 'poster:id,name', 'receiver:id,name'
        );
        return view('warehouse-transfer.show', compact('warehouseTransfer'));
    }

    // Sender posts/sends the transfer
    public function send(WarehouseTransfer $warehouseTransfer)
    {
        if ($warehouseTransfer->status !== 'draft') {
            return back()->with('error', 'Only draft transfer can be sent.');
        }

        DB::transaction(function () use ($warehouseTransfer) {
            // Decrease stock from source location
            foreach ($warehouseTransfer->items as $item) {
                StockService::decreaseStock(
                    $item->sparepart_id, $item->qty,
                    'warehouse_transfer_out', $warehouseTransfer->id,
                    $warehouseTransfer->from_location_id
                );
                WarehouseStock::adjustStock($item->sparepart_id, $warehouseTransfer->from_location_id, -$item->qty);
            }

            $warehouseTransfer->update([
                'status' => 'sent',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer sent. Stock deducted from source.');
    }

    // Receiver confirms receipt
    public function receive(WarehouseTransfer $warehouseTransfer)
    {
        if ($warehouseTransfer->status !== 'sent') {
            return back()->with('error', 'Only sent transfer can be received.');
        }

        DB::transaction(function () use ($warehouseTransfer) {
            // Increase stock at destination location
            foreach ($warehouseTransfer->items as $item) {
                StockService::increaseStock(
                    $item->sparepart_id, $item->qty,
                    'warehouse_transfer_in', $warehouseTransfer->id,
                    $warehouseTransfer->to_location_id
                );
                WarehouseStock::adjustStock($item->sparepart_id, $warehouseTransfer->to_location_id, $item->qty);
            }

            $warehouseTransfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer received. Stock added to destination.');
    }
}
