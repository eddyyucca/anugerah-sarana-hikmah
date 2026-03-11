<?php

namespace App\Http\Controllers;

use App\Models\GoodsIssue;
use App\Models\Sparepart;
use App\Models\WorkOrder;
use App\Models\WarehouseLocation;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use App\Services\RepairCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsIssueController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsIssue::with('workOrder:id,wo_number')
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('gi_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $gis = $query->latest()->paginate(25)->withQueryString();
        return view('goods-issues.index', compact('gis'));
    }

    public function create(Request $request)
    {
        $giNumber = DocumentNumberService::generateGI();
        $spareparts = Sparepart::active()->where('stock_on_hand', '>', 0)
            ->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'stock_on_hand', 'unit_price', 'uom']);
        $locations = WarehouseLocation::orderBy('name')->get();
        $workOrders = WorkOrder::whereIn('status', ['open', 'in_progress', 'waiting_part'])
            ->orderByDesc('id')->get(['id', 'wo_number']);

        $woId = $request->query('wo_id');

        return view('goods-issues.create', compact('giNumber', 'spareparts', 'locations', 'workOrders', 'woId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_order_id' => 'nullable|exists:work_orders,id',
            'issue_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.qty_issued' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $gi = GoodsIssue::create([
                'gi_number' => DocumentNumberService::generateGI(),
                'work_order_id' => $request->work_order_id,
                'issue_date' => $request->issue_date,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $sparepart = Sparepart::find($item['sparepart_id']);
                $gi->items()->create([
                    'sparepart_id' => $item['sparepart_id'],
                    'warehouse_location_id' => $item['warehouse_location_id'] ?? null,
                    'qty_issued' => $item['qty_issued'],
                    'unit_price' => $sparepart->unit_price,
                    'total_price' => $item['qty_issued'] * $sparepart->unit_price,
                ]);
            }
        });

        return redirect()->route('goods-issues.index')->with('success', 'Goods Issue created successfully.');
    }

    public function show(GoodsIssue $goodsIssue)
    {
        $goodsIssue->load('items.sparepart', 'items.warehouseLocation', 'workOrder', 'postedByUser');
        return view('goods-issues.show', compact('goodsIssue'));
    }

    public function post(GoodsIssue $goodsIssue)
    {
        if ($goodsIssue->status !== 'draft') {
            return back()->with('error', 'Only draft GI can be posted.');
        }

        DB::transaction(function () use ($goodsIssue) {
            foreach ($goodsIssue->items as $item) {
                StockService::decreaseStock(
                    $item->sparepart_id,
                    $item->qty_issued,
                    'goods_issue',
                    $goodsIssue->id,
                    $item->warehouse_location_id,
                    $item->unit_price
                );
            }

            $goodsIssue->update([
                'status' => 'posted',
                'posted_by' => auth()->id() ?? 1,
                'posted_at' => now(),
            ]);

            // Recalculate repair cost if linked to WO
            if ($goodsIssue->work_order_id) {
                $wo = WorkOrder::find($goodsIssue->work_order_id);
                if ($wo) RepairCostService::recalculate($wo);
            }
        });

        return back()->with('success', 'Goods Issue posted. Stock updated.');
    }
}
