<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Sparepart;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumablePrController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('requester:id,name')
            ->withCount('items')
            ->where('remarks', 'like', '%[CONSUMABLE]%');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('pr_number', 'like', "%{$request->search}%");
        if ($request->filled('date_from')) $query->where('request_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('request_date', '<=', $request->date_to);

        $prs = $query->latest()->paginate(25)->withQueryString();
        return view('consumable-pr.index', compact('prs'));
    }

    public function create()
    {
        $prNumber = DocumentNumberService::generatePR();
        // Only consumable spareparts
        $spareparts = Sparepart::active()
            ->where('is_consumable', true)
            ->orderBy('part_name')
            ->get(['id', 'part_number', 'part_name', 'uom', 'unit_price', 'stock_on_hand', 'minimum_stock']);

        return view('consumable-pr.create', compact('prNumber', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Calculate estimated total
            $estimatedTotal = 0;
            foreach ($request->items as $item) {
                $sp = Sparepart::find($item['sparepart_id']);
                $estimatedTotal += $sp->unit_price * $item['qty'];
            }

            $pr = PurchaseRequest::create([
                'pr_number' => DocumentNumberService::generatePR(),
                'request_date' => $request->request_date,
                'request_by' => auth()->id() ?? 1,
                'remarks' => '[CONSUMABLE] ' . ($request->remarks ?? ''),
                'estimated_total' => $estimatedTotal,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $pr->items()->create($item);
            }
        });

        return redirect()->route('consumable-pr.index')->with('success', 'Consumable PR created.');
    }
}
