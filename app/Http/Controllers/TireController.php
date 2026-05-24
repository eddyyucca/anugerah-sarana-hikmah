<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitTire;
use App\Models\Sparepart;
use App\Services\OdometerService;
use Illuminate\Http\Request;

class TireController extends Controller
{
    // Daftar semua ban yang terlacak di sistem (terpasang maupun di gudang)
    public function index(Request $request)
    {
        $query = UnitTire::with(['sparepart', 'unit'])
            ->when($request->status === 'terpasang', fn($q) => $q->whereNotNull('unit_id'))
            ->when($request->status === 'gudang',    fn($q) => $q->whereNull('unit_id'))
            ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id));

        $tires = $query->latest()->paginate(20)->withQueryString();
        $units = Unit::where('is_active', true)->orderBy('unit_code')->get(['id', 'unit_code']);
        return view('tires.index', compact('tires', 'units'));
    }

    // Form pasang ban ke unit (ban diambil dari inventory)
    public function installForm(Unit $unit)
    {
        // Hanya sparepart kategori ban (atau filter by part_name mengandung "ban")
        $sparepartQuery = Sparepart::where('is_active', true)
            ->where('stock_on_hand', '>', 0)
            ->where(fn($q) => $q->where('part_name', 'like', '%ban%')
                ->orWhere('part_name', 'like', '%tire%')
                ->orWhere('part_name', 'like', '%tyre%'));

        $spareparts = $sparepartQuery->orderBy('part_name')->get();
        $positions  = $unit->wheel_position_labels;
        $installed  = $unit->tires->keyBy('position_number');

        return view('tires.install', compact('unit', 'spareparts', 'positions', 'installed'));
    }

    public function install(Request $request, Unit $unit)
    {
        $request->validate([
            'sparepart_id'   => 'required|exists:spareparts,id',
            'position_number'=> 'required|integer|min:1|max:' . $unit->wheel_count,
            'km_limit'       => 'required|numeric|min:1000',
            'installed_at'   => 'required|date',
            'notes'          => 'nullable|string|max:200',
        ]);

        $sparepart = Sparepart::findOrFail($request->sparepart_id);
        if ($sparepart->stock_on_hand < 1) {
            return back()->with('error', 'Stok ban tidak mencukupi.');
        }

        // Buat record ban baru dari inventory
        $tire = UnitTire::create([
            'sparepart_id'  => $sparepart->id,
            'km_limit'      => $request->km_limit,
            'notes'         => $request->notes,
        ]);

        // Kurangi stok inventory
        $sparepart->decrement('stock_on_hand');

        // Pasang ke unit
        OdometerService::installTire($tire, $unit, (int)$request->position_number, $request->installed_at);

        return redirect()->route('units.show', $unit)
            ->with('success', "Ban {$sparepart->part_name} dipasang ke posisi #{$request->position_number}.");
    }

    // Pindah ban ke unit/posisi lain
    public function moveForm(UnitTire $tire)
    {
        $units = Unit::where('is_active', true)->orderBy('unit_code')->get();
        return view('tires.move', compact('tire', 'units'));
    }

    public function move(Request $request, UnitTire $tire)
    {
        $request->validate([
            'unit_id'        => 'required|exists:units,id',
            'position_number'=> 'required|integer|min:1',
            'moved_at'       => 'required|date',
        ]);

        $newUnit = Unit::findOrFail($request->unit_id);
        OdometerService::installTire($tire, $newUnit, (int)$request->position_number, $request->moved_at);

        return redirect()->route('units.show', $newUnit)
            ->with('success', "Ban dipindahkan ke {$newUnit->unit_code} posisi #{$request->position_number}.");
    }

    // Lepas ban dari unit (balik ke "gudang" / stok sistem)
    public function remove(Request $request, UnitTire $tire)
    {
        $request->validate([
            'removed_at'     => 'required|date',
            'removed_reason' => 'required|string|max:100',
            'return_to_stock'=> 'boolean',
        ]);

        $unit = $tire->unit;
        OdometerService::removeTire($tire, $unit, $request->removed_at, $request->removed_reason);

        // Kembalikan ke stok inventory jika masih layak pakai
        if ($request->boolean('return_to_stock')) {
            $tire->sparepart->increment('stock_on_hand');
        }

        return redirect()->route('units.show', $unit)->with('success', 'Ban berhasil dilepas.');
    }

    public function show(UnitTire $tire)
    {
        $tire->load(['sparepart', 'unit', 'histories.unit']);
        return view('tires.show', compact('tire'));
    }
}
