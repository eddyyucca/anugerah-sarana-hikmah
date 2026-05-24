<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitTire;
use App\Models\UnitTireHistory;
use App\Models\UnitOdometerReading;
use App\Models\MaintenanceItem;
use App\Models\MaintenanceLog;
use Illuminate\Support\Facades\DB;

class OdometerService
{
    /**
     * Catat pembacaan odometer baru.
     * Otomatis update total_km semua ban yang terpasang.
     */
    public static function recordReading(Unit $unit, float $newKm, string $date, string $recordedBy = null, string $source = 'manual', string $notes = null): UnitOdometerReading
    {
        return DB::transaction(function () use ($unit, $newKm, $date, $recordedBy, $source, $notes) {
            $deltaKm = max(0, $newKm - $unit->current_odometer);

            $reading = UnitOdometerReading::create([
                'unit_id'      => $unit->id,
                'odometer_km'  => $newKm,
                'delta_km'     => $deltaKm,
                'reading_date' => $date,
                'source'       => $source,
                'recorded_by'  => $recordedBy,
                'notes'        => $notes,
            ]);

            // Update total_km semua ban yang terpasang di unit ini
            if ($deltaKm > 0) {
                UnitTire::where('unit_id', $unit->id)->increment('total_km', $deltaKm);
            }

            $unit->update(['current_odometer' => $newKm]);

            return $reading;
        });
    }

    /**
     * Pasang ban (dari inventory) ke unit pada posisi tertentu.
     * Kurangi stock sparepart.
     */
    public static function installTire(UnitTire $tire, Unit $unit, int $position, string $date): void
    {
        DB::transaction(function () use ($tire, $unit, $position, $date) {
            // Lepas ban lain di posisi yang sama jika ada
            $existing = UnitTire::where('unit_id', $unit->id)->where('position_number', $position)->first();
            if ($existing) {
                self::removeTire($existing, $unit, $date, 'diganti');
            }

            // Lepas ban ini dari unit lama jika sedang terpasang
            if ($tire->unit_id) {
                self::removeTire($tire, $tire->unit, $date, 'pindah unit');
            }

            $labels = $unit->wheel_position_labels;

            // Simpan record history instalasi ini
            UnitTireHistory::create([
                'unit_tire_id'   => $tire->id,
                'unit_id'        => $unit->id,
                'position_number'=> $position,
                'position_label' => $labels[$position] ?? "Posisi $position",
                'odo_at_install' => $unit->current_odometer,
                'installed_at'   => $date,
            ]);

            $tire->update([
                'unit_id'           => $unit->id,
                'position_number'   => $position,
                'position_label'    => $labels[$position] ?? "Posisi $position",
                'odo_when_installed'=> $unit->current_odometer,
                'installed_at'      => $date,
            ]);
        });
    }

    /**
     * Catat maintenance selesai untuk unit.
     * Jika item punya sparepart terhubung, stok otomatis dikurangi.
     */
    public static function recordMaintenance(MaintenanceItem $item, Unit $unit, string $date, string $performedBy = null, float $cost = null, string $notes = null, ?int $sparepartId = null, int $qtyUsed = 0): MaintenanceLog
    {
        // Tentukan sparepart: pakai override jika ada, fallback ke default item
        $usedSparepartId = $sparepartId ?? $item->sparepart_id;
        $usedQty         = $qtyUsed > 0 ? $qtyUsed : ($item->qty_per_service ?? 1);

        $log = MaintenanceLog::create([
            'maintenance_item_id' => $item->id,
            'unit_id'             => $unit->id,
            'sparepart_id'        => $usedSparepartId,
            'qty_used'            => $usedSparepartId ? $usedQty : 0,
            'odometer_at_service' => $unit->current_odometer,
            'next_service_km'     => $unit->current_odometer + $item->interval_km,
            'service_date'        => $date,
            'performed_by'        => $performedBy,
            'cost'                => $cost,
            'notes'               => $notes,
        ]);

        // Kurangi stok inventory jika ada sparepart
        if ($usedSparepartId && $usedQty > 0) {
            \App\Models\Sparepart::where('id', $usedSparepartId)
                ->where('stock_on_hand', '>=', $usedQty)
                ->decrement('stock_on_hand', $usedQty);
        }

        return $log;
    }

    /**
     * Lepas ban dari unit.
     */
    public static function removeTire(UnitTire $tire, Unit $unit, string $date, string $reason = ''): void
    {
        $kmUsed = max(0, $unit->current_odometer - ($tire->odo_when_installed ?? $unit->current_odometer));

        // Update history record terakhir
        UnitTireHistory::where('unit_tire_id', $tire->id)
            ->where('unit_id', $unit->id)
            ->whereNull('removed_at')
            ->update([
                'odo_at_remove' => $unit->current_odometer,
                'km_used'       => $kmUsed,
                'removed_at'    => $date,
                'removed_reason'=> $reason,
            ]);

        $tire->update([
            'unit_id'           => null,
            'position_number'   => null,
            'position_label'    => null,
            'odo_when_installed'=> null,
            'installed_at'      => null,
        ]);
    }

    /**
     * Ambil semua alert mendekati/melewati batas km.
     */
    public static function getAlerts(): array
    {
        $alerts = [];

        // Alert ban
        $tires = UnitTire::with(['unit', 'sparepart'])->whereNotNull('unit_id')->get();
        foreach ($tires as $tire) {
            $remaining = $tire->remaining_km;
            if ($remaining <= 2000) {
                $alerts[] = [
                    'type'        => 'tire',
                    'severity'    => $remaining <= 0 ? 'danger' : ($remaining <= 500 ? 'warning' : 'info'),
                    'unit'        => $tire->unit,
                    'label'       => $tire->position_label,
                    'part'        => $tire->sparepart->part_name ?? '-',
                    'remaining_km'=> $remaining,
                    'message'     => "Ban {$tire->sparepart->part_name} | {$tire->unit->unit_code} ({$tire->position_label}): sisa " . number_format($remaining, 0) . " km",
                ];
            }
        }

        // Alert maintenance item
        $items = MaintenanceItem::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        foreach ($units as $unit) {
            foreach ($items as $item) {
                $lastLog = MaintenanceLog::where('unit_id', $unit->id)
                    ->where('maintenance_item_id', $item->id)
                    ->latest('service_date')->first();
                $nextKm   = $lastLog ? $lastLog->next_service_km : $item->interval_km;
                $remaining = $nextKm - $unit->current_odometer;

                if ($remaining <= $item->alert_before_km) {
                    $alerts[] = [
                        'type'        => 'maintenance',
                        'severity'    => $remaining <= 0 ? 'danger' : ($remaining <= ($item->alert_before_km / 2) ? 'warning' : 'info'),
                        'unit'        => $unit,
                        'label'       => $item->name,
                        'remaining_km'=> $remaining,
                        'next_km'     => $nextKm,
                        'message'     => "{$item->name} | {$unit->unit_code}: " . ($remaining <= 0 ? 'TERLAMBAT ' . number_format(abs($remaining), 0) . ' km' : 'sisa ' . number_format($remaining, 0) . ' km'),
                    ];
                }
            }
        }

        usort($alerts, fn($a, $b) => ['danger' => 0, 'warning' => 1, 'info' => 2][$a['severity']] <=> ['danger' => 0, 'warning' => 1, 'info' => 2][$b['severity']]);

        return $alerts;
    }

    public static function countAlerts(): int
    {
        return count(array_filter(self::getAlerts(), fn($a) => in_array($a['severity'], ['danger', 'warning'])));
    }
}
