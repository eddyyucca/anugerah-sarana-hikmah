<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\Sparepart;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier:id,supplier_name', 'purchaseRequest:id,pr_number')
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('po_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('date_from')) {
            $query->where('po_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('po_date', '<=', $request->date_to);
        }

        $pos = $query->latest()->paginate(25)->withQueryString();
        return view('purchase-orders.index', compact('pos'));
    }

    public function create(Request $request)
    {
        $poNumber = DocumentNumberService::generatePO();
        $suppliers = Supplier::active()->orderBy('supplier_name')->get(['id', 'supplier_code', 'supplier_name']);
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'unit_price', 'uom']);

        $prId = $request->query('pr_id');
        $pr = null;
        $prItems = [];
        if ($prId) {
            $pr = PurchaseRequest::with('items.sparepart')->find($prId);
            if ($pr) {
                $prItems = $pr->items;
            }
        }

        $approvedPRs = PurchaseRequest::where('status', 'approved')->orderByDesc('id')->get(['id', 'pr_number']);

        return view('purchase-orders.create', compact('poNumber', 'suppliers', 'spareparts', 'pr', 'prItems', 'approvedPRs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'po_number' => DocumentNumberService::generatePO(),
                'purchase_request_id' => $request->purchase_request_id,
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'expected_date' => $request->expected_date,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $po->items()->create([
                    'sparepart_id' => $item['sparepart_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['qty'] * $item['unit_price'],
                ]);
            }

            // Close PR if linked
            if ($request->purchase_request_id) {
                PurchaseRequest::where('id', $request->purchase_request_id)
                    ->update(['status' => 'closed']);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.sparepart', 'supplier', 'purchaseRequest', 'goodsReceipts');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft'])) {
            return back()->with('error', 'Only draft PO can be edited.');
        }

        $purchaseOrder->load('items');
        $suppliers = Supplier::active()->orderBy('supplier_name')->get(['id', 'supplier_code', 'supplier_name']);
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'unit_price', 'uom']);

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'spareparts'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft PO can be edited.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update($request->only('supplier_id', 'po_date', 'expected_date', 'remarks'));

            $purchaseOrder->items()->delete();
            foreach ($request->items as $item) {
                $purchaseOrder->items()->create([
                    'sparepart_id' => $item['sparepart_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['qty'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'PO updated successfully.');
    }

    public function issue(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft PO can be issued.');
        }
        $purchaseOrder->update(['status' => 'issued']);
        return back()->with('success', 'PO issued successfully.');
    }

    public function close(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['status' => 'completed']);
        return back()->with('success', 'PO closed.');
    }
}
