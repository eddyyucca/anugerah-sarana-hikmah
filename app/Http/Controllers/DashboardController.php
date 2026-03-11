<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use App\Models\RepairCostSummary;
use App\Models\UnitAvailabilityLog;
use App\Models\Sparepart;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUnits = Unit::active()->count();
        $available = Unit::active()->status('available')->count();
        $underRepair = Unit::active()->status('under_repair')->count();
        $standby = Unit::active()->status('standby')->count();

        $openPR = PurchaseRequest::whereIn('status', ['draft', 'submitted'])->count();
        $openPO = PurchaseOrder::whereIn('status', ['draft', 'issued', 'partial'])->count();
        $openWO = WorkOrder::whereIn('status', ['open', 'in_progress', 'waiting_part'])->count();

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $monthlyRepairCost = RepairCostSummary::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_cost');

        // Chart: Repair cost per unit (top 10)
        $costPerUnit = RepairCostSummary::select('unit_id', DB::raw('SUM(total_cost) as total'))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('unit_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('unit:id,unit_code')
            ->get();

        // Chart: Availability trend (last 30 days avg)
        $availTrend = UnitAvailabilityLog::select(
                'date',
                DB::raw('AVG(availability_percent) as avg_avail')
            )
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent WOs
        $recentWOs = WorkOrder::with('unit:id,unit_code', 'technician:id,technician_name')
            ->latest()
            ->limit(10)
            ->get();

        // Top sparepart cost
        $topSpareparts = DB::table('goods_issue_items')
            ->join('goods_issues', 'goods_issues.id', '=', 'goods_issue_items.goods_issue_id')
            ->join('spareparts', 'spareparts.id', '=', 'goods_issue_items.sparepart_id')
            ->where('goods_issues.status', 'posted')
            ->whereBetween('goods_issues.issue_date', [$monthStart, $monthEnd])
            ->select('spareparts.part_name', DB::raw('SUM(goods_issue_items.total_price) as total'))
            ->groupBy('spareparts.part_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalUnits', 'available', 'underRepair', 'standby',
            'openPR', 'openPO', 'openWO', 'monthlyRepairCost',
            'costPerUnit', 'availTrend', 'recentWOs', 'topSpareparts'
        ));
    }
}
