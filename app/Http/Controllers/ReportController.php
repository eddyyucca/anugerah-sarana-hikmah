<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\RepairCostSummary;
use App\Models\UnitAvailabilityLog;
use App\Models\StockMovement;
use App\Models\Sparepart;
use App\Models\WorkOrder;
use App\Models\ComplaintType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function availability(Request $request)
    {
        $query = Unit::active()->with('category:id,name');

        if ($request->filled('unit_id')) {
            $query->where('id', $request->unit_id);
        }

        $units = $query->get()->map(function ($unit) use ($request) {
            $logQuery = UnitAvailabilityLog::where('unit_id', $unit->id);
            if ($request->filled('date_from')) {
                $logQuery->where('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $logQuery->where('date', '<=', $request->date_to);
            }
            $unit->avg_availability = round($logQuery->avg('availability_percent') ?? 100, 2);
            $unit->total_downtime = round($logQuery->sum('downtime_hours'), 2);
            return $unit;
        });

        $allUnits = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code']);

        return view('reports.availability', compact('units', 'allUnits'));
    }

    public function repairCost(Request $request)
    {
        $query = RepairCostSummary::with('unit:id,unit_code,unit_model', 'workOrder:id,wo_number');

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $costs = $query->latest()->paginate(25)->withQueryString();

        $summary = [
            'sparepart' => $costs->sum('sparepart_cost'),
            'labor' => $costs->sum('labor_cost'),
            'vendor' => $costs->sum('vendor_cost'),
            'consumable' => $costs->sum('consumable_cost'),
            'total' => $costs->sum('total_cost'),
        ];

        $allUnits = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code']);

        // Complaint Analysis
        $summaryQuery = WorkOrder::select('complaint_type_id',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
            DB::raw('SUM(downtime_hours) as total_downtime'),
            DB::raw('SUM(labor_cost + vendor_cost + consumable_cost) as total_cost')
        )->whereNotNull('complaint_type_id')->with('complaintType:id,name,color');

        if ($request->filled('date_from')) {
            $summaryQuery->where('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $summaryQuery->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $complaintSummary = $summaryQuery->groupBy('complaint_type_id')->orderByDesc('total_count')->get();
        $allComplaintTypes = ComplaintType::active()->get(['id', 'name', 'color']);

        return view('reports.repair-cost', compact('costs', 'summary', 'allUnits', 'complaintSummary', 'allComplaintTypes'));
    }

    public function stockMovement(Request $request)
    {
        $query = StockMovement::with('sparepart:id,part_number,part_name', 'warehouseLocation:id,name');

        if ($request->filled('sparepart_id')) {
            $query->where('sparepart_id', $request->sparepart_id);
        }
        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        $movements = $query->latest('created_at')->paginate(25)->withQueryString();
        $spareparts = Sparepart::active()->orderBy('part_name')->get(['id', 'part_number', 'part_name']);

        return view('reports.stock-movement', compact('movements', 'spareparts'));
    }

    public function complaintAnalysis(Request $request)
    {
        $query = WorkOrder::with('unit:id,unit_code,unit_model', 'complaintType:id,name,color', 'creator:id,name');

        // Filter by complaint type
        if ($request->filled('complaint_type_id')) {
            $query->where('complaint_type_id', $request->complaint_type_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter by maintenance type
        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        $workOrders = $query->latest('start_time')->paginate(20)->withQueryString();

        // Summary by complaint type
        $summaryQuery = WorkOrder::select('complaint_type_id',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
            DB::raw('SUM(downtime_hours) as total_downtime'),
            DB::raw('SUM(labor_cost + vendor_cost + consumable_cost) as total_cost')
        )->whereNotNull('complaint_type_id')->with('complaintType:id,name,color');

        if ($request->filled('date_from')) {
            $summaryQuery->where('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $summaryQuery->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $summary = $summaryQuery->groupBy('complaint_type_id')->orderByDesc('total_count')->get();

        $allComplaintTypes = ComplaintType::active()->get(['id', 'name', 'color']);

        return view('reports.complaint-analysis', compact('workOrders', 'summary', 'allComplaintTypes'));
    }
}
