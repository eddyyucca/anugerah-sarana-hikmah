<?php

namespace App\Http\Controllers;

use App\Models\TireDamageReport;
use App\Models\UnitTire;
use App\Models\Unit;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;

class TireDamageReportController extends Controller
{
    public function index(Request $request)
    {
        $query = TireDamageReport::with(['unit', 'unitTire.sparepart'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('report_date');

        $reports = $query->paginate(20)->withQueryString();
        return view('tire-damage-reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $unitTire = $request->unit_tire_id
            ? UnitTire::with(['sparepart', 'unit'])->findOrFail($request->unit_tire_id)
            : null;

        $units    = Unit::where('is_active', true)->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model', 'current_odometer']);
        $tires    = UnitTire::with(['sparepart', 'unit'])->whereNotNull('unit_id')->get();

        return view('tire-damage-reports.create', compact('unitTire', 'units', 'tires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_tire_id'       => 'required|exists:unit_tires,id',
            'unit_id'            => 'required|exists:units,id',
            'report_date'        => 'required|date',
            'damage_type'        => 'required|in:puncture,sidewall,bead,tread,manufacturing_defect,other',
            'damage_description' => 'required|string',
            'is_warranty_claim'  => 'boolean',
            'notes'              => 'nullable|string',
        ]);

        $tire = UnitTire::findOrFail($request->unit_tire_id);

        TireDamageReport::create([
            'report_no'            => DocumentNumberService::generateBA(),
            'unit_tire_id'         => $tire->id,
            'unit_id'              => $request->unit_id,
            'report_date'          => $request->report_date,
            'km_at_damage'         => $tire->unit?->current_odometer,
            'km_used_when_damaged' => $tire->total_km,
            'installed_at'         => $tire->installed_at,
            'damage_type'          => $request->damage_type,
            'damage_description'   => $request->damage_description,
            'is_warranty_claim'    => $request->boolean('is_warranty_claim'),
            'notes'                => $request->notes,
            'status'               => 'draft',
        ]);

        return redirect()->route('tire-damage-reports.index')->with('success', 'BA Kerusakan Ban berhasil dibuat.');
    }

    public function show(TireDamageReport $tireDamageReport)
    {
        $tireDamageReport->load(['unitTire.sparepart', 'unit']);
        return view('tire-damage-reports.show', compact('tireDamageReport'));
    }

    public function approve(TireDamageReport $tireDamageReport)
    {
        if ($tireDamageReport->status !== 'draft') {
            return back()->with('error', 'BA sudah disetujui.');
        }

        $tireDamageReport->update([
            'status'      => 'approved',
            'approved_by' => auth()->user()->name ?? 'system',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'BA Kerusakan Ban disetujui.');
    }
}
