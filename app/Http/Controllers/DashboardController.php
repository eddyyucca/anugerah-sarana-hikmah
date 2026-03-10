<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'units_total' => 48,
            'units_available' => 41,
            'units_repair' => 7,
            'availability_percent' => 85.4,
            'open_pr' => 12,
            'open_po' => 6,
            'monthly_repair_cost' => 285750000,
            'monthly_procurement_cost' => 467300000,
        ];

        $recentActivities = [
            [
                'code' => 'WO-20260310-001',
                'title' => 'Repair hydraulic pump unit EX1201',
                'time' => '10 Mar 2026, 08:15',
                'status' => 'In Progress',
            ],
            [
                'code' => 'PR-20260310-002',
                'title' => 'Request filter kit for DT301',
                'time' => '10 Mar 2026, 09:10',
                'status' => 'Pending',
            ],
            [
                'code' => 'PO-20260310-003',
                'title' => 'Purchase order engine hose approved',
                'time' => '10 Mar 2026, 10:00',
                'status' => 'Approved',
            ],
            [
                'code' => 'GI-20260310-004',
                'title' => 'Sparepart issued for work order DT301',
                'time' => '10 Mar 2026, 11:40',
                'status' => 'Completed',
            ],
        ];

        $unitStatus = [
            ['unit' => 'EX1201', 'model' => 'Excavator 120T', 'status' => 'Available', 'availability' => '92%', 'cost' => 18500000],
            ['unit' => 'DT301', 'model' => 'Dump Truck 30T', 'status' => 'Under Repair', 'availability' => '71%', 'cost' => 45200000],
            ['unit' => 'DZ110', 'model' => 'Dozer D8', 'status' => 'Standby', 'availability' => '80%', 'cost' => 12400000],
            ['unit' => 'GR210', 'model' => 'Grader 14M', 'status' => 'Available', 'availability' => '89%', 'cost' => 9700000],
        ];

        $costBreakdown = [
            ['label' => 'Sparepart', 'value' => 165500000],
            ['label' => 'Labor', 'value' => 48750000],
            ['label' => 'Vendor', 'value' => 52000000],
            ['label' => 'Consumable', 'value' => 19500000],
        ];

        return view('dashboard', compact(
            'summary',
            'recentActivities',
            'unitStatus',
            'costBreakdown'
        ));
    }
}