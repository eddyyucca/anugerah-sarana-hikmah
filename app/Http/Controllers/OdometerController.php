<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitOdometerReading;
use App\Services\OdometerService;
use Illuminate\Http\Request;

class OdometerController extends Controller
{
    public function index()
    {
        $units = Unit::where('is_active', true)->orderBy('unit_code')->get();
        $alerts = OdometerService::getAlerts();
        return view('odometer.index', compact('units', 'alerts'));
    }

    public function history(Unit $unit)
    {
        $readings = UnitOdometerReading::where('unit_id', $unit->id)
            ->orderByDesc('reading_date')->paginate(20);
        return view('odometer.history', compact('unit', 'readings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id'      => 'required|exists:units,id',
            'odometer_km'  => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        if ($request->odometer_km < $unit->current_odometer) {
            return back()->with('error', "Odometer baru ({$request->odometer_km} km) tidak boleh lebih kecil dari odometer saat ini ({$unit->current_odometer} km).");
        }

        OdometerService::recordReading(
            $unit,
            (float) $request->odometer_km,
            $request->reading_date,
            auth()->user()->name,
            $request->notes
        );

        return back()->with('success', "Odometer unit {$unit->unit_code} berhasil dicatat. Delta: " . number_format($request->odometer_km - $unit->current_odometer, 1) . " km.");
    }
}
