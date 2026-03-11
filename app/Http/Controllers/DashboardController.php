<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Sparepart;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\GoodsIssue;
use App\Models\WorkOrder;
use App\Models\RepairCostSummary;
use App\Models\UnitAvailabilityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // === KPI Counts ===
        $totalUnits = Unit::active()->count();
        $available = Unit::active()->status('available')->count();
        $underRepair = Unit::active()->status('under_repair')->count();
        $standby = Unit::active()->status('standby')->count();

        $openPR = PurchaseRequest::whereIn('status', ['draft', 'submitted'])->count();
        $openPO = PurchaseOrder::whereIn('status', ['draft', 'issued', 'partial'])->count();
        $openWO = WorkOrder::whereIn('status', ['open', 'in_progress', 'waiting_part'])->count();
        $completedWO = WorkOrder::where('status', 'completed')->count();

        // === Find latest data month for cost queries ===
        $latestCost = RepairCostSummary::max('created_at');
        if ($latestCost) {
            $costStart = \Carbon\Carbon::parse($latestCost)->startOfMonth()->toDateString();
            $costEnd = \Carbon\Carbon::parse($latestCost)->endOfMonth()->toDateString();
        } else {
            $costStart = now()->startOfMonth()->toDateString();
            $costEnd = now()->endOfMonth()->toDateString();
        }

        $monthlyRepairCost = RepairCostSummary::whereBetween('created_at', [$costStart, $costEnd . ' 23:59:59'])
            ->sum('total_cost');

        $totalRepairCostAll = RepairCostSummary::sum('total_cost');

        // === Low stock count ===
        $lowStockCount = Sparepart::active()->lowStock()->count();
        $totalSpareparts = Sparepart::active()->count();

        // === Chart: Repair cost per unit - ALL TIME top 10 ===
        $costPerUnit = RepairCostSummary::select('unit_id', DB::raw('SUM(total_cost) as total'))
            ->groupBy('unit_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('unit:id,unit_code')
            ->get();

        // === Chart: Availability trend (all available data) ===
        $availTrend = UnitAvailabilityLog::select(
                'date',
                DB::raw('AVG(availability_percent) as avg_avail'),
                DB::raw('COUNT(DISTINCT unit_id) as unit_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // === Chart: Cost breakdown (sparepart vs labor vs vendor vs consumable) ===
        $costBreakdown = [
            'sparepart' => RepairCostSummary::sum('sparepart_cost'),
            'labor' => RepairCostSummary::sum('labor_cost'),
            'vendor' => RepairCostSummary::sum('vendor_cost'),
            'consumable' => RepairCostSummary::sum('consumable_cost'),
        ];

        // === Chart: Monthly cost trend ===
        $monthlyCostTrend = RepairCostSummary::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(total_cost) as total'),
                DB::raw('SUM(sparepart_cost) as sparepart'),
                DB::raw('SUM(labor_cost) as labor'),
                DB::raw('SUM(vendor_cost) as vendor')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // === Chart: Top sparepart cost ALL TIME ===
        $topSpareparts = DB::table('goods_issue_items')
            ->join('goods_issues', 'goods_issues.id', '=', 'goods_issue_items.goods_issue_id')
            ->join('spareparts', 'spareparts.id', '=', 'goods_issue_items.sparepart_id')
            ->where('goods_issues.status', 'posted')
            ->select('spareparts.part_name', DB::raw('SUM(goods_issue_items.total_price) as total'))
            ->groupBy('spareparts.part_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // === Chart: WO by maintenance type ===
        $woByType = WorkOrder::select('maintenance_type', DB::raw('COUNT(*) as total'))
            ->groupBy('maintenance_type')
            ->get();

        // === Chart: WO status distribution ===
        $woByStatus = WorkOrder::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // === Recent Work Orders ===
        $recentWOs = WorkOrder::with('unit:id,unit_code,unit_model', 'technician:id,technician_name')
            ->latest()
            ->limit(10)
            ->get();

        // === Units under repair / needing attention ===
        $unitsAttention = Unit::with('category:id,name')
            ->whereIn('current_status', ['under_repair', 'standby'])
            ->where('is_active', true)
            ->get();

        // === Low stock spareparts ===
        $lowStockParts = Sparepart::active()->lowStock()
            ->with('category:id,name')
            ->orderByRaw('stock_on_hand - minimum_stock ASC')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalUnits', 'available', 'underRepair', 'standby',
            'openPR', 'openPO', 'openWO', 'completedWO',
            'monthlyRepairCost', 'totalRepairCostAll',
            'lowStockCount', 'totalSpareparts',
            'costPerUnit', 'availTrend', 'costBreakdown', 'monthlyCostTrend',
            'topSpareparts', 'woByType', 'woByStatus',
            'recentWOs', 'unitsAttention', 'lowStockParts'
        ));
    }
}
