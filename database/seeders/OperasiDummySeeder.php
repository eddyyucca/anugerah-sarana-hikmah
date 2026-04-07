<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Operator;
use App\Models\Unit;
use App\Models\P2hCheck;
use App\Models\FitToWork;
use App\Models\Timesheet;
use App\Services\DocumentNumberService;
use Carbon\Carbon;

class OperasiDummySeeder extends Seeder
{
    public function run(): void
    {
        // ── Tambah Operator ───────────────────────────────────────────────────
        $operators = [
            ['operator_code' => 'OPR-001', 'operator_name' => 'Budi Santoso',    'nik' => '3271010101800001', 'phone' => '081234567001', 'license_type' => 'SIO Excavator', 'license_expiry' => '2026-12-31', 'is_active' => true],
            ['operator_code' => 'OPR-002', 'operator_name' => 'Agus Prasetyo',   'nik' => '3271010101820002', 'phone' => '081234567002', 'license_type' => 'SIO Excavator', 'license_expiry' => '2027-06-30', 'is_active' => true],
            ['operator_code' => 'OPR-003', 'operator_name' => 'Hendra Wijaya',   'nik' => '3271010101850003', 'phone' => '081234567003', 'license_type' => 'SIO Dump Truck', 'license_expiry' => '2026-09-15', 'is_active' => true],
            ['operator_code' => 'OPR-004', 'operator_name' => 'Rizky Firmansyah','nik' => '3271010101900004', 'phone' => '081234567004', 'license_type' => 'SIO Excavator', 'license_expiry' => '2025-03-31', 'is_active' => true],
            ['operator_code' => 'OPR-005', 'operator_name' => 'Slamet Riyadi',   'nik' => '3271010101780005', 'phone' => '081234567005', 'license_type' => 'SIO Grader',    'license_expiry' => '2027-01-20', 'is_active' => true],
        ];

        foreach ($operators as $op) {
            Operator::firstOrCreate(['operator_code' => $op['operator_code']], $op);
        }

        $allOperators = Operator::active()->get();
        $units        = Unit::active()->take(6)->get();
        $checkedBy    = 1; // user id admin

        $checklistItems = $this->getChecklistItems();

        // ── Generate data 14 hari terakhir ───────────────────────────────────
        for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo)->format('Y-m-d');

