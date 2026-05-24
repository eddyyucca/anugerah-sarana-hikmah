<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\MaintenanceLog;
use App\Models\Sparepart;
use App\Models\Unit;
use App\Services\OdometerService;
use Illuminate\Http\Request;

class MaintenanceItemController extends Controller
{
    public function index()
    {
        $items       = MaintenanceItem::with('sparepart')->withCount('maintenanceLogs')->orderBy('name')->get();
        $alerts      = OdometerService::getAlerts();
        $dangerCount = count(array_filter($alerts, fn($a) => $a['severity'] === 'danger'));
        $warningCount= count(array_filter($alerts, fn($a) => $a['severity'] === 'warning'));
        $units       = Unit::where('is_active', true)->orderBy('unit_code')->get();
        $spareparts  = Sparepart::where('is_active', true)->orderBy('part_name')->get();

        return view('maintenance.index', compact('items', 'alerts', 'dangerCount', 'warningCount', 'units', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'interval_km'     => 'required|numeric|min:100',
            'alert_before_km' => 'required|numeric|min:0',
            'sparepart_id'    => 'nullable|exists:spareparts,id',
            'qty_per_service' => 'nullable|integer|min:1',
            'description'     => 'nullable|string|max:500',
        ]);

        MaintenanceItem::create($request->only([
            'name', 'interval_km', 'alert_before_km',
            'sparepart_id', 'qty_per_service', 'description',
        ]) + ['is_active' => true]);

        return back()->with('success', 'Item maintenance berhasil ditambahkan.');
    }

    public function update(Request $request, MaintenanceItem $maintenanceItem)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'interval_km'     => 'required|numeric|min:100',
            'alert_before_km' => 'required|numeric|min:0',
            'sparepart_id'    => 'nullable|exists:spareparts,id',
            'qty_per_service' => 'nullable|integer|min:1',
            'description'     => 'nullable|string|max:500',
        ]);

        $maintenanceItem->update($request->only([
            'name', 'interval_km', 'alert_before_km',
            'sparepart_id', 'qty_per_service', 'description',
        ]));
        return back()->with('success', 'Item maintenance berhasil diperbarui.');
    }

    public function destroy(MaintenanceItem $maintenanceItem)
    {
        $maintenanceItem->delete();
        return back()->with('success', 'Item maintenance dihapus.');
    }

    public function logStore(Request $request)
    {
        $request->validate([
            'maintenance_item_id' => 'required|exists:maintenance_items,id',
            'unit_id'             => 'required|exists:units,id',
            'service_date'        => 'required|date',
            'sparepart_id'        => 'nullable|exists:spareparts,id',
            'qty_used'            => 'nullable|integer|min:1',
            'performed_by'        => 'nullable|string|max:100',
            'cost'                => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:500',
        ]);

        $item = MaintenanceItem::findOrFail($request->maintenance_item_id);
        $unit = Unit::findOrFail($request->unit_id);

        // Cek stok jika ada sparepart
        $sparepartId = $request->sparepart_id ?? $item->sparepart_id;
        $qtyUsed     = (int)($request->qty_used ?? $item->qty_per_service ?? 1);

        if ($sparepartId && $qtyUsed > 0) {
            $sp = Sparepart::find($sparepartId);
            if ($sp && $sp->stock_on_hand < $qtyUsed) {
                return back()->with('error', "Stok {$sp->part_name} tidak mencukupi (stok: {$sp->stock_on_hand}, dibutuhkan: {$qtyUsed}).");
            }
        }

        OdometerService::recordMaintenance(
            $item, $unit,
            $request->service_date,
            $request->performed_by,
            $request->cost ? (float)$request->cost : null,
            $request->notes,
            $sparepartId ?: null,
            $qtyUsed
        );

        $sparepartName = $sparepartId ? (Sparepart::find($sparepartId)?->part_name ?? '') : '';
        $stockInfo = $sparepartId && $qtyUsed > 0 ? " | Stok {$sparepartName} berkurang {$qtyUsed} pcs." : '';

        return back()->with('success', "Maintenance {$item->name} unit {$unit->unit_code} dicatat.{$stockInfo}");
    }
}
