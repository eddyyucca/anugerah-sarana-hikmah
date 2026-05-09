<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\Unit;
use App\Models\Technician;
use App\Models\Operator;
use App\Services\ApprovalService;
use App\Services\DocumentNumberService;
use App\Services\RepairCostService;
use App\Services\UnitBudgetService;
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
        $woNumber    = DocumentNumberService::generateWO();
        $units       = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model', 'monthly_budget_limit']);
        $technicians = Technician::active()->orderBy('technician_name')->get(['id', 'technician_code', 'technician_name']);
        $operators   = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);

        // Sertakan status budget setiap unit (untuk peringatan di form)
        $unitBudgetStatus = [];
        foreach ($units as $u) {
            if ($u->monthly_budget_limit) {
                $status = UnitBudgetService::getStatus($u);
                $unitBudgetStatus[$u->id] = $status;
            }
        }

        return view('work-orders.create', compact('woNumber', 'units', 'technicians', 'operators', 'unitBudgetStatus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id'          => 'required|exists:units,id',
            'operator_id'      => 'nullable|exists:operators,id',
            'repair_location'  => 'required|in:di_workshop,di_luar_workshop',
            'maintenance_type' => 'required|in:corrective,preventive,predictive',
            'technician_id'    => 'nullable|exists:technicians,id',
            'start_time'       => 'required|date',
            'labor_cost'       => 'nullable|numeric|min:0',
            'vendor_cost'      => 'nullable|numeric|min:0',
            'consumable_cost'  => 'nullable|numeric|min:0',
            'remarks'          => 'nullable|string',
        ]);

        $unit          = Unit::findOrFail($request->unit_id);
        $isOverBudget  = UnitBudgetService::isOverBudget($unit);
        $status        = $isOverBudget ? 'pending_approval' : 'open';

        DB::transaction(function () use ($request, $unit, $isOverBudget, $status) {
            $wo = WorkOrder::create([
                'wo_number'        => DocumentNumberService::generateWO(),
                'unit_id'          => $unit->id,
                'operator_id'      => $request->operator_id,
                'repair_location'  => $request->repair_location,
                'maintenance_type' => $request->maintenance_type,
                'technician_id'    => $request->technician_id,
                'status'           => $status,
                'start_time'       => $request->start_time,
                'labor_cost'       => $request->labor_cost ?? 0,
                'vendor_cost'      => $request->vendor_cost ?? 0,
                'consumable_cost'  => $request->consumable_cost ?? 0,
                'remarks'          => $request->remarks,
                'created_by'       => auth()->id() ?? 1,
            ]);

            // Unit status hanya diubah jika WO langsung aktif
            if ($status === 'open') {
                $unit->update(['current_status' => 'under_repair']);
            }

            $logDesc = 'Work Order dibuat.';
            if ($isOverBudget) {
                $logDesc .= ' Menunggu persetujuan — unit telah melampaui budget perbaikan bulanan.';
            }

            WorkOrderLog::create([
                'work_order_id' => $wo->id,
                'activity_time' => now(),
                'activity_type' => 'created',
                'description'   => $logDesc,
                'created_by'    => auth()->id() ?? 1,
            ]);

            if ($isOverBudget) {
                $wo->load('unit');
                UnitBudgetService::initiateOverBudgetApproval($wo);
            }
        });

        $flashType = $isOverBudget ? 'warning' : 'success';
        $flashMsg  = $isOverBudget
            ? 'Work Order dibuat namun membutuhkan persetujuan — unit telah melampaui budget perbaikan bulanan bulan ini.'
            : 'Work Order berhasil dibuat.';

        return redirect()->route('work-orders.index')->with($flashType, $flashMsg);
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load('unit', 'operator', 'technician', 'logs.creator', 'goodsIssues.items.sparepart', 'costSummary');

        $budgetStatus    = $workOrder->unit ? UnitBudgetService::getStatus($workOrder->unit) : ['has_limit' => false];
        $approvalLogs    = ApprovalService::getStatus('wo', $workOrder->id);
        $canApprove      = false;
        $canApproveCheck = [];

        if ($workOrder->status === 'pending_approval' && auth()->check()) {
            $canApproveCheck = ApprovalService::canApprove(auth()->user(), 'wo', $workOrder->id);
            $canApprove      = $canApproveCheck['can_approve'] ?? false;
        }

        return view('work-orders.show', compact('workOrder', 'budgetStatus', 'approvalLogs', 'canApprove', 'canApproveCheck'));
    }

    public function edit(WorkOrder $workOrder)
    {
        if (in_array($workOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'WO yang selesai/dibatalkan tidak dapat diedit.');
        }

        $units       = Unit::active()->orderBy('unit_code')->get(['id', 'unit_code', 'unit_model']);
        $technicians = Technician::active()->orderBy('technician_name')->get(['id', 'technician_code', 'technician_name']);
        $operators   = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);

        return view('work-orders.edit', compact('workOrder', 'units', 'technicians', 'operators'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        if (in_array($workOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'WO yang selesai/dibatalkan tidak dapat diedit.');
        }

        $request->validate([
            'repair_location'  => 'required|in:di_workshop,di_luar_workshop',
            'maintenance_type' => 'required|in:corrective,preventive,predictive',
            'technician_id'    => 'nullable|exists:technicians,id',
            'operator_id'      => 'nullable|exists:operators,id',
            'action_taken'     => 'nullable|string',
            'labor_cost'       => 'nullable|numeric|min:0',
            'vendor_cost'      => 'nullable|numeric|min:0',
            'consumable_cost'  => 'nullable|numeric|min:0',
            'remarks'          => 'nullable|string',
        ]);

        $workOrder->update($request->only([
            'repair_location', 'maintenance_type', 'technician_id', 'operator_id',
            'action_taken', 'labor_cost', 'vendor_cost', 'consumable_cost', 'remarks',
        ]));

        RepairCostService::recalculate($workOrder);

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'WO berhasil diperbarui.');
    }

    public function progress(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'open') {
            return back()->with('error', 'Hanya WO berstatus "open" yang dapat dimulai.');
        }

        $workOrder->update(['status' => 'in_progress']);

        WorkOrderLog::create([
            'work_order_id' => $workOrder->id,
            'activity_time' => now(),
            'activity_type' => 'in_progress',
            'description'   => 'Pekerjaan dimulai.',
            'created_by'    => auth()->id() ?? 1,
        ]);

        return back()->with('success', 'WO berubah ke In Progress.');
    }

    public function complete(Request $request, WorkOrder $workOrder)
    {
        if (!in_array($workOrder->status, ['in_progress', 'waiting_part'])) {
            return back()->with('error', 'WO tidak dapat diselesaikan dari status saat ini.');
        }

        DB::transaction(function () use ($workOrder) {
            $endTime  = now();
            $downtime = 0;
            if ($workOrder->start_time) {
                $downtime = $workOrder->start_time->diffInMinutes($endTime) / 60;
            }

            $workOrder->update([
                'status'         => 'completed',
                'end_time'       => $endTime,
                'downtime_hours' => round($downtime, 2),
            ]);

            Unit::where('id', $workOrder->unit_id)->update(['current_status' => 'available']);

            RepairCostService::recalculate($workOrder);

            // Update biaya bulanan unit & cek pelampauan budget
            $unit = $workOrder->unit()->first();
            if ($unit && $unit->monthly_budget_limit) {
                $wasOverBudget = UnitBudgetService::isOverBudget($unit);
                $monthly       = UnitBudgetService::recalculate($unit);

                if (!$wasOverBudget && $monthly->is_over_budget) {
                    UnitBudgetService::recordExceedance($workOrder, $monthly, $unit);
                }
            }

            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'activity_time' => now(),
                'activity_type' => 'completed',
                'description'   => 'Work Order selesai. Downtime: ' . round($downtime, 2) . ' jam.',
                'created_by'    => auth()->id() ?? 1,
            ]);
        });

        return back()->with('success', 'Work Order berhasil diselesaikan.');
    }

    /**
     * Setujui WO yang menunggu persetujuan (over budget)
     */
    public function approve(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'pending_approval') {
            return back()->with('error', 'WO tidak sedang menunggu persetujuan.');
        }

        $authCheck = ApprovalService::canApprove(auth()->user(), 'wo', $workOrder->id);
        if (!$authCheck['can_approve']) {
            return back()->with('error', $authCheck['message']);
        }

        DB::transaction(function () use ($workOrder, $request) {
            $result = ApprovalService::approve('wo', $workOrder->id, auth()->id(), $request->remarks);

            if ($result === 'fully_approved') {
                $workOrder->update(['status' => 'open']);
                $workOrder->unit()->update(['current_status' => 'under_repair']);

                WorkOrderLog::create([
                    'work_order_id' => $workOrder->id,
                    'activity_time' => now(),
                    'activity_type' => 'approved',
                    'description'   => 'WO disetujui oleh ' . auth()->user()->name . '. Status berubah ke Open.',
                    'created_by'    => auth()->id(),
                ]);
            } else {
                WorkOrderLog::create([
                    'work_order_id' => $workOrder->id,
                    'activity_time' => now(),
                    'activity_type' => 'approved',
                    'description'   => 'Level persetujuan disetuji oleh ' . auth()->user()->name . '. Menunggu level berikutnya.',
                    'created_by'    => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'Work Order berhasil disetujui.');
    }

    /**
     * Tolak WO yang menunggu persetujuan (over budget)
     */
    public function reject(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'pending_approval') {
            return back()->with('error', 'WO tidak sedang menunggu persetujuan.');
        }

        $authCheck = ApprovalService::canApprove(auth()->user(), 'wo', $workOrder->id);
        if (!$authCheck['can_approve']) {
            return back()->with('error', $authCheck['message']);
        }

        DB::transaction(function () use ($workOrder, $request) {
            ApprovalService::reject('wo', $workOrder->id, auth()->id(), $request->remarks);
            $workOrder->update(['status' => 'cancelled']);

            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'activity_time' => now(),
                'activity_type' => 'rejected',
                'description'   => 'WO ditolak dan dibatalkan oleh ' . auth()->user()->name .
                                   ($request->remarks ? '. Alasan: ' . $request->remarks : ''),
                'created_by'    => auth()->id(),
            ]);
        });

        return back()->with('success', 'Work Order ditolak.');
    }
}
