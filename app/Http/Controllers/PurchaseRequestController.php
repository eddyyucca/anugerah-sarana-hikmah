<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Sparepart;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('requester:id,name')
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('pr_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('date_from')) {
            $query->where('request_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('request_date', '<=', $request->date_to);
        }

        $prs = $query->latest()->paginate(25)->withQueryString();
        return view('purchase-requests.index', compact('prs'));
    }

    public function create()
    {
        $prNumber = DocumentNumberService::generatePR();
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'uom']);
        return view('purchase-requests.create', compact('prNumber', 'spareparts'));
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
            $pr = PurchaseRequest::create([
                'pr_number' => DocumentNumberService::generatePR(),
                'request_date' => $request->request_date,
                'request_by' => auth()->id() ?? 1,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                $pr->items()->create($item);
            }
        });

        return redirect()->route('purchase-requests.index')->with('success', 'Purchase Request created successfully.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('items.sparepart', 'requester', 'approver', 'purchaseOrders');
        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'draft') {
            return back()->with('error', 'Only draft PR can be edited.');
        }

        $purchaseRequest->load('items');
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name', 'uom']);
        return view('purchase-requests.edit', compact('purchaseRequest', 'spareparts'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'draft') {
            return back()->with('error', 'Only draft PR can be edited.');
        }

        $request->validate([
            'request_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $purchaseRequest) {
            $purchaseRequest->update([
                'request_date' => $request->request_date,
                'remarks' => $request->remarks,
            ]);

            $purchaseRequest->items()->delete();
            foreach ($request->items as $item) {
                $purchaseRequest->items()->create($item);
            }
        });

        return redirect()->route('purchase-requests.show', $purchaseRequest)->with('success', 'PR updated successfully.');
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'draft') {
            return back()->with('error', 'Only draft PR can be submitted.');
        }

        $purchaseRequest->update(['status' => 'submitted']);
        return back()->with('success', 'PR submitted for approval.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'submitted') {
            return back()->with('error', 'Only submitted PR can be approved.');
        }

        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);
        return back()->with('success', 'PR approved.');
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'submitted') {
            return back()->with('error', 'Only submitted PR can be rejected.');
        }

        $purchaseRequest->update(['status' => 'rejected']);
        return back()->with('success', 'PR rejected.');
    }
}
