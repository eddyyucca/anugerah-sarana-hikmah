<?php

namespace App\Http\Controllers;

use App\Models\P2hCheck;
use App\Models\P2hCheckItem;
use App\Models\Unit;
use App\Models\Operator;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class P2hCheckController extends Controller
{
    // Default P2H checklist template per category
    private function getChecklistTemplate(): array
    {
        return [
            'Engine' => [
                'Kondisi engine oil level',
                'Suara engine / abnormal noise',
                'Coolant level',
                'Exhaust smoke / warna asap',
                'Engine temperature normal',
            ],
            'Hydraulic' => [
                'Hydraulic oil level',
                'Kebocoran hose / fitting',
                'Fungsi cylinder boom/arm/bucket',
                'Swing function normal',
            ],
            'Electrical' => [
                'Kondisi battery / tegangan',
                'Lampu kerja / work light',
                'Horn / klakson',
                'Instrument panel / gauge',
                'Wiper & washer',
            ],
            'Brake & Steering' => [
                'Fungsi service brake',
                'Fungsi parking brake',
                'Fungsi steering / kemudi',
                'Brake fluid level',
            ],
            'Body & Cabin' => [
                'Kaca cabin / windshield',
                'Seat belt',
                'Pintu cabin / lock',
                'Mirror / kaca spion',
                'Kebersihan cabin',
            ],
            'Safety Equipment' => [
                'Fire extinguisher / APAR',
                'Rotating beacon / strobe light',
                'Back-up alarm / mundur',
                'Safety pin / lock',
                'Reflector / sticker safety',
            ],
            'Undercarriage / Tire' => [
                'Kondisi track / ban',
                'Track tension / tekanan ban',
                'Kondisi roller / idler',
                'Kebocoran grease',
            ],
        ];
    }

    public function index(Request $request)
    {
        $query = P2hCheck::with('unit:id,unit_code,unit_model', 'operator:id,operator_code,operator_name')
            ->withCount('items');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('p2h_number', 'like', "%{$s}%")
                ->orWhereHas('unit', fn($u) => $u->where('unit_code', 'like', "%{$s}%"))
                ->orWhereHas('operator', fn($o) => $o->where('operator_name', 'like', "%{$s}%")));
        }
        if ($request->filled('status')) {
            $query->where('overall_status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('check_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_date', '<=', $request->date_to);
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $checks = $query->latest()->paginate(25)->withQueryString();
        $units = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code']);

        return view('p2h.index', compact('checks', 'units'));
    }

    public function create()
    {
        $p2hNumber = DocumentNumberService::generateP2H();
        $units = Unit::active()->status('available')->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model', 'hour_meter']);
        $operators = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);
        $checklist = $this->getChecklistTemplate();

        return view('p2h.create', compact('p2hNumber', 'units', 'operators', 'checklist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'operator_id' => 'required|exists:operators,id',
            'check_date' => 'required|date',
            'shift' => 'required|in:day,night',
            'hour_meter_start' => 'nullable|numeric|min:0',
            'km_start' => 'nullable|numeric|min:0',
            'general_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string|max:50',
            'items.*.check_item' => 'required|string|max:150',
            'items.*.condition' => 'required|in:good,warning,bad,na',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Determine overall status
            $hasBad = collect($request->items)->contains('condition', 'bad');
            $hasWarning = collect($request->items)->contains('condition', 'warning');
            $overall = $hasBad ? 'tidak_layak' : ($hasWarning ? 'layak_catatan' : 'layak');

            $check = P2hCheck::create([
                'p2h_number' => DocumentNumberService::generateP2H(),
                'unit_id' => $request->unit_id,
                'operator_id' => $request->operator_id,
                'check_date' => $request->check_date,
                'shift' => $request->shift,
                'hour_meter_start' => $request->hour_meter_start ?? 0,
                'km_start' => $request->km_start ?? 0,
                'overall_status' => $overall,
                'general_notes' => $request->general_notes,
            ]);

            foreach ($request->items as $item) {
                $check->items()->create($item);
            }
        });

        return redirect()->route('p2h.index')->with('success', 'P2H Check submitted successfully.');
    }

    // === STANDALONE FORM (tanpa sidebar, untuk operator langsung) ===
    public function formOperator()
    {
        $units = Unit::active()->status('available')->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model', 'hour_meter']);
        $operators = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);
        $checklist = $this->getChecklistTemplate();

        return view('p2h.form-operator', compact('units', 'operators', 'checklist'));
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'operator_id' => 'required|exists:operators,id',
            'check_date' => 'required|date',
            'shift' => 'required|in:day,night',
            'hour_meter_start' => 'nullable|numeric|min:0',
            'km_start' => 'nullable|numeric|min:0',
            'general_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string|max:50',
            'items.*.check_item' => 'required|string|max:150',
            'items.*.condition' => 'required|in:good,warning,bad,na',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $hasBad = collect($request->items)->contains('condition', 'bad');
            $hasWarning = collect($request->items)->contains('condition', 'warning');
            $overall = $hasBad ? 'tidak_layak' : ($hasWarning ? 'layak_catatan' : 'layak');

            $check = P2hCheck::create([
                'p2h_number' => DocumentNumberService::generateP2H(),
                'unit_id' => $request->unit_id,
                'operator_id' => $request->operator_id,
                'check_date' => $request->check_date,
                'shift' => $request->shift,
                'hour_meter_start' => $request->hour_meter_start ?? 0,
                'km_start' => $request->km_start ?? 0,
                'overall_status' => $overall,
                'general_notes' => $request->general_notes,
            ]);

            foreach ($request->items as $item) {
                $check->items()->create($item);
            }
        });

        return redirect()->route('p2h.form-operator')->with('success', 'P2H berhasil disubmit! Terima kasih.');
    }

    public function show(P2hCheck $p2h)
    {
        $p2h->load('unit.category', 'operator', 'reviewer', 'items');

        // Group items by category
        $groupedItems = $p2h->items->groupBy('category');

        return view('p2h.show', compact('p2h', 'groupedItems'));
    }

    public function review(P2hCheck $p2h)
    {
        $p2h->update([
            'reviewed_by' => auth()->id() ?? 1,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'P2H reviewed successfully.');
    }

    public function summary(Request $request)
    {
        // Unit fitness summary
        $units = Unit::active()->with('category:id,name')->get()->map(function ($unit) use ($request) {
            $query = P2hCheck::where('unit_id', $unit->id);
            if ($request->filled('date_from')) $query->where('check_date', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->where('check_date', '<=', $request->date_to);

            $checks = $query->get();
            $unit->total_checks = $checks->count();
            $unit->layak_count = $checks->where('overall_status', 'layak')->count();
            $unit->catatan_count = $checks->where('overall_status', 'layak_catatan')->count();
            $unit->tidak_layak_count = $checks->where('overall_status', 'tidak_layak')->count();
            $unit->fitness_percent = $unit->total_checks > 0
                ? round(($unit->layak_count / $unit->total_checks) * 100, 1)
                : 0;
            $unit->last_check = $checks->sortByDesc('check_date')->first();
            return $unit;
        })->filter(fn($u) => $u->total_checks > 0)->sortByDesc('total_checks');

        // Operator summary
        $operators = Operator::active()->get()->map(function ($op) use ($request) {
            $query = P2hCheck::where('operator_id', $op->id);
            if ($request->filled('date_from')) $query->where('check_date', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->where('check_date', '<=', $request->date_to);

            $checks = $query->with('unit:id,unit_code')->get();
            $op->total_checks = $checks->count();
            $op->layak_count = $checks->where('overall_status', 'layak')->count();
            $op->tidak_layak_count = $checks->where('overall_status', 'tidak_layak')->count();
            $op->last_check = $checks->sortByDesc('check_date')->first();
            return $op;
        })->filter(fn($o) => $o->total_checks > 0)->sortByDesc('total_checks');

        // Overall stats
        $totalChecks = P2hCheck::count();
        $layakTotal = P2hCheck::where('overall_status', 'layak')->count();
        $catatanTotal = P2hCheck::where('overall_status', 'layak_catatan')->count();
        $tidakLayakTotal = P2hCheck::where('overall_status', 'tidak_layak')->count();

        return view('p2h.summary', compact(
            'units', 'operators',
            'totalChecks', 'layakTotal', 'catatanTotal', 'tidakLayakTotal'
        ));
    }
}
