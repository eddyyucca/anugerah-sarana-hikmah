<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\Unit;
use App\Models\Technician;
use App\Services\DocumentNumberService;
use App\Services\RepairCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with('unit:id,unit_code,unit_model', 'technician:id,technician_name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('wo_number', 'like', "%{$s}%")
                ->orWhereHas('unit', fn($u) => $u->where('unit_code', 'like', "%{$s}%")));
        }
        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $workOrders = $query->latest()->paginate(25)->withQueryString();
        return view('work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $woNumber = DocumentNumberService::generateWO();
        $units = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model']);
        $technicians = Technician::active()->orderBy('technician_name')->get(['id', 'technician_code', 'technician_name']);
        return view('work-orders.create', compact('woNumber', 'units', 'technicians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'complaint' => 'required|string',
            'maintenance_type' => 'required|in:corrective,preventive,predictive',
            'technician_id' => 'nullable|exists:technicians,id',
            'start_time' => 'required|date',
            'labor_cost' => 'nullable|numeric|min:0',
            'vendor_cost' => 'nullable|numeric|min:0',
            'consumable_cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $wo = WorkOrder::create([
                'wo_number' => DocumentNumberService::generateWO(),
                'unit_id' => $request->unit_id,
                'complaint' => $request->complaint,
                'maintenance_type' => $request->maintenance_type,
                'technician_id' => $request->technician_id,
                'status' => 'open',
                'start_time' => $request->start_time,
                'labor_cost' => $request->labor_cost ?? 0,
                'vendor_cost' => $request->vendor_cost ?? 0,
                'consumable_cost' => $request->consumable_cost ?? 0,
                'remarks' => $request->remarks,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Update unit status
            Unit::where('id', $request->unit_id)->update(['current_status' => 'under_repair']);

            // Log
            WorkOrderLog::create([
                'work_order_id' => $wo->id,
                'activity_time' => now(),
                'activity_type' => 'created',
                'description' => 'Work Order created.',
                'created_by' => auth()->id() ?? 1,
            ]);
        });

        return redirect()->route('work-orders.index')->with('success', 'Work Order created successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load('unit', 'technician', 'logs.creator', 'goodsIssues.items.sparepart', 'costSummary');
        return view('work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder)
    {
        if (in_array($workOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Completed/cancelled WO cannot be edited.');
        }

        $units = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model']);
        $technicians = Technician::active()->orderBy('technician_name')->get(['id', 'technician_code', 'technician_name']);
        return view('work-orders.edit', compact('workOrder', 'units', 'technicians'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        if (in_array($workOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Completed/cancelled WO cannot be edited.');
        }

        $request->validate([
            'complaint' => 'required|string',
            'maintenance_type' => 'required|in:corrective,preventive,predictive',
            'technician_id' => 'nullable|exists:technicians,id',
            'action_taken' => 'nullable|string',
            'labor_cost' => 'nullable|numeric|min:0',
            'vendor_cost' => 'nullable|numeric|min:0',
            'consumable_cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $workOrder->update($request->only([
            'complaint', 'maintenance_type', 'technician_id', 'action_taken',
            'labor_cost', 'vendor_cost', 'consumable_cost', 'remarks',
        ]));

        RepairCostService::recalculate($workOrder);

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'WO updated.');
    }

    public function progress(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'open') {
            return back()->with('error', 'Only open WO can be set to in progress.');
        }

        $workOrder->update(['status' => 'in_progress']);

        WorkOrderLog::create([
            'work_order_id' => $workOrder->id,
            'activity_time' => now(),
            'activity_type' => 'in_progress',
            'description' => 'Work started.',
            'created_by' => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'WO set to In Progress.');
    }

    public function complete(Request $request, WorkOrder $workOrder)
    {
        if (!in_array($workOrder->status, ['in_progress', 'waiting_part'])) {
            return back()->with('error', 'WO cannot be completed from current status.');
        }

        DB::transaction(function () use ($workOrder) {
            $endTime = now();
            $downtime = 0;
            if ($workOrder->start_time) {
                $downtime = $workOrder->start_time->diffInMinutes($endTime) / 60;
            }

            $workOrder->update([
                'status' => 'completed',
                'end_time' => $endTime,
                'downtime_hours' => round($downtime, 2),
            ]);

            // Update unit status
            Unit::where('id', $workOrder->unit_id)->update(['current_status' => 'available']);

            RepairCostService::recalculate($workOrder);

            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'activity_time' => now(),
                'activity_type' => 'completed',
                'description' => 'Work Order completed. Downtime: ' . round($downtime, 2) . ' hours.',
                'created_by' => auth()->id() ?? 1,
            ]);
        });

        return back()->with('success', 'Work Order completed.');
    }
}
