<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Sparepart;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsIssue;
use App\Models\WorkOrder;
use App\Models\RepairCostSummary;
use App\Models\UnitAvailabilityLog;
use App\Models\UnitMonthlyCost;
use App\Models\UnitTire;
use App\Models\P2hCheck;
use App\Models\ComplaintType;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ══════════════════════════════════════════
        // SHARED KPI
        // ══════════════════════════════════════════
        $totalUnits  = Unit::active()->count();
        $available   = Unit::active()->status('available')->count();
        $underRepair = Unit::active()->status('under_repair')->count();
        $standby     = Unit::active()->status('standby')->count();

        $openPR      = PurchaseRequest::whereIn('status', ['draft', 'submitted'])->count();
        $openPO      = PurchaseOrder::whereIn('status', ['draft', 'issued', 'partial'])->count();
        $openWO      = WorkOrder::whereIn('status', ['open', 'in_progress', 'waiting_part'])->count();
        $completedWO = WorkOrder::where('status', 'completed')->count();

        $lowStockCount   = Sparepart::active()->lowStock()->count();
        $totalSpareparts = Sparepart::active()->count();
        $totalRepairCostAll = RepairCostSummary::sum('total_cost');

        $unitsAttention = Unit::with('category:id,name')
            ->whereIn('current_status', ['under_repair', 'standby'])
            ->where('is_active', true)
            ->get();

        $lowStockParts = Sparepart::active()->lowStock()
            ->orderByRaw('stock_on_hand - minimum_stock ASC')
            ->limit(10)
            ->get();

        $woByType = WorkOrder::select('maintenance_type', DB::raw('COUNT(*) as total'))
            ->groupBy('maintenance_type')
            ->get();

        // ══════════════════════════════════════════
        // DAILY
        // ══════════════════════════════════════════
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();

        $daily = [
            'wo_open'        => WorkOrder::whereIn('status', ['open', 'in_progress', 'waiting_part'])
                                    ->whereDate('created_at', today())->count(),
            'wo_completed'   => WorkOrder::where('status', 'completed')
                                    ->whereDate('updated_at', today())->count(),
            'p2h_total'      => P2hCheck::whereDate('created_at', today())->count(),
            'p2h_pass'       => P2hCheck::whereDate('created_at', today())->whereHas('items', fn($q) => $q->where('condition', 'good'))->count(),
            'p2h_fail'       => P2hCheck::whereDate('created_at', today())->whereHas('items', fn($q) => $q->where('condition', 'bad'))->count(),
            'goods_issue'    => GoodsIssue::whereDate('issue_date', today())->where('status', 'posted')->count(),
            'downtime_hours' => round(
                                    UnitAvailabilityLog::whereDate('date', today())
                                        ->sum(DB::raw('24 - (availability_percent / 100 * 24)')),
                                    1
                                ),
            'repair_cost'    => RepairCostSummary::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_cost'),
            'wo_hourly'      => $this->woHourly($todayStart, $todayEnd),
            'cost_breakdown' => $this->costBreakdown($todayStart, $todayEnd),
            'recent_wo'      => WorkOrder::with('unit:id,unit_code,unit_model', 'technician:id,technician_name')
                                    ->whereDate('created_at', today())
                                    ->latest()->limit(10)->get(),
        ];

        // ══════════════════════════════════════════
        // WEEKLY
        // ══════════════════════════════════════════
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $weekly = [
            'wo_total'       => WorkOrder::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'wo_completed'   => WorkOrder::where('status', 'completed')
                                    ->whereBetween('updated_at', [$weekStart, $weekEnd])->count(),
            'repair_cost'    => RepairCostSummary::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_cost'),
            'goods_issue'    => GoodsIssue::whereBetween('issue_date', [$weekStart, $weekEnd])
                                    ->where('status', 'posted')->count(),
            'avail_trend'    => $this->availTrend($weekStart, $weekEnd, 'daily'),
            'wo_daily'       => $this->woDailyBreakdown($weekStart, $weekEnd),
            'cost_breakdown' => $this->costBreakdown($weekStart, $weekEnd),
            'top_spareparts' => $this->topSpareparts($weekStart, $weekEnd, 6),
            'recent_wo'      => WorkOrder::with('unit:id,unit_code,unit_model', 'technician:id,technician_name')
                                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                                    ->latest()->limit(10)->get(),
        ];

        // ══════════════════════════════════════════
        // MONTHLY
        // ══════════════════════════════════════════
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $monthly = [
            'repair_cost'        => RepairCostSummary::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_cost'),
            'procurement_cost'   => PurchaseOrderItem::join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_id')
                                        ->where('purchase_orders.status', 'completed')
                                        ->whereBetween('purchase_orders.updated_at', [$monthStart, $monthEnd])->sum('total_price'),
            'wo_total'           => WorkOrder::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'wo_completed'       => WorkOrder::where('status', 'completed')
                                        ->whereBetween('updated_at', [$monthStart, $monthEnd])->count(),
            'cost_breakdown'     => $this->costBreakdown($monthStart, $monthEnd),
            'cost_breakdown_all' => [
                'sparepart'  => RepairCostSummary::sum('sparepart_cost'),
                'labor'      => RepairCostSummary::sum('labor_cost'),
                'vendor'     => RepairCostSummary::sum('vendor_cost'),
                'consumable' => RepairCostSummary::sum('consumable_cost'),
            ],
            'avail_trend'        => $this->availTrend($monthStart, $monthEnd, 'weekly'),
            'monthly_cost_trend' => $this->monthlyCostTrend(12),
            'cost_per_unit'      => RepairCostSummary::select('unit_id', DB::raw('SUM(total_cost) as total'))
                                        ->groupBy('unit_id')->orderByDesc('total')->limit(10)
                                        ->with('unit:id,unit_code')->get(),
            'top_spareparts'     => $this->topSpareparts($monthStart, $monthEnd, 8),
            'top_complaints'     => $this->topComplaints($monthStart, $monthEnd, 6),
            'recent_wo'          => WorkOrder::with('unit:id,unit_code,unit_model', 'technician:id,technician_name')
                                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                                        ->latest()->limit(10)->get(),
        ];

        $budgetAlerts = $this->budgetAlerts();

        return view('dashboard.index', compact(
            'totalUnits', 'available', 'underRepair', 'standby',
            'openPR', 'openPO', 'openWO', 'completedWO',
            'lowStockCount', 'totalSpareparts', 'totalRepairCostAll',
            'unitsAttention', 'lowStockParts', 'woByType',
            'daily', 'weekly', 'monthly', 'budgetAlerts'
        ));
    }

    // ── Helpers ──────────────────────────────

    private function woHourly($start, $end): array
    {
        $rows = WorkOrder::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('hour')
            ->pluck('total', 'hour');

        return collect(range(0, 23))->map(fn($h) => $rows[$h] ?? 0)->toArray();
    }

    private function availTrend($start, $end, string $groupBy = 'daily'): array
    {
        if ($groupBy === 'weekly') {
            $rows = UnitAvailabilityLog::select(
                    DB::raw('YEARWEEK(date, 1) as period'),
                    DB::raw('MIN(date) as week_start'),
                    DB::raw('AVG(availability_percent) as avg_avail')
                )
                ->whereBetween('date', [$start, $end])
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            return [
                'labels' => $rows->map(fn($r) => Carbon::parse($r->week_start)->format('d M'))->toArray(),
                'values' => $rows->map(fn($r) => round($r->avg_avail, 1))->toArray(),
            ];
        }

        $rows = UnitAvailabilityLog::select('date', DB::raw('AVG(availability_percent) as avg_avail'))
            ->whereBetween('date', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $rows->map(fn($r) => Carbon::parse($r->date)->format('d M'))->toArray(),
            'values' => $rows->map(fn($r) => round($r->avg_avail, 1))->toArray(),
        ];
    }

    private function woDailyBreakdown($start, $end): array
    {
        $open = WorkOrder::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereIn('status', ['open', 'in_progress', 'waiting_part'])
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('day')->pluck('total', 'day');

        $closed = WorkOrder::selectRaw('DATE(updated_at) as day, COUNT(*) as total')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$start, $end])
            ->groupBy('day')->pluck('total', 'day');

        $days = collect(Carbon::parse($start)->daysUntil($end));

        return [
            'labels' => $days->map(fn($d) => $d->format('D d/m'))->toArray(),
            'open'   => $days->map(fn($d) => $open[$d->toDateString()] ?? 0)->toArray(),
            'closed' => $days->map(fn($d) => $closed[$d->toDateString()] ?? 0)->toArray(),
        ];
    }

    private function costBreakdown($start, $end): array
    {
        return [
            'sparepart'  => (float) RepairCostSummary::whereBetween('created_at', [$start, $end])->sum('sparepart_cost'),
            'labor'      => (float) RepairCostSummary::whereBetween('created_at', [$start, $end])->sum('labor_cost'),
            'vendor'     => (float) RepairCostSummary::whereBetween('created_at', [$start, $end])->sum('vendor_cost'),
            'consumable' => (float) RepairCostSummary::whereBetween('created_at', [$start, $end])->sum('consumable_cost'),
        ];
    }

    private function topSpareparts($start, $end, int $limit = 8)
    {
        return DB::table('goods_issue_items')
            ->join('goods_issues', 'goods_issues.id', '=', 'goods_issue_items.goods_issue_id')
            ->join('spareparts', 'spareparts.id', '=', 'goods_issue_items.sparepart_id')
            ->where('goods_issues.status', 'posted')
            ->whereBetween('goods_issues.issue_date', [$start, $end])
            ->select('spareparts.part_name', DB::raw('SUM(goods_issue_items.total_price) as total'))
            ->groupBy('spareparts.part_name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    private function monthlyCostTrend(int $months = 12)
    {
        return RepairCostSummary::select(
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as sort_key"),
                DB::raw('SUM(total_cost) as total'),
                DB::raw('SUM(sparepart_cost) as sparepart'),
                DB::raw('SUM(labor_cost) as labor'),
                DB::raw('SUM(vendor_cost) as vendor')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths($months)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();
    }

    private function topComplaints($start, $end, int $limit = 6)
    {
        return WorkOrder::select('complaint_type_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(downtime_hours) as total_downtime'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('complaint_type_id')
            ->groupBy('complaint_type_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('complaintType:id,name,color')
            ->get();
    }

    private function budgetAlerts(): array
    {
        $yearMonth = now()->format('Y-m');

        // ── 1. Unit Cost Budget ───────────────────────────────────────────────
        $costMonthly = UnitMonthlyCost::where('year_month', $yearMonth)
            ->with('unit:id,unit_code,unit_model,monthly_budget_limit')
            ->get()
            ->filter(fn($m) => ($m->unit->monthly_budget_limit ?? 0) > 0)
            ->map(function ($m) {
                $limit = (float) $m->unit->monthly_budget_limit;
                $used  = (float) $m->total_cost;
                $pct   = $limit > 0 ? min(100, round(($used / $limit) * 100, 1)) : 0;
                return [
                    'type'      => 'cost',
                    'label'     => 'Budget Biaya',
                    'unit_code' => $m->unit->unit_code,
                    'unit_model'=> $m->unit->unit_model,
                    'unit_id'   => $m->unit_id,
                    'used'      => $used,
                    'limit'     => $limit,
                    'pct'       => $pct,
                    'link'      => route('units.show', $m->unit_id),
                ];
            });

        // ── 2. Unit KM Budget ─────────────────────────────────────────────────
        $kmMonthly = UnitMonthlyCost::where('year_month', $yearMonth)
            ->with('unit:id,unit_code,unit_model,monthly_km_budget')
            ->get()
            ->filter(fn($m) => ($m->unit->monthly_km_budget ?? 0) > 0)
            ->map(function ($m) {
                $limit = (float) $m->unit->monthly_km_budget;
                $used  = (float) $m->total_km;
                $pct   = $limit > 0 ? min(100, round(($used / $limit) * 100, 1)) : 0;
                return [
                    'type'      => 'km',
                    'label'     => 'Budget KM',
                    'unit_code' => $m->unit->unit_code,
                    'unit_model'=> $m->unit->unit_model,
                    'unit_id'   => $m->unit_id,
                    'used'      => $used,
                    'limit'     => $limit,
                    'pct'       => $pct,
                    'link'      => route('units.show', $m->unit_id),
                ];
            });

        // ── 3. Tire KM ────────────────────────────────────────────────────────
        $tireAlerts = UnitTire::whereNotNull('unit_id')
            ->where('km_limit', '>', 0)
            ->with('unit:id,unit_code', 'sparepart:id,part_name')
            ->get()
            ->map(function ($t) {
                $pct = $t->km_limit > 0
                    ? min(100, round(($t->total_km / $t->km_limit) * 100, 1))
                    : 0;
                return [
                    'type'       => 'tire',
                    'label'      => 'Limit Ban',
                    'unit_code'  => $t->unit->unit_code ?? '-',
                    'unit_model' => ($t->sparepart->part_name ?? 'Ban') . ' [' . ($t->position_label ?? $t->position_number) . ']',
                    'unit_id'    => $t->unit_id,
                    'used'       => (float) $t->total_km,
                    'limit'      => (float) $t->km_limit,
                    'pct'        => $pct,
                    'link'       => route('tires.show', $t->id),
                ];
            });

        // ── Merge, assign severity, sort ─────────────────────────────────────
        $all = $costMonthly->concat($kmMonthly)->concat($tireAlerts)
            ->map(function ($item) {
                $p = $item['pct'];
                $item['severity'] = $p >= 100 ? 4 : ($p >= 80 ? 3 : ($p >= 50 ? 2 : 1));
                $item['color']    = $p >= 100 ? 'danger' : ($p >= 80 ? 'orange' : ($p >= 50 ? 'warning' : 'success'));
                return $item;
            })
            ->sortByDesc('severity')
            ->values();

        return [
            'items'    => $all,
            'red'      => $all->where('severity', 4)->count(),
            'orange'   => $all->where('severity', 3)->count(),
            'yellow'   => $all->where('severity', 2)->count(),
            'green'    => $all->where('severity', 1)->count(),
        ];
    }
}
