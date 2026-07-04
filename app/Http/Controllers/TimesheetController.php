<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\P2hCheck;
use App\Models\Unit;
use App\Models\Operator;
use App\Services\DocumentNumberService;
use App\Services\OdometerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $query = Timesheet::with(
            'unit:id,unit_code,unit_model',
            'operator:id,operator_code,operator_name',
            'p2h:id,p2h_number,overall_status'
        )->latest('shift_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('ts_number', 'like', "%{$s}%")
                ->orWhereHas('unit', fn($u) => $u->where('unit_code', 'like', "%{$s}%"))
                ->orWhereHas('operator', fn($o) => $o->where('operator_name', 'like', "%{$s}%")));
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('date_from')) {
            $query->where('shift_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('shift_date', '<=', $request->date_to);
        }
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $timesheets = $query->paginate(25)->withQueryString();
        $units = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code']);

        return view('timesheets.index', compact('timesheets', 'units'));
    }

    public function create()
    {
        // P2H yang belum punya timesheet
        $p2hList = P2hCheck::with('unit:id,unit_code,unit_model', 'operator:id,operator_code,operator_name')
            ->whereDoesntHave('timesheet')
            ->where('overall_status', '!=', 'tidak_layak')
            ->orderByDesc('check_date')
            ->get();

        return view('timesheets.create', compact('p2hList'));
    }

    public function store(Request $request)
    {
        $p2h = P2hCheck::findOrFail($request->p2h_check_id);
        $hmStart = (float) $p2h->hour_meter_start;

        $request->validate([
            'p2h_check_id'   => 'required|exists:p2h_checks,id|unique:timesheets,p2h_check_id',
            'hour_meter_end' => "required|numeric|min:{$hmStart}",
            'retase'         => 'required|integer|min:0',
            'notes'          => 'nullable|string',
        ], [
            'p2h_check_id.unique'   => 'P2H ini sudah memiliki timesheet.',
            'hour_meter_end.min'    => "HM akhir tidak boleh kurang dari HM awal ({$hmStart}).",
        ]);

        DB::transaction(function () use ($request, $p2h, $hmStart) {
            $hmEnd = (float) $request->hour_meter_end;
            $workingHours = round($hmEnd - $hmStart, 2);

            Timesheet::create([
                'ts_number'         => DocumentNumberService::generateTS(),
                'p2h_check_id'      => $p2h->id,
                'unit_id'           => $p2h->unit_id,
                'operator_id'       => $p2h->operator_id,
                'shift_date'        => $p2h->check_date,
                'shift'             => $p2h->shift,
                'hour_meter_start'  => $hmStart,
                'hour_meter_end'    => $hmEnd,
                'working_hours'     => $workingHours,
                'retase'            => $request->retase,
                'notes'             => $request->notes,
                'submitted_by'      => auth()->id(),
            ]);

            // Update HM unit jika lebih besar
            $unit = $p2h->unit;
            if ($hmEnd > (float) $unit->hour_meter) {
                $unit->update(['hour_meter' => $hmEnd]);
            }
        });

        return redirect()->route('timesheets.index')->with('success', 'Timesheet berhasil disimpan.');
    }

    public function show(Timesheet $timesheet)
    {
        $timesheet->load('p2h.items', 'unit.category', 'operator', 'submitter');

        return view('timesheets.show', compact('timesheet'));
    }

    // ── Standalone (operator tanpa login) ────────────────────────────────────

    public function formOperator()
    {
        // P2H yang belum punya timesheet, layak/layak_catatan
        $p2hList = P2hCheck::with('unit:id,unit_code,unit_model', 'operator:id,operator_code,operator_name')
            ->whereDoesntHave('timesheet')
            ->where('overall_status', '!=', 'tidak_layak')
            ->orderByDesc('check_date')
            ->get();

        return view('timesheets.form-operator', compact('p2hList'));
    }

    public function storeOperator(Request $request)
    {
        $p2h    = P2hCheck::with('unit')->findOrFail($request->p2h_check_id);
        $kmStart = (float) $p2h->km_start;
        $hmStart = (float) $p2h->hour_meter_start;

        $request->validate([
            'p2h_check_id'    => 'required|exists:p2h_checks,id|unique:timesheets,p2h_check_id',
            'km_end'          => "required|numeric|min:{$kmStart}",
            'working_hours'   => 'required|numeric|min:0|max:24',
            'retase'          => 'required|integer|min:0',
            'notes'           => 'nullable|string',
            'odo_end_photo'   => 'nullable|image|max:5120',
        ], [
            'p2h_check_id.unique' => 'P2H ini sudah memiliki timesheet.',
            'km_end.min'          => "Odometer akhir tidak boleh kurang dari awal ({$kmStart} km).",
            'odo_end_photo.image'   => 'File foto ODO akhir harus berupa gambar.',
        ]);

        DB::transaction(function () use ($request, $p2h, $kmStart, $hmStart) {
            $kmEnd      = (float) $request->km_end;
            $kmTraveled = round(max(0, $kmEnd - $kmStart), 2);
            $retase     = (int) $request->retase;

            // Hitung km per ritase & deteksi anomali
            $kmPerRitase = null;
            $discrepancyFlag = 0;
            if ($retase > 0 && $kmTraveled > 0) {
                $kmPerRitase = round($kmTraveled / $retase, 2);
                // Flag anomali jika km/ritase < 1 km atau > 100 km (tidak wajar)
                if ($kmPerRitase < 1 || $kmPerRitase > 100) {
                    $discrepancyFlag = 1;
                }
            } elseif ($kmTraveled > 5 && $retase === 0) {
                // ODO bergerak tapi ritase 0 — anomali
                $discrepancyFlag = 1;
            }

            // Upload foto ODO akhir
            $endPhotoPath = null;
            if ($request->hasFile('odo_end_photo')) {
                $endPhotoPath = $request->file('odo_end_photo')
                    ->store('odometer-photos', 'public');
            }

            Timesheet::create([
                'ts_number'           => DocumentNumberService::generateTS(),
                'p2h_check_id'        => $p2h->id,
                'unit_id'             => $p2h->unit_id,
                'operator_id'         => $p2h->operator_id,
                'shift_date'          => $p2h->check_date,
                'shift'               => $p2h->shift,
                'hour_meter_start'    => $hmStart,
                'hour_meter_end'      => $hmStart,
                'km_end'              => $kmEnd,
                'km_traveled'         => $kmTraveled,
                'working_hours'       => $request->working_hours,
                'retase'              => $retase,
                'notes'               => $request->notes,
                'submitted_by'        => null,
                'odo_end_photo'       => $endPhotoPath,
                'km_per_ritase'       => $kmPerRitase,
                'odo_discrepancy_flag'=> $discrepancyFlag,
            ]);

            // Record odometer reading ke sistem
            $unit = $p2h->unit;
            if ($kmEnd > (float) $unit->current_odometer) {
                OdometerService::recordReading(
                    $unit,
                    $kmEnd,
                    $p2h->check_date->format('Y-m-d'),
                    $p2h->operator->operator_name ?? 'operator',
                    'timesheet',
                    "Odometer akhir shift dari TS operator"
                );
            }
        });

        return redirect()->route('operator.ts-form')->with('success', 'Timesheet berhasil disubmit!');
    }
}
