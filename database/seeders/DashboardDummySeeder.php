<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardDummySeeder extends Seeder
{
    // Unit IDs aktif dari database
    private array $unitIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    // Technician IDs
    private array $technicianIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    // Sparepart ID => unit_price
    private array $spareparts = [
        1  => 350000,
        2  => 280000,
        3  => 850000,
        4  => 650000,
        5  => 450000,
        6  => 720000,
        7  => 85000000,
        8  => 1200000,
        9  => 950000,
        10 => 4500000,
        11 => 3800000,
        12 => 650000,
        13 => 45000000,
        14 => 8500000,
        15 => 12000000,
        16 => 15000000,
        17 => 2500000,
        18 => 3500000,
        19 => 18000000,
        20 => 5500000,
        21 => 3500000,
        22 => 12000000,
        23 => 15000000,
        24 => 8500000,
        25 => 12000000,
    ];

    // Complaint type IDs
    private array $complaintTypeIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

    private int $woCounter = 0;
    private int $giCounter = 0;

    public function run(): void
    {
        $this->command->info('Membuat data dummy dashboard 2026...');

        // Hapus data lama di tabel terkait (opsional, hati-hati di production)
        DB::table('goods_issue_items')->delete();
        DB::table('goods_issues')->delete();
        DB::table('repair_cost_summaries')->delete();
        DB::table('work_order_logs')->delete();
        DB::table('work_orders')->delete();
        DB::table('unit_availability_logs')->delete();

        $this->command->info('Data lama dihapus, membuat data baru...');

        // Generate dari Jan 2026 sampai hari ini
        $startDate = Carbon::create(2026, 1, 1);
        $today     = Carbon::today();

        $current = $startDate->copy();
        while ($current->lte($today)) {
            $this->generateDailyData($current->copy());
            $current->addDay();
        }

        $this->command->info('Selesai!');
        $this->command->info('Work Orders : ' . DB::table('work_orders')->count());
        $this->command->info('Availability: ' . DB::table('unit_availability_logs')->count());
        $this->command->info('Repair Cost : ' . DB::table('repair_cost_summaries')->count());
        $this->command->info('Goods Issues: ' . DB::table('goods_issues')->count());
    }

    private function generateDailyData(Carbon $date): void
    {
        // Buat availability log untuk semua unit
        foreach ($this->unitIds as $unitId) {
            $this->createAvailabilityLog($unitId, $date);
        }

        // Buat 2-6 work order per hari
        $woCount = rand(2, 6);
        for ($i = 0; $i < $woCount; $i++) {
            $this->createWorkOrder($date);
        }
    }

    private function createAvailabilityLog(int $unitId, Carbon $date): void
    {
        // Availability 80-100% dengan variasi
        $availPct      = round(80 + mt_rand(0, 200) / 10, 2); // 80.0 - 100.0
        $scheduled     = 24;
        $downtime      = round($scheduled * (1 - $availPct / 100), 2);
        $availableHours = round($scheduled - $downtime, 2);

        // Hindari duplikat
        $exists = DB::table('unit_availability_logs')
            ->where('unit_id', $unitId)
            ->where('date', $date->toDateString())
            ->whereNull('reference_type')
            ->exists();

        if (!$exists) {
            DB::table('unit_availability_logs')->insert([
                'unit_id'              => $unitId,
                'date'                 => $date->toDateString(),
                'scheduled_hours'      => $scheduled,
                'downtime_hours'       => $downtime,
                'available_hours'      => $availableHours,
                'availability_percent' => $availPct,
                'reference_type'       => null,
                'reference_id'         => null,
            ]);
        }
    }

    private function createWorkOrder(Carbon $date): void
    {
        $this->woCounter++;
        $woNumber   = 'WO-' . $date->format('Ym') . '-' . str_pad($this->woCounter, 4, '0', STR_PAD_LEFT);
        $unitId     = $this->unitIds[array_rand($this->unitIds)];
        $techId     = $this->technicianIds[array_rand($this->technicianIds)];
        $ctId       = $this->complaintTypeIds[array_rand($this->complaintTypeIds)];

        $types      = ['corrective', 'preventive', 'predictive'];
        $typeWeights = [60, 30, 10]; // 60% corrective, 30% preventive, 10% predictive
        $type       = $this->weightedRandom($types, $typeWeights);

        $statuses   = ['open', 'in_progress', 'waiting_part', 'completed', 'cancelled'];
        // Semakin lama hari tsb, semakin besar kemungkinan completed
        $cutoff7 = Carbon::today()->subDays(7)->startOfDay();
        $cutoff1 = Carbon::today()->subDays(1)->startOfDay();
        if ($date->lt($cutoff7)) {
            $statusWeights = [5, 5, 5, 80, 5];   // 80% completed
        } elseif ($date->lt($cutoff1)) {
            $statusWeights = [15, 20, 10, 50, 5]; // 50% completed
        } else {
            $statusWeights = [30, 35, 20, 10, 5]; // today: 10% completed
        }
        $status     = $this->weightedRandom($statuses, $statusWeights);

        $startHour  = rand(7, 16);
        $startTime  = $date->copy()->setTime($startHour, rand(0, 59));
        $endTime    = $status === 'completed'
            ? $startTime->copy()->addHours(rand(2, 10))->addMinutes(rand(0, 59))
            : null;
        $downtime   = $endTime ? round($startTime->diffInMinutes($endTime) / 60, 2) : round(rand(1, 8) + rand(0, 9) / 10, 2);

        $laborCost      = rand(0, 1) ? rand(500000, 5000000) : 0;
        $vendorCost     = rand(0, 4) === 0 ? rand(1000000, 15000000) : 0;
        $consumableCost = rand(100000, 800000);

        $complaints = [
            'Mesin tidak mau hidup',
            'Hydraulic bocor di hose',
            'Suara abnormal di transmission',
            'Brake tidak pakem',
            'Overheating coolant',
            'Hydraulic pump lemah',
            'Track terlepas',
            'Bucket cylinder seal bocor',
            'Alternator tidak charging',
            'Starter motor rusak',
            'AC cabin tidak dingin',
            'Sensor fuel pressure error',
            'Lampu kerja mati',
            'Clam shell tidak menutup rapat',
            'Transmisi slip di gigi 3',
        ];

        $woId = DB::table('work_orders')->insertGetId([
            'wo_number'          => $woNumber,
            'unit_id'            => $unitId,
            'complaint'          => $complaints[array_rand($complaints)],
            'complaint_type_id'  => $ctId,
            'maintenance_type'   => $type,
            'technician_id'      => $techId,
            'status'             => $status,
            'start_time'         => $startTime,
            'end_time'           => $endTime,
            'downtime_hours'     => $downtime,
            'labor_cost'         => $laborCost,
            'vendor_cost'        => $vendorCost,
            'consumable_cost'    => $consumableCost,
            'action_taken'       => $status === 'completed' ? 'Penggantian dan perbaikan komponen selesai dilakukan.' : null,
            'remarks'            => null,
            'created_by'         => 1,
            'created_at'         => $startTime,
            'updated_at'         => $endTime ?? $startTime,
        ]);

        // Buat Goods Issue & repair cost untuk WO yang completed
        if ($status === 'completed') {
            $sparepartCost = $this->createGoodsIssue($woId, $date);

            $totalCost = $sparepartCost + $laborCost + $vendorCost + $consumableCost;
            DB::table('repair_cost_summaries')->insert([
                'work_order_id'   => $woId,
                'unit_id'         => $unitId,
                'sparepart_cost'  => $sparepartCost,
                'labor_cost'      => $laborCost,
                'vendor_cost'     => $vendorCost,
                'consumable_cost' => $consumableCost,
                'total_cost'      => $totalCost,
                'created_at'      => $endTime ?? $startTime,
                'updated_at'      => $endTime ?? $startTime,
            ]);
        }
    }

    private function createGoodsIssue(int $woId, Carbon $date): float
    {
        $this->giCounter++;
        $giNumber = 'GI-' . $date->format('Ym') . '-' . str_pad($this->giCounter, 4, '0', STR_PAD_LEFT);

        $giId = DB::table('goods_issues')->insertGetId([
            'gi_number'      => $giNumber,
            'work_order_id'  => $woId,
            'issue_date'     => $date->toDateString(),
            'remarks'        => null,
            'status'         => 'posted',
            'posted_by'      => 1,
            'posted_at'      => $date->copy()->setTime(rand(8, 17), rand(0, 59)),
            'created_at'     => $date,
            'updated_at'     => $date,
        ]);

        // 1-3 item sparepart per GI
        $partIds   = array_keys($this->spareparts);
        shuffle($partIds);
        $selected  = array_slice($partIds, 0, rand(1, 3));

        $total = 0;
        foreach ($selected as $spId) {
            $price = $this->spareparts[$spId];
            $qty   = rand(1, 3);
            $lineTotal = $price * $qty;
            $total += $lineTotal;

            DB::table('goods_issue_items')->insert([
                'goods_issue_id'        => $giId,
                'sparepart_id'          => $spId,
                'warehouse_location_id' => null,
                'qty_issued'            => $qty,
                'unit_price'            => $price,
                'total_price'           => $lineTotal,
            ]);
        }

        return (float) $total;
    }

    private function weightedRandom(array $items, array $weights): mixed
    {
        $total = array_sum($weights);
        $rand  = rand(1, $total);
        $cumulative = 0;
        foreach ($items as $i => $item) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $item;
            }
        }
        return $items[array_key_last($items)];
    }
}
