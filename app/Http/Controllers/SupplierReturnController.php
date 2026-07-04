<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierReturn;
use App\Models\SupplierReturnItem;
use App\Models\GoodsReceipt;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierReturn::with('supplier')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest('return_date');

        $returns   = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::active()->orderBy('supplier_name')->get(['id', 'supplier_name', 'supplier_code']);

        return view('supplier-returns.index', compact('returns', 'suppliers'));
    }

    public function create()
    {
        $suppliers    = Supplier::active()->orderBy('supplier_name')->get();
        $spareparts   = Sparepart::where('is_active', true)->orderBy('part_name')->get(['id', 'part_name', 'part_number', 'uom']);
        $goodsReceipts = GoodsReceipt::with('purchaseOrder.supplier')
            ->latest('receipt_date')->limit(50)->get(['id', 'gr_number', 'receipt_date', 'purchase_order_id']);

        return view('supplier-returns.create', compact('suppliers', 'spareparts', 'goodsReceipts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'return_date'      => 'required|date',
            'supplier_id'      => 'required|exists:suppliers,id',
            'goods_receipt_id' => 'nullable|exists:goods_receipts,id',
            'return_reason'    => 'required|string',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.sparepart_id'  => 'required|exists:spareparts,id',
            'items.*.qty_returned'  => 'required|numeric|min:0.01',
            'items.*.defect_reason' => 'required|string|max:200',
        ]);

        DB::transaction(function () use ($request) {
            $sr = SupplierReturn::create([
                'return_no'        => DocumentNumberService::generateSR(),
                'return_date'      => $request->return_date,
                'supplier_id'      => $request->supplier_id,
                'goods_receipt_id' => $request->goods_receipt_id,
                'purchase_order_id'=> $request->purchase_order_id,
                'return_reason'    => $request->return_reason,
                'notes'            => $request->notes,
                'status'           => 'draft',
            ]);

            foreach ($request->items as $item) {
                SupplierReturnItem::create([
                    'supplier_return_id'      => $sr->id,
                    'sparepart_id'            => $item['sparepart_id'],
                    'qty_returned'            => $item['qty_returned'],
                    'qty_received_original'   => $item['qty_received_original'] ?? 0,
                    'defect_reason'           => $item['defect_reason'],
                    'condition_notes'         => $item['condition_notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('supplier-returns.index')->with('success', 'Dokumen return supplier berhasil dibuat.');
    }

    public function show(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load('supplier', 'items.sparepart', 'goodsReceipt');
        return view('supplier-returns.show', compact('supplierReturn'));
    }

    public function confirm(SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status !== 'draft') {
            return back()->with('error', 'Dokumen sudah dikonfirmasi.');
        }

        DB::transaction(function () use ($supplierReturn) {
            $supplierReturn->load('items.sparepart');

            foreach ($supplierReturn->items as $item) {
                // Kurangi stok karena barang dikembalikan ke supplier
                $item->sparepart->decrement('stock_on_hand', $item->qty_returned);

                StockMovement::create([
                    'sparepart_id'   => $item->sparepart_id,
                    'movement_date'  => $supplierReturn->return_date,
                    'movement_type'  => 'out',
                    'reference_type' => 'supplier_return',
                    'reference_id'   => $supplierReturn->id,
                    'qty_out'        => $item->qty_returned,
                    'qty_in'         => 0,
                    'balance_after'  => $item->sparepart->stock_on_hand,
                ]);
            }

            $supplierReturn->update([
                'status'       => 'confirmed',
                'confirmed_by' => auth()->user()->name ?? 'system',
                'confirmed_at' => now(),
            ]);
        });

        return back()->with('success', 'Return dikonfirmasi. Stok telah disesuaikan.');
    }

    public function send(SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status !== 'confirmed') {
            return back()->with('error', 'Return harus dikonfirmasi terlebih dahulu.');
        }

        $supplierReturn->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Return ditandai sebagai terkirim ke supplier.');
    }
}
