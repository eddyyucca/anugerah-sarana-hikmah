<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\RepairCostSummary;

class RepairCostService
{
    public static function recalculate(WorkOrder $workOrder): RepairCostSummary
    {
        $sparepartCost = $workOrder->goodsIssues()
            ->where('status', 'posted')
            ->with('items')
            ->get()
            ->flatMap->items
            ->sum('total_price');

        return RepairCostSummary::updateOrCreate(
            ['work_order_id' => $workOrder->id],
            [
                'unit_id' => $workOrder->unit_id,
                'sparepart_cost' => $sparepartCost,
                'labor_cost' => $workOrder->labor_cost,
                'vendor_cost' => $workOrder->vendor_cost,
                'consumable_cost' => $workOrder->consumable_cost,
                'total_cost' => $sparepartCost + $workOrder->labor_cost + $workOrder->vendor_cost + $workOrder->consumable_cost,
            ]
        );
    }
}
