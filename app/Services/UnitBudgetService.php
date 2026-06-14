<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\WorkOrder;
use App\Models\UnitMonthlyCost;
use App\Models\OperatorPerformanceRecord;
use App\Models\ApprovalSetting;
use App\Models\ApprovalLog;
use Carbon\Carbon;

class UnitBudgetService
{
    public static function getYearMonth(?Carbon $date = null): string
    {
        return ($date ?? now())->format('Y-m');
    }

    /**
     * Ambil atau buat record biaya bulanan untuk unit
     */
    public static function getMonthlyRecord(int $unitId, ?string $yearMonth = null): UnitMonthlyCost
    {
        $yearMonth ??= self::getYearMonth();

        return UnitMonthlyCost::firstOrCreate(
            ['unit_id' => $unitId, 'year_month' => $yearMonth],
            ['total_cost' => 0, 'work_order_count' => 0, 'is_over_budget' => false]
        );
    }

    /**
     * Hitung ulang biaya bulanan dari WO yang selesai
     */
    public static function recalculate(Unit $unit, ?string $yearMonth = null): UnitMonthlyCost
    {
        $yearMonth ??= self::getYearMonth();
        [$year, $month] = explode('-', $yearMonth);

        $startDate = Carbon::create((int)$year, (int)$month, 1)->startOfMonth();
        $endDate   = Carbon::create((int)$year, (int)$month, 1)->endOfMonth();

        // Sum semua WO completed untuk unit ini di bulan ini
        $result = $unit->workOrders()
            ->where('status', 'completed')
            ->whereBetween('end_time', [$startDate, $endDate])
            ->with('costSummary')
            ->get()
            ->reduce(function ($carry, $wo) {
                return [
                    'total_cost' => $carry['total_cost'] + ($wo->costSummary?->total_cost ?? 0),
                    'count' => $carry['count'] + 1,
                ];
            }, ['total_cost' => 0.0, 'count' => 0]);

        $monthly = self::getMonthlyRecord($unit->id, $yearMonth);
        $wasOverBudget = $monthly->is_over_budget;

        $newTotal    = $result['total_cost'];
        $budgetLimit = (float)$unit->monthly_budget_limit;
        $isOver      = $budgetLimit > 0 && $newTotal > $budgetLimit;

        $monthly->update([
            'total_cost'       => $newTotal,
            'work_order_count' => $result['count'],
            'is_over_budget'   => $isOver,
            'exceeded_at'      => (!$wasOverBudget && $isOver) ? now() : $monthly->exceeded_at,
        ]);

        return $monthly->fresh();
    }

    /**
     * Apakah unit sudah melebihi budget bulan ini?
     */
    public static function isOverBudget(Unit $unit): bool
    {
        if (!$unit->monthly_budget_limit) return false;

        $monthly = self::getMonthlyRecord($unit->id);
        return (bool)$monthly->is_over_budget;
    }

    /**
     * Ambil status budget untuk ditampilkan di view
     */
    public static function getStatus(Unit $unit): array
    {
        $limit = (float)$unit->monthly_budget_limit;

        if (!$limit) {
            return ['has_limit' => false];
        }

        $monthly   = self::getMonthlyRecord($unit->id);
        $used      = (float)$monthly->total_cost;
        $remaining = max(0.0, $limit - $used);
        $pct       = $limit > 0 ? min(100, (int)round(($used / $limit) * 100)) : 0;

        return [
            'has_limit'      => true,
            'limit'          => $limit,
            'used'           => $used,
            'remaining'      => $remaining,
            'percentage'     => $pct,
            'is_over_budget' => (bool)$monthly->is_over_budget,
            'exceeded_at'    => $monthly->exceeded_at,
            'year_month'     => self::getYearMonth(),
            'wo_count'       => $monthly->work_order_count,
        ];
    }

    /**
     * Tambahkan km ke record bulanan dan cek budget km
     */
    public static function addKm(Unit $unit, float $deltaKm, ?string $yearMonth = null): void
    {
        if ($deltaKm <= 0) return;

        $yearMonth ??= self::getYearMonth();
        $monthly = self::getMonthlyRecord($unit->id, $yearMonth);
        $kmLimit = (float)$unit->monthly_km_budget;

        $newTotalKm    = (float)$monthly->total_km + $deltaKm;
        $wasOver       = $monthly->is_over_km_budget;
        $isNowOver     = $kmLimit > 0 && $newTotalKm > $kmLimit;

        $monthly->update([
            'total_km'          => $newTotalKm,
            'is_over_km_budget' => $isNowOver,
            'km_exceeded_at'    => (!$wasOver && $isNowOver) ? now() : $monthly->km_exceeded_at,
        ]);
    }

