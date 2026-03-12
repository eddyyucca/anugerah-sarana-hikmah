<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\WarehouseLocation;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsReceipt::with('purchaseOrder:id,po_number')->withCount('items');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('gr_number', 'like', "%{$request->search}%");
        if ($request->filled('date_from')) $query->where('receipt_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('receipt_date', '<=', $request->date_to);

        $grs = $query->latest()->paginate(25)->withQueryString();
        return view('goods-receipts.index', compact('grs'));
    }

    public function create(Request $request)
    {
        $grNumber = DocumentNumberService::generateGR();
        $locations = WarehouseLocation::orderBy('name')->get();

        $poId = $request->query('po_id');
        $po = null;
        $poItems = collect();

        if ($poId) {
            $po = PurchaseOrder::with('items.sparepart')->find($poId);
            if ($po) {
                $poItems = $po->items->filter(fn($item) => $item->qty_remaining > 0);
            }
        }

        $openPOs = PurchaseOrder::whereIn('status', ['issued', 'partial'])
            ->orderByDesc('id')->get(['id', 'po_number']);

        return view('goods-receipts.create', compact('grNumber', 'locations', 'po', 'poItems', 'openPOs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.po_item_id' => 'nullable|exists:purchase_order_items,id',
            'items.*.warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.qty_received' => 'required|integer|min:1',
        ]);

        // Validate qty doesn't exceed outstanding
        $po = PurchaseOrder::with('items.sparepart')->findOrFail($request->purchase_order_id);
        foreach ($request->items as $item) {
            if (!empty($item['po_item_id'])) {
                $poItem = $po->items->find($item['po_item_id']);
                if ($poItem && $item['qty_received'] > $poItem->qty_remaining) {
                    $partName = $poItem->sparepart ? $poItem->sparepart->part_number : 'item';
                    $remaining = $poItem->qty_remaining;
                    return back()->withErrors([
                        'items' => "Qty for {$partName} exceeds outstanding ({$remaining})."
                    ])->withInput();
                }
            }
        }

        DB::transaction(function () use ($request) {
            $gr = GoodsReceipt::create([
                'gr_number' => DocumentNumberService::generateGR(),
                'purchase_order_id' => $request->purchase_order_id,
                'receipt_date' => $request->receipt_date,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                if ($item['qty_received'] > 0) {
                    $gr->items()->create([
                        'sparepart_id' => $item['sparepart_id'],
                        'warehouse_location_id' => $item['warehouse_location_id'] ?? null,
                        'qty_received' => $item['qty_received'],
                    ]);
                }
            }
        });

        return redirect()->route('goods-receipts.index')->with('success', 'Goods Receipt created.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load('items.sparepart', 'items.warehouseLocation', 'purchaseOrder.items.sparepart', 'postedByUser');
        return view('goods-receipts.show', compact('goodsReceipt'));
    }

    public function post(GoodsReceipt $goodsReceipt)
    {
        if ($goodsReceipt->status !== 'draft') {
            return back()->with('error', 'Only draft GR can be posted.');
        }

        DB::transaction(function () use ($goodsReceipt) {
            foreach ($goodsReceipt->items as $item) {
                StockService::increaseStock(
                    $item->sparepart_id,
                    $item->qty_received,
                    'goods_receipt',
                    $goodsReceipt->id,
                    $item->warehouse_location_id
                );

                // Update PO item qty_received
                $poItem = PurchaseOrderItem::where('purchase_order_id', $goodsReceipt->purchase_order_id)
                    ->where('sparepart_id', $item->sparepart_id)
                    ->first();

                if ($poItem) {
                    $poItem->qty_received += $item->qty_received;
                    $poItem->qty_outstanding = max(0, $poItem->qty - $poItem->qty_received);
                    $poItem->save();
                }
            }

            $goodsReceipt->update([
                'status' => 'posted',
                'posted_by' => auth()->id() ?? 1,
                'posted_at' => now(),
            ]);

            // Update PO status
            $po = $goodsReceipt->purchaseOrder;
            if ($po) {
                $allDone = $po->items()->where('qty_outstanding', '>', 0)->doesntExist();
                $anyDone = $po->items()->where('qty_received', '>', 0)->exists();
                $po->update(['status' => $allDone ? 'completed' : ($anyDone ? 'partial' : $po->status)]);
            }
        });

        return back()->with('success', 'GR posted. Stock updated.');
    }
}
