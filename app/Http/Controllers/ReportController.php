<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\RepairCostSummary;
use App\Models\UnitAvailabilityLog;
use App\Models\StockMovement;
use App\Models\Sparepart;
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

        return view('reports.repair-cost', compact('costs', 'summary', 'allUnits'));
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
}
