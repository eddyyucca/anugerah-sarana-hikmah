<?php

namespace App\Http\Controllers;

use App\Models\P2hCheck;
use App\Models\Timesheet;
use App\Models\FitToWork;
use App\Models\Unit;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperasiLogController extends Controller
{
    /**
     * Log harian gabungan: FTW + P2H + Timesheet per tanggal/shift
     */
    public function log(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        // Ambil semua P2H dalam rentang, beserta FTW dan Timesheet-nya
        $p2hList = P2hCheck::with([
            'unit:id,unit_code,unit_model',
            'operator:id,operator_code,operator_name',
            'timesheet',
        ])
        ->whereBetween('check_date', [$dateFrom, $dateTo])
        ->when($request->filled('unit_id'),     fn($q) => $q->where('unit_id', $request->unit_id))
        ->when($request->filled('operator_id'), fn($q) => $q->where('operator_id', $request->operator_id))
        ->when($request->filled('shift'),       fn($q) => $q->where('shift', $request->shift))
        ->orderByDesc('check_date')
        ->orderBy('shift')
        ->paginate(30)
        ->withQueryString();

        // FTW per operator per tanggal (untuk lookup di tabel)
        $ftwMap = FitToWork::whereBetween('check_date', [$dateFrom, $dateTo])
            ->get()
            ->keyBy(fn($f) => $f->operator_id . '_' . $f->check_date . '_' . $f->shift);

        $units     = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code']);
        $operators = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);

        return view('operasi.log', compact('p2hList', 'ftwMap', 'units', 'operators', 'dateFrom', 'dateTo'));
    }

    /**
     * Laporan & Analisa Akumulasi P2H + Timesheet
     */
    public function laporan(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        // ── Ringkasan Keseluruhan ──────────────────────────────────────────────
        $totalP2H     = P2hCheck::whereBetween('check_date', [$dateFrom, $dateTo])->count();
        $totalLayak   = P2hCheck::whereBetween('check_date', [$dateFrom, $dateTo])->where('overall_status', 'layak')->count();
        $totalCatatan = P2hCheck::whereBetween('check_date', [$dateFrom, $dateTo])->where('overall_status', 'layak_catatan')->count();
        $totalTidakLayak = P2hCheck::whereBetween('check_date', [$dateFrom, $dateTo])->where('overall_status', 'tidak_layak')->count();

        $totalTS      = Timesheet::whereBetween('shift_date', [$dateFrom, $dateTo])->count();
        $totalRetase  = Timesheet::whereBetween('shift_date', [$dateFrom, $dateTo])->sum('retase');
        $totalHmUsage = Timesheet::whereBetween('shift_date', [$dateFrom, $dateTo])->sum('working_hours');
        $avgRetase    = $totalTS > 0 ? round($totalRetase / $totalTS, 1) : 0;

        $totalFTW     = FitToWork::whereBetween('check_date', [$dateFrom, $dateTo])->count();
        $totalFit     = FitToWork::whereBetween('check_date', [$dateFrom, $dateTo])->where('is_fit', true)->count();

        // ── Per Unit ──────────────────────────────────────────────────────────
        $unitStats = Unit::active()
            ->with('category:id,name')
            ->get()
            ->map(function ($unit) use ($dateFrom, $dateTo) {
                $p2h = P2hCheck::where('unit_id', $unit->id)
                    ->whereBetween('check_date', [$dateFrom, $dateTo]);
                $ts  = Timesheet::where('unit_id', $unit->id)
                    ->whereBetween('shift_date', [$dateFrom, $dateTo]);

                $unit->total_p2h        = $p2h->count();
                $unit->p2h_layak        = (clone $p2h)->where('overall_status', 'layak')->count();
                $unit->p2h_tidak_layak  = (clone $p2h)->where('overall_status', 'tidak_layak')->count();
                $unit->total_shift      = $ts->count();
                $unit->total_retase     = (int) (clone $ts)->sum('retase');
                $unit->total_hm_usage   = (float) (clone $ts)->sum('working_hours');
                $unit->avg_retase       = $unit->total_shift > 0
                    ? round($unit->total_retase / $unit->total_shift, 1) : 0;
                return $unit;
            })
            ->filter(fn($u) => $u->total_p2h > 0 || $u->total_shift > 0)
            ->sortByDesc('total_retase')
            ->values();

        // ── Per Operator ──────────────────────────────────────────────────────
        $operatorStats = Operator::active()
            ->get()
            ->map(function ($op) use ($dateFrom, $dateTo) {
                $p2h = P2hCheck::where('operator_id', $op->id)
                    ->whereBetween('check_date', [$dateFrom, $dateTo]);
                $ts  = Timesheet::where('operator_id', $op->id)
                    ->whereBetween('shift_date', [$dateFrom, $dateTo]);
                $ftw = FitToWork::where('operator_id', $op->id)
                    ->whereBetween('check_date', [$dateFrom, $dateTo]);

                $op->total_p2h      = $p2h->count();
                $op->total_shift    = $ts->count();
                $op->total_retase   = (int) (clone $ts)->sum('retase');
                $op->total_hm       = (float) (clone $ts)->sum('working_hours');
                $op->avg_retase     = $op->total_shift > 0
                    ? round($op->total_retase / $op->total_shift, 1) : 0;
                $op->ftw_total      = $ftw->count();
                $op->ftw_fit        = (clone $ftw)->where('is_fit', true)->count();
                return $op;
            })
            ->filter(fn($o) => $o->total_p2h > 0 || $o->total_shift > 0)
            ->sortByDesc('total_retase')
            ->values();

        // ── Trend Harian (7 hari terakhir atau sesuai filter) ─────────────────
        $dailyTrend = Timesheet::whereBetween('shift_date', [$dateFrom, $dateTo])
            ->select('shift_date', DB::raw('SUM(retase) as total_retase'), DB::raw('SUM(working_hours) as total_hm'), DB::raw('COUNT(*) as total_shift'))
            ->groupBy('shift_date')
            ->orderBy('shift_date')
            ->get();

        return view('operasi.laporan', compact(
            'dateFrom', 'dateTo',
            'totalP2H', 'totalLayak', 'totalCatatan', 'totalTidakLayak',
            'totalTS', 'totalRetase', 'totalHmUsage', 'avgRetase',
            'totalFTW', 'totalFit',
            'unitStats', 'operatorStats', 'dailyTrend'
        ));
    }
}
