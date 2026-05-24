<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Sparepart;
use App\Models\UnitTire;
use App\Models\UnitOdometerReading;
use App\Models\MaintenanceItem;
use App\Models\MaintenanceLog;
use App\Services\OdometerService;

class OdometerTireSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Sparepart ban (jika belum ada) ────────────────────────────────
        $tireParts = [
            ['part_number' => 'BAN-1200R24', 'part_name' => 'Ban Bridgestone 12.00R24',       'stock_on_hand' => 24, 'unit_price' => 8500000],
            ['part_number' => 'BAN-1400R25', 'part_name' => 'Ban Michelin 14.00R25',           'stock_on_hand' => 16, 'unit_price' => 14500000],
            ['part_number' => 'BAN-1800R33', 'part_name' => 'Ban Bridgestone 18.00R33 (100T)', 'stock_on_hand' => 8,  'unit_price' => 42000000],
            ['part_number' => 'BAN-WL-23.5', 'part_name' => 'Ban Loader 23.5-25',              'stock_on_hand' => 8,  'unit_price' => 9800000],
            ['part_number' => 'BAN-GR-13.00','part_name' => 'Ban Grader 13.00-24',             'stock_on_hand' => 6,  'unit_price' => 7200000],
        ];

        $partIds = [];
        foreach ($tireParts as $p) {
            $sp = Sparepart::firstOrCreate(
                ['part_number' => $p['part_number']],
                array_merge($p, ['uom' => 'pcs', 'minimum_stock' => 4, 'is_active' => true])
            );
            $partIds[$p['part_number']] = $sp->id;
        }

        // ── 2. Atur wheel_count per unit & set odometer awal ─────────────────
        $unitConfig = [
            // [unit_code, wheel_count, current_odometer (km)]
            ['DT-001', 12, 38500],
            ['DT-002', 8,  27300],
            ['DT-003', 12, 52100],
            ['DT-004', 12, 19800],
            ['DT-005', 8,  31600],
            ['TR02 031', 8, 44200],
            ['DT025',   8,  11500],
            ['LD-001',  4,  18200],
            ['LD-002',  4,  9400],
            ['GR-001',  6,  22700],
            ['GR-002',  6,  14300],
        ];

        foreach ($unitConfig as [$code, $wheels, $odo]) {
            Unit::where('unit_code', $code)->update([
                'wheel_count'      => $wheels,
                'current_odometer' => $odo,
            ]);
        }

        // ── 3. Odometer history (simulasi 6 bulan terakhir) ──────────────────
        $this->seedOdometerHistory();

        // ── 4. Maintenance items ──────────────────────────────────────────────
        $maintItems = [
            ['name' => 'Ganti Oli Mesin',        'interval_km' => 5000,  'alert_before_km' => 500],
            ['name' => 'Ganti Filter Oli',        'interval_km' => 5000,  'alert_before_km' => 500],
            ['name' => 'Ganti Filter Bahan Bakar','interval_km' => 10000, 'alert_before_km' => 1000],
            ['name' => 'Ganti Filter Udara',      'interval_km' => 10000, 'alert_before_km' => 1000],
            ['name' => 'Rotasi Ban',              'interval_km' => 8000,  'alert_before_km' => 800],
        ];

        $maintIds = [];
        foreach ($maintItems as $item) {
            $mi = MaintenanceItem::firstOrCreate(['name' => $item['name']], array_merge($item, ['is_active' => true]));
            $maintIds[$item['name']] = $mi->id;
        }

        // ── 5. Log maintenance history per unit ──────────────────────────────
        $this->seedMaintenanceLogs($maintIds);

        // ── 6. Pasang ban ke semua DT & unit beroda ───────────────────────────
        $this->seedTireInstallations($partIds);

        $this->command->info('✅ Seeder selesai. Odometer, ban, dan maintenance item berhasil dibuat.');
    }

    private function seedOdometerHistory(): void
    {
        $scenarios = [
            // [unit_code, readings: [bulan_lalu, km], ...]
            ['DT-001',   [6=>5200, 5=>10800, 4=>17500, 3=>24100, 2=>31200, 1=>38500]],
            ['DT-002',   [6=>3800, 5=>8200,  4=>13100, 3=>18400, 2=>22900, 1=>27300]],
            ['DT-003',   [6=>8500, 5=>16200, 4=>24700, 3=>33100, 2=>43600, 1=>52100]],
            ['DT-004',   [6=>2100, 5=>5300,  4=>8800,  3=>12400, 2=>16100, 1=>19800]],
            ['DT-005',   [6=>4200, 5=>9100,  4=>14600, 3=>20300, 2=>26100, 1=>31600]],
            ['TR02 031', [6=>6800, 5=>13500, 4=>21200, 3=>29800, 2=>37100, 1=>44200]],
            ['DT025',    [5=>2200, 4=>4800,  3=>7500,  2=>9600,  1=>11500]],
            ['LD-001',   [6=>2800, 5=>5900,  4=>9400,  3=>12700, 2=>15800, 1=>18200]],
            ['LD-002',   [5=>1600, 4=>3700,  3=>5900,  2=>7800,  1=>9400]],
            ['GR-001',   [6=>3600, 5=>7800,  4=>12100, 3=>16500, 2=>19900, 1=>22700]],
            ['GR-002',   [6=>1800, 5=>4200,  4=>7600,  3=>10400, 2=>12600, 1=>14300]],
        ];

        foreach ($scenarios as [$code, $readings]) {
            $unit = Unit::where('unit_code', $code)->first();
            if (!$unit) continue;

            $prev = 0;
            foreach ($readings as $monthsAgo => $km) {
                $date = now()->subMonths($monthsAgo)->format('Y-m-d');
                UnitOdometerReading::create([
                    'unit_id'      => $unit->id,
                    'odometer_km'  => $km,
                    'delta_km'     => $km - $prev,
                    'reading_date' => $date,
                    'source'       => 'p2h',
                    'recorded_by'  => 'P2H - Operator',
                    'notes'        => 'Data awal (seeder)',
                ]);
                $prev = $km;
            }
        }
    }

    private function seedMaintenanceLogs(array $maintIds): void
    {
        // DT-001: 38500 km — oli terakhir di 35000, jadi sisa 1500 km → warning
        $this->logMaint($maintIds['Ganti Oli Mesin'],         'DT-001', 35000, 40000, '-3 months');
        $this->logMaint($maintIds['Ganti Filter Oli'],        'DT-001', 35000, 40000, '-3 months');
        $this->logMaint($maintIds['Ganti Filter Bahan Bakar'],'DT-001', 30000, 40000, '-4 months');
        $this->logMaint($maintIds['Rotasi Ban'],              'DT-001', 32000, 40000, '-3 months');

        // DT-002: 27300 km — oli terakhir di 25000, sisa 2700 km → info
        $this->logMaint($maintIds['Ganti Oli Mesin'],         'DT-002', 25000, 30000, '-2 months');
        $this->logMaint($maintIds['Ganti Filter Bahan Bakar'],'DT-002', 20000, 30000, '-4 months');

        // DT-003: 52100 km — oli terakhir 50000, sisa 2900 km → info
        $this->logMaint($maintIds['Ganti Oli Mesin'],         'DT-003', 50000, 55000, '-1 months');
        $this->logMaint($maintIds['Ganti Filter Oli'], 'DT-003', 50000, 55000, '-1 months');
        $this->logMaint($maintIds['Rotasi Ban'],              'DT-003', 48000, 56000, '-2 months');

        // DT-004: 19800 km — oli belum pernah ganti (interval 5000) → DANGER di km 5000
        // (tidak ada log = sudah 19800 km tanpa ganti oli = 14800 km terlambat!)

        // DT-005: 31600 km — oli terakhir 30000, sisa 3400 km
        $this->logMaint($maintIds['Ganti Oli Mesin'],         'DT-005', 30000, 35000, '-1 months');

        // LD-001: 18200 km
        $this->logMaint($maintIds['Ganti Oli Mesin'],         'LD-001', 15000, 20000, '-2 months');
    }

    private function logMaint(int $itemId, string $unitCode, float $odoAtService, float $nextKm, string $dateOffset): void
    {
        $unit = Unit::where('unit_code', $unitCode)->first();
        if (!$unit) return;

        MaintenanceLog::create([
            'maintenance_item_id' => $itemId,
            'unit_id'             => $unit->id,
            'odometer_at_service' => $odoAtService,
            'next_service_km'     => $nextKm,
            'service_date'        => now()->modify($dateOffset)->format('Y-m-d'),
            'performed_by'        => 'Tim Mekanik',
            'cost'                => rand(300, 800) * 1000,
            'notes'               => 'Rutin',
        ]);
    }

    private function seedTireInstallations(array $partIds): void
    {
        // Konfigurasi: [unit_code, part_number_key, km_limit, total_km_ban_sudah_terpakai]
        // Sengaja variasi: ada yang hampir habis, ada yang baru, ada yang sudah lewat batas

        $configs = [
            // DT-001 (12 roda, 38500 km) — ban 12.00R24, limit 40000
            // Posisi 1-2 sudah lama (total km ~36000, hampir batas)
            // Posisi 3-6 sedang (total km ~20000)
            // Posisi 7-12 masih baru (total km ~5000)
            ['DT-001', 'BAN-1200R24', 40000, [
                1 => 36200, 2 => 35800,                          // Hampir batas (warning/danger)
                3 => 21500, 4 => 22100, 5 => 21800, 6 => 22300,  // Sedang
                7 => 5200,  8 => 5100,  9 => 5300, 10 => 5000,   // Baru
                11 => 5400, 12 => 5100,
            ]],

            // DT-002 (8 roda, 27300 km) — ban 12.00R24, limit 40000
            ['DT-002', 'BAN-1200R24', 40000, [
                1 => 27300, 2 => 26800,                          // Sudah 6 bulan, km = odo unit
                3 => 15200, 4 => 15500, 5 => 15300, 6 => 15100,
                7 => 8200,  8 => 8100,
            ]],

            // DT-003 (12 roda, 52100 km) — ban 14.00R25, limit 50000
            // Pos 1-4 sudah MELEWATI batas (danger!)
            ['DT-003', 'BAN-1400R25', 50000, [
                1 => 52100, 2 => 51800, 3 => 51200, 4 => 50800,  // Lewat batas!
                5 => 38500, 6 => 38200, 7 => 37900, 8 => 38100,
                9 => 22000, 10 => 21500, 11 => 22300, 12 => 21800,
            ]],

            // DT-004 (12 roda, 19800 km) — ban 18.00R33, limit 60000
            ['DT-004', 'BAN-1800R33', 60000, [
                1 => 19800, 2 => 19800, 3 => 19800, 4 => 19800,
                5 => 19800, 6 => 19800, 7 => 19800, 8 => 19800,
                9 => 19800, 10 => 19800, 11 => 19800, 12 => 19800,
            ]],

            // DT-005 (8 roda, 31600 km) — ban 12.00R24, limit 40000
            ['DT-005', 'BAN-1200R24', 40000, [
                1 => 31600, 2 => 31200,
                3 => 18400, 4 => 18600, 5 => 18500, 6 => 18300,
                7 => 9800,  8 => 9600,
            ]],

            // TR02 031 (8 roda, 44200 km) — ban 12.00R24, limit 40000
            // Beberapa sudah over!
            ['TR02 031', 'BAN-1200R24', 40000, [
                1 => 41200, 2 => 40800,  // Over limit
                3 => 38500, 4 => 39200,  // Mendekati
                5 => 24100, 6 => 23800,
                7 => 11500, 8 => 11200,
            ]],

            // DT025 (8 roda, 11500 km) — ban 12.00R24, limit 40000 — semua masih baru
            ['DT025', 'BAN-1200R24', 40000, [
                1 => 11500, 2 => 11500, 3 => 11500, 4 => 11500,
                5 => 11500, 6 => 11500, 7 => 11500, 8 => 11500,
            ]],

            // LD-001 (4 roda, 18200 km) — ban Loader
            ['LD-001', 'BAN-WL-23.5', 6000, [
                1 => 18200, 2 => 17800, 3 => 12400, 4 => 12100,
            ]],

            // GR-001 (6 roda, 22700 km) — ban Grader
            ['GR-001', 'BAN-GR-13.00', 15000, [
                1 => 14200, 2 => 13800,
                3 => 8500,  4 => 8200,
                5 => 4100,  6 => 3900,
            ]],
        ];

        foreach ($configs as [$code, $partKey, $kmLimit, $positions]) {
            $unit = Unit::where('unit_code', $code)->first();
            if (!$unit) continue;

            $partId = $partIds[$partKey] ?? null;
            if (!$partId) continue;

            $labels = $unit->wheel_position_labels;

            foreach ($positions as $pos => $totalKm) {
                // total_km = km kumulatif ban ini (sudah termasuk seluruh pemakaian).
                // OdometerService::recordReading() akan increment total_km saat odo masuk.
                // Untuk seeder, total_km langsung kita set sebagai nilai akhir sekarang.
                // odo_when_installed hanya dipakai untuk menghitung km_used saat dilepas.
                $odoAtInstall = max(0, $unit->current_odometer - $totalKm);
                $monthsAgo    = max(1, (int)($totalKm / 6000));

                $tire = UnitTire::create([
                    'sparepart_id'      => $partId,
                    'unit_id'           => $unit->id,
                    'position_number'   => $pos,
                    'position_label'    => $labels[$pos] ?? "Posisi $pos",
                    'total_km'          => $totalKm,
                    'km_limit'          => $kmLimit,
                    'odo_when_installed'=> $odoAtInstall,
                    'installed_at'      => now()->subMonths($monthsAgo)->format('Y-m-d'),
                ]);

                \App\Models\UnitTireHistory::create([
                    'unit_tire_id'   => $tire->id,
                    'unit_id'        => $unit->id,
                    'position_number'=> $pos,
                    'position_label' => $labels[$pos] ?? "Posisi $pos",
                    'odo_at_install' => $odoAtInstall,
                    'installed_at'   => $tire->installed_at,
                ]);
            }

            Sparepart::where('id', $partId)->decrement('stock_on_hand', count($positions));
        }
    }
}
