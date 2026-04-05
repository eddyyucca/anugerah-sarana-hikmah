<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\WorkOrder;
use App\Models\UnitAvailabilityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DowntimeAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->unit_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $units = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model']);

        // === Downtime per Unit ===
        $query = WorkOrder::select(
                'unit_id',
                DB::raw('COUNT(*) as total_wo'),
                DB::raw('SUM(downtime_hours) as total_downtime'),
                DB::raw('AVG(downtime_hours) as avg_downtime'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_wo")
            )
            ->groupBy('unit_id')
            ->with('unit:id,unit_code,unit_model');

        if ($unitId) $query->where('unit_id', $unitId);
        if ($dateFrom) $query->where('start_time', '>=', $dateFrom);
        if ($dateTo) $query->where('start_time', '<=', $dateTo . ' 23:59:59');

        $downtimePerUnit = $query->orderByDesc('total_downtime')->get();

        // === MTBF & MTTR per Unit ===
        $mtbfData = Unit::active()->get()->map(function ($unit) use ($dateFrom, $dateTo) {
            $woQuery = WorkOrder::where('unit_id', $unit->id)->where('status', 'completed');
            if ($dateFrom) $woQuery->where('start_time', '>=', $dateFrom);
            if ($dateTo) $woQuery->where('start_time', '<=', $dateTo . ' 23:59:59');

            $completedWOs = $woQuery->orderBy('start_time')->get();

            if ($completedWOs->count() < 1) return null;

            // MTTR = avg repair time
            $mttr = $completedWOs->avg('downtime_hours');

            // MTBF = avg time between failures
            $mtbf = 0;
            if ($completedWOs->count() >= 2) {
                $gaps = [];
                for ($i = 1; $i < $completedWOs->count(); $i++) {
                    $prevEnd = $completedWOs[$i - 1]->end_time;
                    $nextStart = $completedWOs[$i]->start_time;
                    if ($prevEnd && $nextStart) {
                        $gaps[] = $prevEnd->diffInHours($nextStart);
                    }
                }
                $mtbf = count($gaps) > 0 ? array_sum($gaps) / count($gaps) : 0;
            }

            return (object) [
                'unit_id' => $unit->id,
                'unit_code' => $unit->unit_code,
                'unit_model' => $unit->unit_model,
                'total_failures' => $completedWOs->count(),
                'total_downtime' => round($completedWOs->sum('downtime_hours'), 2),
                'mttr' => round($mttr, 2),
                'mtbf' => round($mtbf, 2),
            ];
        })->filter()->sortByDesc('total_failures')->values();

        // === Top Breakdown Reasons ===
        $breakdownQuery = WorkOrder::where('maintenance_type', 'corrective')
            ->whereNotNull('complaint')
            ->where('complaint', '!=', '');
        if ($dateFrom) $breakdownQuery->where('start_time', '>=', $dateFrom);
        if ($dateTo) $breakdownQuery->where('start_time', '<=', $dateTo . ' 23:59:59');

        $topBreakdowns = $breakdownQuery
            ->select('complaint', DB::raw('COUNT(*) as count'), DB::raw('SUM(downtime_hours) as total_downtime'))
            ->groupBy('complaint')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        // === Availability Summary ===
        $availQuery = UnitAvailabilityLog::query();
        if ($dateFrom) $availQuery->where('date', '>=', $dateFrom);
        if ($dateTo) $availQuery->where('date', '<=', $dateTo);

        $availSummary = $availQuery->select(
                'unit_id',
                DB::raw('AVG(availability_percent) as avg_avail'),
                DB::raw('SUM(downtime_hours) as total_downtime'),
                DB::raw('SUM(scheduled_hours) as total_scheduled'),
                DB::raw('COUNT(*) as days_counted')
            )
            ->groupBy('unit_id')
            ->with('unit:id,unit_code,unit_model')
            ->orderBy('avg_avail')
            ->get();

        // === Overall KPIs ===
        $totalDowntime = $downtimePerUnit->sum('total_downtime');
        $totalWO = $downtimePerUnit->sum('total_wo');
        $avgMTTR = $mtbfData->count() > 0 ? round($mtbfData->avg('mttr'), 2) : 0;
        $avgMTBF = $mtbfData->count() > 0 ? round($mtbfData->avg('mtbf'), 2) : 0;
        $overallAvail = $availSummary->count() > 0 ? round($availSummary->avg('avg_avail'), 2) : 100;

        // === Chart: Downtime trend by month ===
        $dtTrendQuery = WorkOrder::select(
                DB::raw("DATE_FORMAT(start_time, '%Y-%m') as month"),
                DB::raw('SUM(downtime_hours) as total'),
                DB::raw('COUNT(*) as wo_count')
            )->whereNotNull('start_time');
        if ($dateFrom) $dtTrendQuery->where('start_time', '>=', $dateFrom);
        if ($dateTo) $dtTrendQuery->where('start_time', '<=', $dateTo . ' 23:59:59');
        $downtimeTrend = $dtTrendQuery->groupBy('month')->orderBy('month')->get();

        // === Chart: WO by type ===
        $woByType = WorkOrder::select('maintenance_type', DB::raw('COUNT(*) as total'))
            ->groupBy('maintenance_type')->get();

        return view('downtime.index', compact(
            'units', 'downtimePerUnit', 'mtbfData', 'topBreakdowns',
            'availSummary', 'totalDowntime', 'totalWO', 'avgMTTR', 'avgMTBF',
            'overallAvail', 'downtimeTrend', 'woByType'
        ));
    }
}
