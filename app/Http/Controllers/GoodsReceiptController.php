<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\WarehouseLocation;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsReceipt::with('purchaseOrder:id,po_number')
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('gr_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('date_from')) {
            $query->where('receipt_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('receipt_date', '<=', $request->date_to);
        }

        $grs = $query->latest()->paginate(25)->withQueryString();
        return view('goods-receipts.index', compact('grs'));
    }

    public function create(Request $request)
    {
        $grNumber = DocumentNumberService::generateGR();
        $locations = WarehouseLocation::orderBy('name')->get();

        $poId = $request->query('po_id');
        $po = null;
        $poItems = [];
        if ($poId) {
            $po = PurchaseOrder::with('items.sparepart')->find($poId);
            if ($po) $poItems = $po->items;
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
            'items.*.warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.qty_received' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $gr = GoodsReceipt::create([
                'gr_number' => DocumentNumberService::generateGR(),
                'purchase_order_id' => $request->purchase_order_id,
                'receipt_date' => $request->receipt_date,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $gr->items()->create($item);
            }
        });

        return redirect()->route('goods-receipts.index')->with('success', 'Goods Receipt created successfully.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load('items.sparepart', 'items.warehouseLocation', 'purchaseOrder', 'postedByUser');
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
            }

            $goodsReceipt->update([
                'status' => 'posted',
                'posted_by' => auth()->id() ?? 1,
                'posted_at' => now(),
            ]);

            // Update PO status
            $po = $goodsReceipt->purchaseOrder;
            if ($po) {
                $po->update(['status' => 'partial']);
            }
        });

        return back()->with('success', 'Goods Receipt posted. Stock updated.');
    }
}