            foreach (['day', 'night'] as $shift) {
                // Ambil 3-4 unit & operator acak per shift
                $selectedUnits = $units->random(min(4, $units->count()));

                foreach ($selectedUnits as $idx => $unit) {
                    $operator = $allOperators->random();

                    // ── FIT TO WORK ───────────────────────────────────────
                    $siapBekerja   = rand(0, 10) > 1; // 90% siap
                    $kondisiSehat  = rand(0, 10) > 1; // 90% sehat
                    $isFit         = $siapBekerja && $kondisiSehat;

                    FitToWork::create([
                        'ftw_number'    => DocumentNumberService::generateFTW(),
                        'operator_id'   => $operator->id,
                        'check_date'    => $date,
                        'shift'         => $shift,
                        'siap_bekerja'  => $siapBekerja,
                        'kondisi_sehat' => $kondisiSehat,
                        'is_fit'        => $isFit,
                        'notes'         => !$isFit ? 'Operator tidak diizinkan bekerja pada shift ini.' : null,
                        'checked_by'    => $checkedBy,
                        'created_at'    => $date . ' ' . ($shift === 'day' ? '06:30:00' : '18:30:00'),
                        'updated_at'    => $date . ' ' . ($shift === 'day' ? '06:30:00' : '18:30:00'),
                    ]);

                    // ── P2H ───────────────────────────────────────────────
                    $currentHm = (float) $unit->hour_meter;
                    $hmStart   = $currentHm;

                    // Status P2H: 70% layak, 20% layak_catatan, 10% tidak_layak
                    $rand   = rand(1, 10);
                    $status = $rand <= 7 ? 'layak' : ($rand <= 9 ? 'layak_catatan' : 'tidak_layak');

                    $p2h = P2hCheck::create([
                        'p2h_number'        => DocumentNumberService::generateP2H(),
                        'unit_id'           => $unit->id,
                        'operator_id'       => $operator->id,
                        'check_date'        => $date,
                        'shift'             => $shift,
                        'hour_meter_start'  => $hmStart,
                        'km_start'          => 0,
                        'overall_status'    => $status,
                        'general_notes'     => $status !== 'layak' ? 'Ada beberapa item yang perlu perhatian.' : null,
                        'reviewed_by'       => $checkedBy,
                        'reviewed_at'       => $date . ' ' . ($shift === 'day' ? '07:00:00' : '19:00:00'),
                        'created_at'        => $date . ' ' . ($shift === 'day' ? '06:45:00' : '18:45:00'),
                        'updated_at'        => $date . ' ' . ($shift === 'day' ? '07:00:00' : '19:00:00'),
                    ]);

                    // Insert checklist items sesuai status
                    foreach ($checklistItems as $category => $items) {
                        foreach ($items as $itemName) {
                            $condition = 'good';
                            if ($status === 'layak_catatan' && rand(0, 5) === 0) $condition = 'warning';
                            if ($status === 'tidak_layak'   && rand(0, 4) === 0) $condition = 'bad';

                            DB::table('p2h_check_items')->insert([
                                'p2h_check_id' => $p2h->id,
                                'category'     => $category,
                                'check_item'   => $itemName,
                                'condition'    => $condition,
                                'notes'        => $condition !== 'good' ? 'Perlu perhatian.' : null,
                            ]);
                        }
                    }

                    // ── TIMESHEET (hanya jika P2H layak/layak_catatan) ───
                    if ($status !== 'tidak_layak') {
                        $workingHours = rand(7, 10) + (rand(0, 9) / 10);
                        $hmEnd        = round($hmStart + $workingHours, 1);
                        $retase       = rand(10, 25);

                        Timesheet::create([
                            'ts_number'         => DocumentNumberService::generateTS(),
                            'p2h_check_id'      => $p2h->id,
                            'unit_id'           => $unit->id,
                            'operator_id'       => $operator->id,
                            'shift_date'        => $date,
                            'shift'             => $shift,
                            'hour_meter_start'  => $hmStart,
                            'hour_meter_end'    => $hmEnd,
                            'working_hours'     => round($workingHours, 2),
                            'retase'            => $retase,
                            'notes'             => null,
                            'submitted_by'      => $checkedBy,
                            'created_at'        => $date . ' ' . ($shift === 'day' ? '17:30:00' : '05:30:00'),
                            'updated_at'        => $date . ' ' . ($shift === 'day' ? '17:30:00' : '05:30:00'),
                        ]);

                        // Update HM unit
                        $unit->update(['hour_meter' => $hmEnd]);
                        $unit->hour_meter = $hmEnd;
                    }
                }
            }
        }

        $this->command->info('Dummy data operasi berhasil dibuat!');
        $this->command->info('FTW: '       . FitToWork::count());
        $this->command->info('P2H: '       . P2hCheck::count());
        $this->command->info('Timesheet: ' . Timesheet::count());
    }

    private function getChecklistItems(): array
    {
        return [
            'Engine'              => ['Kondisi engine oil level', 'Suara engine / abnormal noise', 'Coolant level', 'Exhaust smoke / warna asap', 'Engine temperature normal'],
            'Hydraulic'           => ['Hydraulic oil level', 'Kebocoran hose / fitting', 'Fungsi cylinder boom/arm/bucket', 'Swing function normal'],
            'Electrical'          => ['Kondisi battery / tegangan', 'Lampu kerja / work light', 'Horn / klakson', 'Instrument panel / gauge', 'Wiper & washer'],
            'Brake & Steering'    => ['Fungsi service brake', 'Fungsi parking brake', 'Fungsi steering / kemudi', 'Brake fluid level'],
            'Body & Cabin'        => ['Kaca cabin / windshield', 'Seat belt', 'Pintu cabin / lock', 'Mirror / kaca spion', 'Kebersihan cabin'],
            'Safety Equipment'    => ['Fire extinguisher / APAR', 'Rotating beacon / strobe light', 'Back-up alarm / mundur', 'Safety pin / lock', 'Reflector / sticker safety'],
            'Undercarriage / Tire'=> ['Kondisi track / ban', 'Track tension / tekanan ban', 'Kondisi roller / idler', 'Kebocoran grease'],
        ];
    }
}