    /**
     * Status budget km untuk ditampilkan di view
     */
    public static function getKmStatus(Unit $unit): array
    {
        $limit = (float)$unit->monthly_km_budget;

        if (!$limit) {
            return ['has_limit' => false];
        }

        $monthly   = self::getMonthlyRecord($unit->id);
        $used      = (float)$monthly->total_km;
        $remaining = max(0.0, $limit - $used);
        $pct       = $limit > 0 ? min(100, (int)round(($used / $limit) * 100)) : 0;

        return [
            'has_limit'         => true,
            'limit'             => $limit,
            'used'              => $used,
            'remaining'         => $remaining,
            'percentage'        => $pct,
            'is_over_km_budget' => (bool)$monthly->is_over_km_budget,
            'km_exceeded_at'    => $monthly->km_exceeded_at,
            'year_month'        => self::getYearMonth(),
        ];
    }

    /**
     * Catat pelanggaran performa operator ketika budget terlampaui
     */
    public static function recordExceedance(WorkOrder $workOrder, UnitMonthlyCost $monthly, Unit $unit): void
    {
        if (!$workOrder->operator_id) return;

        $excessAmount = max(0.0, (float)$monthly->total_cost - (float)$unit->monthly_budget_limit);

        OperatorPerformanceRecord::create([
            'operator_id'              => $workOrder->operator_id,
            'unit_id'                  => $workOrder->unit_id,
            'work_order_id'            => $workOrder->id,
            'year_month'               => self::getYearMonth($workOrder->end_time),
            'monthly_budget_limit'     => $unit->monthly_budget_limit,
            'total_cost_at_exceedance' => $monthly->total_cost,
            'excess_amount'            => $excessAmount,
            'recorded_at'              => now(),
            'notes'                    => "WO {$workOrder->wo_number} menyebabkan budget unit {$unit->unit_code} " .
                                          "terlampaui. Batas IDR " . number_format($unit->monthly_budget_limit, 0, ',', '.') .
                                          ", total IDR " . number_format($monthly->total_cost, 0, ',', '.') .
                                          " (lebih IDR " . number_format($excessAmount, 0, ',', '.') . ").",
        ]);

        // Notifikasi ke manager & admin
        $operatorName = $workOrder->operator->operator_name ?? 'Operator';
        $msg = "Operator {$operatorName} memicu pelampauian budget unit {$unit->unit_code}. " .
               "Budget IDR " . number_format($unit->monthly_budget_limit, 0, ',', '.') . " terlampaui.";
        $link = route('operator-performance.index');

        NotificationService::sendToRole('manager', 'budget_exceeded', "Budget Perbaikan Terlampaui: {$unit->unit_code}", $msg, $link, 'work_order', $workOrder->id);
        NotificationService::sendToRole('admin',   'budget_exceeded', "Budget Perbaikan Terlampaui: {$unit->unit_code}", $msg, $link, 'work_order', $workOrder->id);
    }

    /**
     * Inisiasi approval level tertinggi untuk WO yang over budget
     */
    public static function initiateOverBudgetApproval(WorkOrder $workOrder): void
    {
        $workOrder->loadMissing('unit');

        // Cari level approval tertinggi untuk dokumen tipe 'wo'
        $highestLevel = ApprovalSetting::where('document_type', 'wo')
            ->where('is_active', true)
            ->orderBy('level_order', 'desc')
            ->first();

        $unitCode = $workOrder->unit->unit_code ?? '-';
        $link     = route('work-orders.show', $workOrder);

        if (!$highestLevel) {
            // Tidak ada setting approval WO — kirim notifikasi ke admin
            NotificationService::sendToRole(
                'admin',
                'approval_request',
                "WO Perlu Persetujuan (Over Budget): {$workOrder->wo_number}",
                "Unit {$unitCode} telah melampaui budget perbaikan bulanan. WO {$workOrder->wo_number} membutuhkan persetujuan admin.",
                $link, 'work_order', $workOrder->id
            );
            return;
        }

        ApprovalLog::create([
            'document_type'       => 'wo',
            'document_id'         => $workOrder->id,
            'approval_setting_id' => $highestLevel->id,
            'level_name'          => $highestLevel->level_name,
            'level_order'         => $highestLevel->level_order,
            'action'              => 'pending',
        ]);

        $title = "Persetujuan WO Diperlukan (Over Budget): {$workOrder->wo_number}";
        $msg   = "Unit {$unitCode} melampaui budget perbaikan bulanan. Persetujuan level tertinggi dibutuhkan.";

        if ($highestLevel->approver_user_id) {
            NotificationService::send($highestLevel->approver_user_id, 'approval_request', $title, $msg, $link, 'work_order', $workOrder->id);
        }

        if ($highestLevel->approver_role) {
            NotificationService::sendToRole($highestLevel->approver_role, 'approval_request', $title, $msg, $link, 'work_order', $workOrder->id);
        }
    }
}
