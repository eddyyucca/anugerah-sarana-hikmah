<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Sparepart;
use App\Models\StockMovement;
use App\Services\DocumentNumberService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with('conductor:id,name')->withCount('items');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('opname_number', 'like', "%{$request->search}%");

        $opnames = $query->latest()->paginate(25)->withQueryString();
        return view('stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $itemCount = Sparepart::active()->count();
        $hasActive = StockOpname::where('status', 'in_progress')->exists();
        return view('stock-opname.create', compact('itemCount', 'hasActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'remarks'     => 'nullable|string|max:500',
        ]);

        $opname = DB::transaction(function () use ($request) {
            $opname = StockOpname::create([
                'opname_number' => DocumentNumberService::generateSO(),
                'opname_date'   => $request->opname_date,
                'status'        => 'in_progress',
                'remarks'       => $request->remarks,
                'conducted_by'  => auth()->id() ?? 1,
            ]);

            // Auto-populate ALL active spareparts, sorted by bin_location then part_number
            $spareparts = Sparepart::active()
                ->orderByRaw("CASE WHEN bin_location IS NULL OR bin_location = '' THEN 1 ELSE 0 END")
                ->orderBy('bin_location')
                ->orderBy('part_number')
                ->get(['id', 'stock_on_hand']);

            $items = $spareparts->map(fn($sp) => [
                'stock_opname_id' => $opname->id,
                'sparepart_id'    => $sp->id,
                'system_qty'      => $sp->stock_on_hand,
                'physical_qty'    => 0,
                'difference'      => 0,
                'is_counted'      => false,
            ])->toArray();

            StockOpnameItem::insert($items);

            return $opname;
        });

        $total = $opname->items()->count();
        return redirect()->route('stock-opname.show', $opname)
            ->with('success', "Opname #{$opname->opname_number} dibuat dengan {$total} item. Mulai pencacahan!");
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load([
            'items.sparepart:id,part_number,part_name,bin_location,uom',
            'items.counter:id,name',
            'conductor:id,name',
            'submitter:id,name',
            'approver:id,name',
        ]);

        $items = $stockOpname->items->sortBy(function ($item) {
            $binloc = $item->sparepart->bin_location ?? '';
            return [$binloc === '' ? 'ZZZZ' : $binloc, $item->sparepart->part_number];
        })->values();

        $totalItems    = $items->count();
        $countedItems  = $items->where('is_counted', true)->count();
        $discrepancies = $items->where('is_counted', true)->filter(fn($i) => $i->difference !== 0)->count();
        $shortItems    = $items->where('is_counted', true)->filter(fn($i) => $i->difference < 0)->count();
        $overItems     = $items->where('is_counted', true)->filter(fn($i) => $i->difference > 0)->count();

        $approvalLogs = ApprovalService::getStatus('so', $stockOpname->id);

        $canApprove = false;
        if ($stockOpname->status === 'pending_approval') {
            $check = ApprovalService::canApprove(auth()->user(), 'so', $stockOpname->id);
            $canApprove = $check['can_approve'];
        }

        return view('stock-opname.show', compact(
            'stockOpname', 'items', 'totalItems', 'countedItems',
            'discrepancies', 'shortItems', 'overItems', 'approvalLogs', 'canApprove'
        ));
    }

    /**
     * Save counted items (partial save — warehouse can save and continue later).
     */
    public function count(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Opname tidak dalam status pencacahan.');
        }

        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.physical_qty' => 'nullable|integer|min:0',
            'items.*.notes'        => 'nullable|string|max:255',
        ]);

        $saved = 0;
        DB::transaction(function () use ($request, $stockOpname, &$saved) {
            foreach ($request->items as $itemId => $data) {
                // Skip items where physical_qty is not filled
                if (!isset($data['physical_qty']) || $data['physical_qty'] === '') continue;

                $item = StockOpnameItem::where('stock_opname_id', $stockOpname->id)
                    ->where('id', $itemId)
                    ->first();

                if (!$item) continue;

                $physQty            = (int) $data['physical_qty'];
                $item->physical_qty = $physQty;
                $item->difference   = $physQty - $item->system_qty;
                $item->notes        = $data['notes'] ?? null;
                $item->is_counted   = true;
                $item->counted_by   = auth()->id() ?? 1;
                $item->counted_at   = now();
                $item->save();
                $saved++;
            }
        });

        if ($saved === 0) {
            return back()->with('warning', 'Tidak ada item yang disimpan. Isi qty fisik terlebih dahulu.');
        }

        $counted   = $stockOpname->items()->where('is_counted', true)->count();
        $remaining = $stockOpname->items()->where('is_counted', false)->count();

        return back()->with('success', "{$saved} item baru disimpan. Total {$counted} terhitung, sisa {$remaining} belum.");
    }

    /**
     * Submit opname for approval after all items are counted.
     */
    public function submit(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Opname harus dalam status pencacahan.');
        }

        $uncounted = $stockOpname->items()->where('is_counted', false)->count();
        if ($uncounted > 0) {
            return back()->with('error', "Masih ada {$uncounted} item yang belum dihitung. Selesaikan dulu.");
        }

        DB::transaction(function () use ($stockOpname) {
            $stockOpname->update([
                'status'       => 'pending_approval',
                'submitted_by' => auth()->id() ?? 1,
                'submitted_at' => now(),
            ]);

            ApprovalService::initiate('so', $stockOpname->id, 0);
        });

        return back()->with('success', 'Opname diajukan untuk persetujuan.');
    }

    /**
     * Approve opname and adjust stock for all discrepancies.
     */
    public function approve(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending_approval') {
            return back()->with('error', 'Opname belum diajukan untuk persetujuan.');
        }

        $authCheck = ApprovalService::canApprove(auth()->user(), 'so', $stockOpname->id);
        if (!$authCheck['can_approve']) {
            return back()->with('error', $authCheck['message']);
        }

        DB::transaction(function () use ($request, $stockOpname) {
            $stockOpname->load('items');

            foreach ($stockOpname->items as $item) {
                if ($item->difference === 0) continue;

                $sp = Sparepart::lockForUpdate()->find($item->sparepart_id);
                $sp->stock_on_hand = $item->physical_qty;
                $sp->save();

                // Log stock adjustment as stock movement
                StockMovement::create([
                    'movement_date'         => now()->toDateString(),
                    'sparepart_id'          => $item->sparepart_id,
                    'warehouse_location_id' => null,
                    'movement_type'         => $item->difference > 0 ? 'in' : 'out',
                    'reference_type'        => 'stock_opname',
                    'reference_id'          => $stockOpname->id,
                    'qty_in'                => $item->difference > 0 ? $item->difference : 0,
                    'qty_out'               => $item->difference < 0 ? abs($item->difference) : 0,
                    'balance_after'         => $item->physical_qty,
                    'unit_price'            => $sp->unit_price ?? 0,
                ]);
            }

            ApprovalService::approve('so', $stockOpname->id, auth()->id(), $request->remarks);

            $stockOpname->update([
                'status'      => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Opname disetujui. Stok telah disesuaikan.');
    }

    /**
     * Reject opname — returns to in_progress for recount.
     */
    public function reject(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending_approval') {
            return back()->with('error', 'Opname belum diajukan untuk persetujuan.');
        }

        $authCheck = ApprovalService::canApprove(auth()->user(), 'so', $stockOpname->id);
        if (!$authCheck['can_approve']) {
            return back()->with('error', $authCheck['message']);
        }

        DB::transaction(function () use ($request, $stockOpname) {
            ApprovalService::reject('so', $stockOpname->id, auth()->id(), $request->remarks);
            $stockOpname->update(['status' => 'in_progress']);
        });

        return back()->with('warning', 'Opname ditolak. Petugas gudang dapat melakukan penghitungan ulang.');
    }
}
