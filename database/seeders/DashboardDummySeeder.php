<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Seeder komprehensif: semua unit DT, data operasional Mei–Jun 2026
 */
class DashboardDummySeeder extends Seeder
{
    // ── Counters ──────────────────────────────────────────────────────────────
    private int $woSeq   = 0;
    private int $giSeq   = 0;
    private int $grSeq   = 0;
    private int $poSeq   = 0;
    private int $prSeq   = 0;
    private int $p2hSeq  = 0;
    private int $ftwSeq  = 0;
    private int $tsSeq   = 0;

    // ── Runtime maps (filled during seeding) ─────────────────────────────────
    private array $unitIds      = [];
    private array $units        = [];   // id => [odo, budget_cost, budget_km, wheel_count]
    private array $operatorIds  = [];
    private array $techIds      = [];
    private array $supplierIds  = [];
    private array $sparepartMap = []; // part_number => ['id', 'price', 'type']
    private array $tireSpIds    = []; // sparepart ids yang tipe ban
    private int   $warehouseId  = 1;
    private array $unitTireIds  = []; // unit_id => [position => tire_id]

    // Monthly cost accumulator: unit_id => year_month => total_cost
    private array $monthlyCost = [];
    private array $monthlyKm   = [];
    private array $monthlyWoCount = [];

    public function run(): void
    {
        $this->command->info('🧹 Menghapus semua data lama...');
        $this->truncateAll();

        $this->command->info('🏗️  Membuat master data...');
        $this->seedMaster();

        $this->command->info('🔩 Memasang ban ke semua unit...');
        $this->seedTires();

        $this->command->info('🚛 Membuat data operasional Mei–Jun 2026...');
        $start = Carbon::create(2026, 5, 1);
        $today = Carbon::today();
        $cur   = $start->copy();
        while ($cur->lte($today)) {
            $this->generateDay($cur->copy());
            $cur->addDay();
        }

        $this->command->info('💰 Menyimpan budget bulanan...');
        $this->flushMonthlyCosts();

        $this->command->info('📦 Membuat procurement (PR→PO→GR)...');
        $this->seedProcurement();

        $this->command->info('🔧 Update stok akhir unit...');
        $this->updateUnitOdometers();

        $this->printSummary();
    }

    // ── TRUNCATE ──────────────────────────────────────────────────────────────
    private function truncateAll(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = [
            'operator_warning_letters',
            'operator_performance_records',
            'unit_monthly_costs',
            'maintenance_logs', 'maintenance_items',
            'unit_tire_history', 'unit_tires',
            'unit_odometer_readings',
            'tire_damage_reports',
            'supplier_return_items', 'supplier_returns',
            'timesheets', 'fit_to_work_checks', 'p2h_check_items', 'p2h_checks',
            'stock_movements',
            'goods_issue_items', 'goods_issues',
            'goods_receipt_items', 'goods_receipts',
            'purchase_order_items', 'purchase_orders',
            'purchase_request_items', 'purchase_requests',
            'repair_cost_summaries', 'work_order_logs', 'work_orders',
            'unit_availability_logs',
            'approval_logs',
            'notifications',
            'operators',
            'technicians',
            'suppliers',
            'spareparts',
            'sparepart_categories',
            'units',
            'unit_categories',
            'warehouse_locations',
            'complaint_types',
        ];
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    // ── MASTER DATA ───────────────────────────────────────────────────────────
    private function seedMaster(): void
    {
        $now = now();

        // Complaint Types (relevan DT)
        $ctIds = [];
        foreach ([
            ['name'=>'Rem Tidak Pakem',         'color'=>'#dc2626'],
            ['name'=>'Mesin Overheat',           'color'=>'#f97316'],
            ['name'=>'Hydraulic Body Lemah',     'color'=>'#f59e0b'],
            ['name'=>'Ban Kempes / Pecah',       'color'=>'#8b5cf6'],
            ['name'=>'Transmisi Keras / Slip',   'color'=>'#3b82f6'],
            ['name'=>'Oli Mesin Bocor',          'color'=>'#10b981'],
            ['name'=>'Alternator Tidak Charge',  'color'=>'#6366f1'],
            ['name'=>'Starter Motor Lemah',      'color'=>'#ec4899'],
            ['name'=>'AC Cabin Rusak',           'color'=>'#14b8a6'],
            ['name'=>'Filter Buntu',             'color'=>'#84cc16'],
            ['name'=>'Lampu Kerja Mati',         'color'=>'#eab308'],
            ['name'=>'Klakson / Wiper Rusak',    'color'=>'#a3a3a3'],
            ['name'=>'Kopling Slip',             'color'=>'#dc2626'],
            ['name'=>'Steering Berat',           'color'=>'#f97316'],
            ['name'=>'Body / Dump Bocor',        'color'=>'#78716c'],
        ] as $ct) {
            $ctIds[] = DB::table('complaint_types')->insertGetId(array_merge($ct, ['created_at'=>$now,'updated_at'=>$now]));
        }
        $this->unitIds = []; // reset

        // Unit Category
        $catDt = DB::table('unit_categories')->insertGetId(['name'=>'Dump Truck','description'=>'Unit hauling tambang','created_at'=>$now,'updated_at'=>$now]);

        // Warehouse
        $wh = DB::table('warehouse_locations')->insertGetId(['code'=>'GDG-01','name'=>'Gudang Utama','description'=>'Gudang spare part utama','created_at'=>$now,'updated_at'=>$now]);
        $this->warehouseId = $wh;

        // Units (8 DT)
        $unitDefs = [
            ['DT-001','Hino 500 FM 260JD','Dump Truck 20T','Mining',  6, 18500, 75_000_000, 6000],
            ['DT-002','Mitsubishi Fuso FZ629','Dump Truck 20T','Mining', 6, 22000, 80_000_000, 6500],
            ['DT-003','Isuzu Giga GXZ77','Dump Truck 18T','Mining',   6, 15000, 70_000_000, 5500],
            ['DT-004','Hino 500 FM 260JD','Dump Truck 20T','Mining',  6, 31000, 75_000_000, 6000],
            ['DT-005','Mitsubishi Fuso FV515','Dump Truck 15T','Mining',6, 8500,  60_000_000, 5000],
            ['DT-006','Scania G450','Dump Truck 25T','Mining',        10, 45000, 90_000_000, 7000],
            ['DT-007','Hino 500 FM 260JD','Dump Truck 20T','Mining',  6, 12000, 75_000_000, 6000],
            ['DT-008','Volvo FMX 440','Dump Truck 25T','Mining',      10, 67000,100_000_000, 7500],
        ];

        foreach ($unitDefs as [$code,$model,$type,$dept,$wheels,$odo,$budgetCost,$budgetKm]) {
            $id = DB::table('units')->insertGetId([
                'unit_code'            => $code,
                'unit_model'           => $model,
                'unit_type'            => $type,
                'category_id'          => $catDt,
                'department'           => $dept,
                'current_status'       => 'available',
                'hour_meter'           => round($odo / 20, 2), // approx HM
                'current_odometer'     => $odo,
                'wheel_count'          => $wheels,
                'monthly_budget_limit' => $budgetCost,
                'monthly_km_budget'    => $budgetKm,
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
            $this->unitIds[]   = $id;
            $this->units[$id]  = ['odo'=>$odo,'budget_cost'=>$budgetCost,'budget_km'=>$budgetKm,'wheel_count'=>$wheels,'code'=>$code];
        }

        // Sparepart Categories
        $catTire  = DB::table('sparepart_categories')->insertGetId(['name'=>'Ban','description'=>'Ban DT','created_at'=>$now,'updated_at'=>$now]);
        $catOil   = DB::table('sparepart_categories')->insertGetId(['name'=>'Pelumas','description'=>'Oli & Grease','created_at'=>$now,'updated_at'=>$now]);
        $catFilter= DB::table('sparepart_categories')->insertGetId(['name'=>'Filter','description'=>'Filter mesin','created_at'=>$now,'updated_at'=>$now]);
        $catBrake = DB::table('sparepart_categories')->insertGetId(['name'=>'Rem','description'=>'Komponen rem','created_at'=>$now,'updated_at'=>$now]);
        $catElec  = DB::table('sparepart_categories')->insertGetId(['name'=>'Elektrikal','description'=>'Kelistrikan','created_at'=>$now,'updated_at'=>$now]);
        $catHydro = DB::table('sparepart_categories')->insertGetId(['name'=>'Hydraulik','description'=>'Komponen hydraulik','created_at'=>$now,'updated_at'=>$now]);
        $catBody  = DB::table('sparepart_categories')->insertGetId(['name'=>'Body & Cabin','description'=>'Bodi dan cabin','created_at'=>$now,'updated_at'=>$now]);
        $catEngine= DB::table('sparepart_categories')->insertGetId(['name'=>'Mesin','description'=>'Komponen mesin','created_at'=>$now,'updated_at'=>$now]);

        $spDefs = [
            // Ban
            ['BAN-BS-1222',  'Ban Bridgestone 12R22.5',       $catTire, 4_200_000,  'tire', 8, 20],
            ['BAN-DL-1122',  'Ban Dunlop 11R22.5',            $catTire, 3_800_000,  'tire', 6, 20],
            ['BAN-GT-1222',  'Ban GT Radial 12R22.5',         $catTire, 3_500_000,  'tire', 8, 20],
            ['BAN-MC-1222',  'Ban Michelin XZY3 12R22.5',     $catTire, 5_100_000,  'tire', 4, 20],
            // Oli
            ['OLI-ME-20W50', 'Oli Mesin SAE 20W50 (18L)',     $catOil,  520_000,    'oil',  30, 5],
            ['OLI-TR-TF60',  'Oli Transmisi TF-0870 (18L)',   $catOil,  680_000,    'oil',  20, 5],
            ['OLI-HY-46',    'Oli Hydraulik VG46 (20L)',      $catOil,  450_000,    'oil',  20, 5],
            ['OLI-GA-80W90', 'Oli Gardan SAE 80W90 (5L)',     $catOil,  180_000,    'oil',  20, 3],
            // Filter
            ['FLT-OIL-001',  'Filter Oli Mesin',              $catFilter,145_000,   'filter',30,3],
            ['FLT-AIR-001',  'Filter Udara Primer',           $catFilter,385_000,   'filter',15,3],
            ['FLT-FUL-001',  'Filter Solar',                  $catFilter, 95_000,   'filter',30,3],
            ['FLT-HYD-001',  'Filter Hydraulik Return',       $catFilter,220_000,   'filter',15,2],
            // Rem
            ['REM-KMP-001',  'Kampas Rem (Set 4 pcs)',        $catBrake, 980_000,   'brake', 10,3],
            ['REM-DRM-001',  'Drum Rem Belakang',             $catBrake,2_400_000,  'brake',  5,2],
            ['REM-CYL-001',  'Wheel Cylinder Rem',            $catBrake, 650_000,   'brake',  8,2],
            // Elektrikal
            ['ELE-BTR-12V',  'Baterai 12V 200Ah',            $catElec, 1_800_000,  'elec',   8,2],
            ['ELE-ALT-001',  'Alternator 24V 100A',           $catElec, 4_500_000,  'elec',   3,1],
            ['ELE-STR-001',  'Starter Motor 24V',             $catElec, 3_200_000,  'elec',   3,1],
            ['ELE-LAM-001',  'Lampu HID Kerja 70W',           $catElec,  320_000,   'elec',  10,2],
            // Hydraulik
            ['HYD-SIL-001',  'Seal Kit Silinder Angkat',     $catHydro,1_200_000,  'hydro',  6,2],
            ['HYD-PMP-001',  'Pompa Hydraulik Gear Type',    $catHydro,8_500_000,  'hydro',  2,1],
            ['HYD-HOS-001',  'Hose Hydraulik 1" x 1m',       $catHydro, 380_000,   'hydro', 10,3],
            // Body
            ['BOD-WIP-001',  'Wiper Arm & Blade Set',        $catBody,  175_000,   'body',  10,2],
            ['BOD-KAC-001',  'Kaca Spion Kanan DT',          $catBody,  420_000,   'body',   5,1],
            // Mesin
            ['MES-FBL-001',  'Fan Belt Set',                 $catEngine,285_000,   'engine', 8,2],
            ['MES-TMB-001',  'Timing Belt',                  $catEngine,1_850_000, 'engine', 3,1],
            ['MES-THM-001',  'Thermostat Mesin',             $catEngine, 320_000,  'engine', 5,1],
            ['MES-WPM-001',  'Water Pump',                   $catEngine,2_100_000, 'engine', 3,1],
        ];

        foreach ($spDefs as [$pn, $name, $catId, $price, $type, $stock, $minStock]) {
            $id = DB::table('spareparts')->insertGetId([
                'part_number'   => $pn,
                'part_name'     => $name,
                'category_id'   => $catId,
                'unit_price'    => $price,
                'minimum_stock' => $minStock,
                'stock_on_hand' => $stock,
                'uom'           => $type === 'tire' ? 'PCS' : ($type === 'oil' ? 'JRG' : 'PCS'),
                'is_active'     => 1,
                'is_consumable' => in_array($type, ['oil','filter']) ? 1 : 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $this->sparepartMap[$pn] = ['id'=>$id,'price'=>$price,'type'=>$type,'name'=>$name,'stock'=>$stock];
            if ($type === 'tire') {
                $this->tireSpIds[] = $id;
            }
        }

        // Suppliers
        $supplierDefs = [
            ['SUP-001','PT Bridgestone Tire Indonesia','Andi Santoso','021-5551234','procurement@bridgestone.co.id'],
            ['SUP-002','PT Dunlop Tires Indonesia','Budi Rahman','021-5552345','budi@dunloptires.co.id'],
            ['SUP-003','CV Mitra Teknik Jaya','Candra Wijaya','0811234567','mitra.teknik@gmail.com'],
            ['SUP-004','PT Indolube Lubricants','Deni Saputra','021-7893456','sales@indolube.co.id'],
            ['SUP-005','PT Graha Spare Parts','Eko Prasetyo','0812345678','graha.sp@yahoo.com'],
        ];
        foreach ($supplierDefs as [$code,$name,$contact,$phone,$email]) {
            $this->supplierIds[] = DB::table('suppliers')->insertGetId([
                'supplier_code'  => $code,
                'supplier_name'  => $name,
                'contact_person' => $contact,
                'phone'          => $phone,
                'email'          => $email,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // Technicians
        $techDefs = [
            ['TC-001','Ahmad Fauzi','Mekanik Senior','082111111111'],
            ['TC-002','Beni Kurniawan','Mekanik','082122222222'],
            ['TC-003','Cecep Hidayat','Elektrikal','082133333333'],
            ['TC-004','Dedi Suprapto','Hydraulik Specialist','082144444444'],
            ['TC-005','Eko Santoso','Mekanik','082155555555'],
            ['TC-006','Fajar Nugroho','Mekanik Junior','082166666666'],
        ];
        foreach ($techDefs as [$code,$name,$skill,$phone]) {
            $this->techIds[] = DB::table('technicians')->insertGetId([
                'technician_code' => $code,
                'technician_name' => $name,
                'skill'           => $skill,
                'phone'           => $phone,
                'is_active'       => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        // Operators
        $opDefs = [
            ['OP-001','Gunawan Setiadi','3201011501890001','083111111111','SIM B2',Carbon::create(2027,3,15)],
            ['OP-002','Hendra Wijaya','3271025603880002','083122222222','SIM B2',Carbon::create(2026,11,20)],
            ['OP-003','Irwan Maulana','3578061204900003','083133333333','SIM B2',Carbon::create(2027,7,1)],
            ['OP-004','Joko Susilo','3374090807850004','083144444444','SIM B2',Carbon::create(2026,12,31)],
            ['OP-005','Karyadi','3201021001920005','083155555555','SIM B2',Carbon::create(2027,5,10)],
            ['OP-006','Lukman Hakim','3578040906870006','083166666666','SIM B2',Carbon::create(2028,1,5)],
            ['OP-007','Muhamad Rizki','3201030204910007','083177777777','SIM B2',Carbon::create(2027,9,15)],
            ['OP-008','Nanang Suharto','3374051103880008','083188888888','SIM B2',Carbon::create(2026,8,20)],
        ];
        foreach ($opDefs as [$code,$name,$nik,$phone,$lic,$licExp]) {
            $this->operatorIds[] = DB::table('operators')->insertGetId([
                'operator_code'  => $code,
                'operator_name'  => $name,
                'nik'            => $nik,
                'phone'          => $phone,
                'license_type'   => $lic,
                'license_expiry' => $licExp->toDateString(),
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // Maintenance Items (km-based)
        DB::table('maintenance_items')->insert([
            ['name'=>'Ganti Oli Mesin',        'interval_km'=>10000,'alert_before_km'=>500,'sparepart_id'=>$this->sp('OLI-ME-20W50'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Ganti Filter Oli',       'interval_km'=>10000,'alert_before_km'=>500,'sparepart_id'=>$this->sp('FLT-OIL-001'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Ganti Filter Udara',     'interval_km'=>20000,'alert_before_km'=>1000,'sparepart_id'=>$this->sp('FLT-AIR-001'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Ganti Oli Transmisi',    'interval_km'=>40000,'alert_before_km'=>2000,'sparepart_id'=>$this->sp('OLI-TR-TF60'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Ganti Filter Hydraulik', 'interval_km'=>20000,'alert_before_km'=>1000,'sparepart_id'=>$this->sp('FLT-HYD-001'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Cek & Ganti Fan Belt',   'interval_km'=>30000,'alert_before_km'=>2000,'sparepart_id'=>$this->sp('MES-FBL-001'),'qty_per_service'=>1,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }

    private function sp(string $pn): int
    {
        return $this->sparepartMap[$pn]['id'];
    }

    // ── TIRES INSTALLATION ────────────────────────────────────────────────────
    private function seedTires(): void
    {
        $installDate = Carbon::create(2026, 5, 1);
        $tireSpArr   = array_values($this->tireSpIds);

        $posLabels6  = [1=>'Steer Kiri',2=>'Steer Kanan',3=>'Drive Kiri Luar',4=>'Drive Kiri Dalam',5=>'Drive Kanan Dalam',6=>'Drive Kanan Luar'];
        $posLabels10 = [1=>'Steer Kiri',2=>'Steer Kanan',3=>'D1 Ki Luar',4=>'D1 Ki Dalam',5=>'D1 Ka Dalam',6=>'D1 Ka Luar',7=>'D2 Ki Luar',8=>'D2 Ki Dalam',9=>'D2 Ka Dalam',10=>'D2 Ka Luar'];

        $serialSeq = 1;
        foreach ($this->unitIds as $uid) {
            $unitInfo  = $this->units[$uid];
            $wheels    = $unitInfo['wheel_count'];
            $posLabels = $wheels === 10 ? $posLabels10 : $posLabels6;
            $this->unitTireIds[$uid] = [];

            for ($pos = 1; $pos <= $wheels; $pos++) {
                // Pilih tipe ban: steer = Bridgestone, drive = GT Radial atau Dunlop
                $spId = ($pos <= 2)
                    ? $this->sp('BAN-BS-1222')
                    : ($pos % 2 === 1 ? $this->sp('BAN-GT-1222') : $this->sp('BAN-DL-1122'));

                $kmUsedAlready = rand(5000, 18000); // km terpakai sejak ban dipasang sebelumnya
                $sn = 'SN' . str_pad($serialSeq++, 6, '0', STR_PAD_LEFT);

                $tireId = DB::table('unit_tires')->insertGetId([
                    'sparepart_id'       => $spId,
                    'serial_number'      => $sn,
                    'unit_id'            => $uid,
                    'position_number'    => $pos,
                    'position_label'     => $posLabels[$pos],
                    'total_km'           => $kmUsedAlready,
                    'km_limit'           => 40000,
                    'odo_when_installed' => $unitInfo['odo'] - $kmUsedAlready,
                    'installed_at'       => $installDate->toDateString(),
                    'created_at'         => $installDate,
                    'updated_at'         => $installDate,
                ]);

                DB::table('unit_tire_history')->insert([
                    'unit_tire_id'   => $tireId,
                    'unit_id'        => $uid,
                    'position_number'=> $pos,
                    'position_label' => $posLabels[$pos],
                    'odo_at_install' => $unitInfo['odo'] - $kmUsedAlready,
                    'odo_at_remove'  => null,
                    'km_used'        => $kmUsedAlready,
                    'installed_at'   => $installDate->toDateString(),
                    'removed_at'     => null,
                    'created_at'     => $installDate,
                    'updated_at'     => $installDate,
                ]);

                $this->unitTireIds[$uid][$pos] = $tireId;
            }
        }
    }

    // ── DAILY OPERATIONS ──────────────────────────────────────────────────────
    private function generateDay(Carbon $date): void
    {
        $isWeekend  = $date->isWeekend();
        $activeUnits = $isWeekend
            ? array_slice($this->unitIds, 0, 5)  // weekend: 5 unit jalan
            : $this->unitIds;

        // P2H + Timesheet + Odometer per unit aktif
        foreach ($activeUnits as $uid) {
            $opId = $this->operatorIds[array_rand($this->operatorIds)];
            $this->createP2hAndTimesheet($uid, $opId, $date);
        }

        // Work Orders
        $woCount = $isWeekend ? rand(1, 3) : rand(3, 7);
        $usedUnits = [];
        for ($i = 0; $i < $woCount; $i++) {
            $uid = $this->unitIds[array_rand($this->unitIds)];
            // Max 2 WO per unit per day
            if (($usedUnits[$uid] ?? 0) >= 2) continue;
            $usedUnits[$uid] = ($usedUnits[$uid] ?? 0) + 1;
            $this->createWorkOrder($uid, $date);
        }

        // Availability logs
        foreach ($this->unitIds as $uid) {
            $inWO       = ($usedUnits[$uid] ?? 0) > 0;
            $avail      = $inWO ? rand(650, 900) / 10 : rand(850, 1000) / 10;
            $scheduled  = 24;
            $downtime   = round($scheduled * (1 - $avail / 100), 2);
            DB::table('unit_availability_logs')->insertOrIgnore([
                'unit_id'              => $uid,
                'date'                 => $date->toDateString(),
                'scheduled_hours'      => $scheduled,
                'downtime_hours'       => $downtime,
                'available_hours'      => round($scheduled - $downtime, 2),
                'availability_percent' => $avail,
                'reference_type'       => null,
                'reference_id'         => null,
            ]);
        }
    }

    private function createP2hAndTimesheet(int $uid, int $opId, Carbon $date): void
    {
        $this->p2hSeq++;
        $p2hNo  = 'P2H-' . $date->format('Ymd') . '-' . str_pad($this->p2hSeq, 4, '0', STR_PAD_LEFT);
        $odo    = $this->units[$uid]['odo'];
        $shift  = 'day';

        // Delta km hari ini: 120-280 km untuk DT tambang
        $deltaKm = rand(120, 280);
        $kmStart = $odo;
        $kmEnd   = $odo + $deltaKm;

        // P2H items: engine, hydraulic, brake, tire, electrical, body
        $p2hItems = [
            ['engine','Kebocoran oli mesin'],
            ['engine','Kondisi fan belt'],
            ['engine','Level air radiator'],
            ['hydraulic','Tekanan hydraulik body'],
            ['hydraulic','Kebocoran hose hydraulik'],
            ['brake','Kondisi kampas rem'],
            ['brake','Tekanan angin rem'],
            ['tire','Kondisi ban (tekanan & kerusakan)'],
            ['tire','Baut roda lengkap & kencang'],
            ['electrical','Lampu kerja nyala semua'],
            ['electrical','Kondisi baterai'],
            ['body','Kaca cabin bersih'],
            ['safety','APAR tersedia & bertekanan'],
        ];

        // 90% layak, 8% layak_catatan, 2% tidak_layak
        $statuses = ['layak','layak','layak','layak','layak','layak','layak','layak','layak','layak_catatan','layak_catatan'];
        if ($date->day % 15 === 0) $statuses[] = 'tidak_layak';
        $status = $statuses[array_rand($statuses)];

        $p2hId = DB::table('p2h_checks')->insertGetId([
            'p2h_number'     => $p2hNo,
            'unit_id'        => $uid,
            'operator_id'    => $opId,
            'check_date'     => $date->toDateString(),
            'shift'          => $shift,
            'hour_meter_start'=> round($this->units[$uid]['odo'] / 20, 2),
            'km_start'       => $kmStart,
            'overall_status' => $status,
            'general_notes'  => $status !== 'layak' ? 'Perlu perhatian pada beberapa poin pemeriksaan.' : null,
            'reviewed_by'    => 1,
            'reviewed_at'    => $date->copy()->setTime(8, rand(0,30)),
            'created_at'     => $date->copy()->setTime(6, rand(0,30)),
            'updated_at'     => $date->copy()->setTime(8, rand(0,30)),
        ]);

        foreach ($p2hItems as [$cat,$item]) {
            $cond = rand(1, 10) <= 8 ? 'good' : (rand(1,2)===1 ? 'warning' : 'good');
            DB::table('p2h_check_items')->insert([
                'p2h_check_id' => $p2hId,
                'category'     => $cat,
                'check_item'   => $item,
                'condition'    => $cond,
                'notes'        => $cond === 'warning' ? 'Perlu pemantauan lebih lanjut.' : null,
            ]);
        }

        // Fit to Work
        $this->ftwSeq++;
        DB::table('fit_to_work_checks')->insert([
            'ftw_number'   => 'FTW-' . $date->format('Ymd') . '-' . str_pad($this->ftwSeq, 4, '0', STR_PAD_LEFT),
            'operator_id'  => $opId,
            'check_date'   => $date->toDateString(),
            'shift'        => $shift,
            'siap_bekerja' => 1,
            'kondisi_sehat'=> 1,
            'is_fit'       => 1,
            'checked_by'   => 1,
            'created_at'   => $date->copy()->setTime(5, 45),
            'updated_at'   => $date->copy()->setTime(5, 50),
        ]);

        // Timesheet
        $this->tsSeq++;
        $workHours = rand(80, 100) / 10; // 8.0–10.0 jam
        $retase    = rand(12, 25);
        DB::table('timesheets')->insert([
            'ts_number'       => 'TS-' . $date->format('Ymd') . '-' . str_pad($this->tsSeq, 4, '0', STR_PAD_LEFT),
            'p2h_check_id'    => $p2hId,
            'unit_id'         => $uid,
            'operator_id'     => $opId,
            'shift_date'      => $date->toDateString(),
            'shift'           => $shift,
            'hour_meter_start'=> round($kmStart / 20, 2),
            'hour_meter_end'  => round($kmEnd / 20, 2),
            'km_end'          => $kmEnd,
            'km_traveled'     => $deltaKm,
            'working_hours'   => $workHours,
            'retase'          => $retase,
            'submitted_by'    => 1,
            'created_at'      => $date->copy()->setTime(17, rand(0,59)),
            'updated_at'      => $date->copy()->setTime(17, rand(0,59)),
        ]);

        // Odometer reading
        DB::table('unit_odometer_readings')->insert([
            'unit_id'      => $uid,
            'odometer_km'  => $kmEnd,
            'delta_km'     => $deltaKm,
            'reading_date' => $date->toDateString(),
            'source'       => 'p2h',
            'recorded_by'  => 'Operator ' . $opId,
            'created_at'   => $date->copy()->setTime(17, rand(30,59)),
            'updated_at'   => $date->copy()->setTime(17, rand(30,59)),
        ]);

        // Update runtime odometer
        $this->units[$uid]['odo'] = $kmEnd;

        // Update tire total_km
        foreach ($this->unitTireIds[$uid] ?? [] as $pos => $tireId) {
            DB::table('unit_tires')->where('id', $tireId)->increment('total_km', $deltaKm);
            DB::table('unit_tire_history')->where('unit_tire_id', $tireId)->whereNull('removed_at')
                ->increment('km_used', $deltaKm);
        }

        // Accumulate monthly km
        $ym = $date->format('Y-m');
        $this->monthlyKm[$uid][$ym] = ($this->monthlyKm[$uid][$ym] ?? 0) + $deltaKm;
    }

    private function createWorkOrder(int $uid, Carbon $date): void
    {
        $this->woSeq++;
        $woNo   = 'WO-' . $date->format('Ym') . '-' . str_pad($this->woSeq, 4, '0', STR_PAD_LEFT);
        $techId = $this->techIds[array_rand($this->techIds)];

        $complaints = [
            [1, 'Rem terasa tidak pakem, jarak pengereman jauh',     'Penyetelan & penggantian kampas rem',     rand(1,2)*1_500_000, 0,             rand(200_000,500_000)],
            [2, 'Suhu mesin terus naik, indikator overheat menyala', 'Penggantian termostat dan flush radiator', rand(1,2)*500_000,  0,             rand(100_000,300_000)],
            [3, 'Hydraulik body angkat lemah, lambat naik',          'Penggantian seal kit silinder angkat',     rand(1,2)*2_000_000,0,             rand(200_000,400_000)],
            [4, 'Ban belakang kiri bocor di lokasi kerja',            'Tambal ban & pantau tekanan',              0,                  0,             rand(50_000, 150_000)],
            [5, 'Transmisi keras saat pindah gigi 3-4',              'Service transmisi & ganti oli transmisi',  rand(1,2)*1_200_000,0,             rand(300_000,600_000)],
            [6, 'Oli mesin merembes dari area timing cover',         'Penggantian gasket & seal timing',         rand(1,2)*800_000,  0,             rand(200_000,400_000)],
            [7, 'Lampu indikator baterai menyala, voltase drop',     'Penggantian alternator 24V',               0,                  4_500_000,     rand(200_000,400_000)],
            [8, 'Starter motor berputar lambat, mesin sulit hidup',  'Overhaul starter motor',                   0,                  rand(1,1)*3_200_000, rand(200_000,400_000)],
            [9, 'AC tidak dingin, kompresor bunyi kasar',            'Isi freon & ganti fan motor blower',       rand(1,2)*600_000,  0,             rand(100_000,200_000)],
            [10,'Filter solar tersumbat, mesin tersendat',           'Penggantian filter solar & priming',       0,                  0,             rand(50_000, 100_000)],
            [11,'Dua lampu HID kerja mati',                          'Penggantian lampu HID & cek kabel',        0,                  rand(1,2)*320_000, rand(50_000,100_000)],
            [13,'Kopling terasa slip saat beban berat di tanjakan',  'Penggantian kampas kopling',               rand(1,2)*2_500_000,0,             rand(300_000,500_000)],
            [14,'Setir terasa berat, power steering kurang responsif','Service power steering & cek tekanan',    rand(1,2)*800_000,  0,             rand(200_000,350_000)],
        ];

        $comp = $complaints[array_rand($complaints)];
        [$ctId, $complaint, $action, $laborCost, $vendorCost, $consumableCost] = $comp;

        // Status: WO baru (7 hari terakhir) ada yang masih open, lebih lama semua completed
        $cutoff7 = Carbon::today()->subDays(7);
        if ($date->lt($cutoff7)) {
            $status = 'completed';
        } else {
            $statuses = ['completed','completed','completed','in_progress','waiting_part','open'];
            $status = $statuses[array_rand($statuses)];
        }

        $startHour = rand(7, 15);
        $startTime = $date->copy()->setTime($startHour, rand(0,59));
        $endTime   = $status === 'completed'
            ? $startTime->copy()->addHours(rand(2,8))->addMinutes(rand(0,59))
            : null;
        $downtime  = $endTime ? round($startTime->diffInMinutes($endTime) / 60, 2) : round(rand(2,8), 2);

        $woId = DB::table('work_orders')->insertGetId([
            'wo_number'         => $woNo,
            'unit_id'           => $uid,
            'complaint'         => $complaint,
            'complaint_type_id' => $ctId,
            'maintenance_type'  => in_array($ctId, [2,5,10]) ? 'preventive' : 'corrective',
            'technician_id'     => $techId,
            'status'            => $status,
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'downtime_hours'    => $downtime,
            'labor_cost'        => $laborCost,
            'vendor_cost'       => $vendorCost,
            'consumable_cost'   => $consumableCost,
            'action_taken'      => $status === 'completed' ? $action : null,
            'created_by'        => 1,
            'created_at'        => $startTime,
            'updated_at'        => $endTime ?? $startTime,
        ]);

        if ($status === 'completed') {
            $spareCost = $this->createGoodsIssue($woId, $uid, $date, $ctId);
            $totalCost = $spareCost + $laborCost + $vendorCost + $consumableCost;

            DB::table('repair_cost_summaries')->insert([
                'work_order_id'   => $woId,
                'unit_id'         => $uid,
                'sparepart_cost'  => $spareCost,
                'labor_cost'      => $laborCost,
                'vendor_cost'     => $vendorCost,
                'consumable_cost' => $consumableCost,
                'total_cost'      => $totalCost,
                'created_at'      => $endTime,
                'updated_at'      => $endTime,
            ]);

            // Accumulate monthly cost
            $ym = $date->format('Y-m');
            $this->monthlyCost[$uid][$ym]    = ($this->monthlyCost[$uid][$ym] ?? 0) + $totalCost;
            $this->monthlyWoCount[$uid][$ym] = ($this->monthlyWoCount[$uid][$ym] ?? 0) + 1;
        }
    }

    private function createGoodsIssue(int $woId, int $uid, Carbon $date, int $ctId): float
    {
        $this->giSeq++;
        $giNo = 'GI-' . $date->format('Ym') . '-' . str_pad($this->giSeq, 4, '0', STR_PAD_LEFT);

        $giId = DB::table('goods_issues')->insertGetId([
            'gi_number'   => $giNo,
            'work_order_id'=> $woId,
            'issue_date'  => $date->toDateString(),
            'status'      => 'posted',
            'posted_by'   => 1,
            'posted_at'   => $date->copy()->setTime(rand(8,17), rand(0,59)),
            'created_at'  => $date,
            'updated_at'  => $date,
        ]);

        // Pilih sparepart berdasarkan complaint type
        $spareGroups = [
            1  => ['REM-KMP-001','REM-CYL-001'],
            2  => ['MES-THM-001','OLI-ME-20W50','FLT-OIL-001'],
            3  => ['HYD-SIL-001','OLI-HY-46'],
            4  => [],   // ban kempes - tidak perlu GI besar
            5  => ['OLI-TR-TF60'],
            6  => ['OLI-ME-20W50','FLT-OIL-001'],
            7  => ['ELE-ALT-001'],
            8  => ['ELE-STR-001'],
            9  => ['BOD-WIP-001'],
            10 => ['FLT-FUL-001'],
            11 => ['ELE-LAM-001'],
            13 => [],
            14 => ['HYD-HOS-001'],
        ];

        $partNos = $spareGroups[$ctId] ?? ['FLT-OIL-001'];
        if (empty($partNos)) $partNos = ['FLT-OIL-001'];

        // Tambah oli/filter random kadang
        if (rand(1,3) === 1) $partNos[] = 'OLI-ME-20W50';

        $total = 0;
        $balance = [];
        foreach (array_unique($partNos) as $pn) {
            if (!isset($this->sparepartMap[$pn])) continue;
            $sp    = $this->sparepartMap[$pn];
            $qty   = rand(1, 2);
            $price = $sp['price'];
            $line  = $qty * $price;
            $total += $line;

            // running balance (simple)
            $curStock = $this->sparepartMap[$pn]['stock'];
            $newStock = max(0, $curStock - $qty);
            $this->sparepartMap[$pn]['stock'] = $newStock;

            DB::table('goods_issue_items')->insert([
                'goods_issue_id'        => $giId,
                'sparepart_id'          => $sp['id'],
                'warehouse_location_id' => $this->warehouseId,
                'qty_issued'            => $qty,
                'unit_price'            => $price,
                'total_price'           => $line,
            ]);

            DB::table('stock_movements')->insert([
                'movement_date'         => $date->toDateString(),
                'sparepart_id'          => $sp['id'],
                'warehouse_location_id' => $this->warehouseId,
                'movement_type'         => 'out',
                'reference_type'        => 'goods_issue',
                'reference_id'          => $giId,
                'qty_in'                => 0,
                'qty_out'               => $qty,
                'balance_after'         => $newStock,
                'unit_price'            => $price,
                'created_at'            => $date,
            ]);
        }

        return (float) $total;
    }

    // ── MONTHLY COST FLUSH ────────────────────────────────────────────────────
    private function flushMonthlyCosts(): void
    {
        $allMonths = array_unique(array_merge(
            array_merge(...array_map('array_keys', $this->monthlyCost ?: [[]])),
            array_merge(...array_map('array_keys', $this->monthlyKm   ?: [[]])),
        ));

        foreach ($this->unitIds as $uid) {
            $budgetCost = $this->units[$uid]['budget_cost'];
            $budgetKm   = $this->units[$uid]['budget_km'];

            foreach ($allMonths as $ym) {
                $cost    = $this->monthlyCost[$uid][$ym]    ?? 0;
                $km      = $this->monthlyKm[$uid][$ym]      ?? 0;
                $woCount = $this->monthlyWoCount[$uid][$ym] ?? 0;

                $isOverCost = $budgetCost > 0 && $cost > $budgetCost;
                $isOverKm   = $budgetKm > 0   && $km   > $budgetKm;

                DB::table('unit_monthly_costs')->upsert([
                    'unit_id'           => $uid,
                    'year_month'        => $ym,
                    'total_cost'        => $cost,
                    'work_order_count'  => $woCount,
                    'is_over_budget'    => $isOverCost ? 1 : 0,
                    'exceeded_at'       => $isOverCost ? now() : null,
                    'total_km'          => $km,
                    'is_over_km_budget' => $isOverKm ? 1 : 0,
                    'km_exceeded_at'    => $isOverKm ? now() : null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ], ['unit_id','year_month'], [
                    'total_cost','work_order_count','is_over_budget','exceeded_at',
                    'total_km','is_over_km_budget','km_exceeded_at','updated_at',
                ]);

                // Create operator performance record if budget exceeded
                if ($isOverCost) {
                    // Get a random WO for this unit/month to reference
                    $refWo = DB::table('work_orders')
                        ->where('unit_id', $uid)
                        ->where('status', 'completed')
                        ->whereRaw("DATE_FORMAT(end_time,'%Y-%m') = ?", [$ym])
                        ->inRandomOrder()->first();

                    if ($refWo && !DB::table('operator_performance_records')
                        ->where('unit_id',$uid)->where('year_month',$ym)->exists()) {
                        $opId = $this->operatorIds[array_rand($this->operatorIds)];
                        DB::table('operator_performance_records')->insert([
                            'operator_id'              => $opId,
                            'unit_id'                  => $uid,
                            'work_order_id'            => $refWo->id,
                            'year_month'               => $ym,
                            'monthly_budget_limit'     => $budgetCost,
                            'total_cost_at_exceedance' => $cost,
                            'excess_amount'            => $cost - $budgetCost,
                            'recorded_at'              => now(),
                            'notes'                    => "Biaya perbaikan unit {$this->units[$uid]['code']} bulan $ym melebihi budget. Budget IDR " . number_format($budgetCost,0,',','.') . ", aktual IDR " . number_format($cost,0,',','.'),
                            'created_at'               => now(),
                            'updated_at'               => now(),
                        ]);
                    }
                }
            }
        }
    }

    // ── PROCUREMENT ───────────────────────────────────────────────────────────
    private function seedProcurement(): void
    {
        $now = now();

        // PR & PO untuk bulan Mei (pengadaan ban + oli awal bulan)
        $prItems = [
            ['BAN-BS-1222', 20],
            ['BAN-GT-1222', 16],
            ['OLI-ME-20W50', 10],
            ['OLI-TR-TF60',  5],
            ['FLT-OIL-001', 20],
            ['FLT-AIR-001', 10],
        ];

        $prDate = Carbon::create(2026, 5, 3);
        $this->prSeq++;
        $prNo = 'PR-202605-' . str_pad($this->prSeq, 4, '0', STR_PAD_LEFT);
        $prId = DB::table('purchase_requests')->insertGetId([
            'pr_number'       => $prNo,
            'request_date'    => $prDate->toDateString(),
            'request_by'      => 1,
            'remarks'         => 'Pengadaan ban dan pelumas bulan Mei 2026',
            'status'          => 'approved',
            'approved_by'     => 1,
            'approved_at'     => $prDate->copy()->addDay(),
            'estimated_total' => 0,
            'created_at'      => $prDate,
            'updated_at'      => $prDate->copy()->addDay(),
        ]);

        $estTotal = 0;
        foreach ($prItems as [$pn,$qty]) {
            $sp = $this->sparepartMap[$pn];
            $estTotal += $sp['price'] * $qty;
            DB::table('purchase_request_items')->insert([
                'purchase_request_id' => $prId,
                'sparepart_id'        => $sp['id'],
                'qty'                 => $qty,
            ]);
        }
        DB::table('purchase_requests')->where('id',$prId)->update(['estimated_total'=>$estTotal]);

        // PO
        $poDate = Carbon::create(2026, 5, 5);
        $this->poSeq++;
        $poNo = 'PO-202605-' . str_pad($this->poSeq, 4, '0', STR_PAD_LEFT);
        $suppId = $this->supplierIds[0]; // Bridgestone supplier
        $poId = DB::table('purchase_orders')->insertGetId([
            'po_number'           => $poNo,
            'purchase_request_id' => $prId,
            'supplier_id'         => $suppId,
            'po_date'             => $poDate->toDateString(),
            'expected_date'       => $poDate->copy()->addDays(7)->toDateString(),
            'status'              => 'completed',
            'created_at'          => $poDate,
            'updated_at'          => $poDate->copy()->addDays(8),
        ]);

        foreach ($prItems as [$pn,$qty]) {
            $sp    = $this->sparepartMap[$pn];
            $total = $sp['price'] * $qty;
            DB::table('purchase_order_items')->insert([
                'purchase_order_id' => $poId,
                'sparepart_id'      => $sp['id'],
                'qty'               => $qty,
                'unit_price'        => $sp['price'],
                'total_price'       => $total,
                'qty_received'      => $qty,
                'qty_outstanding'   => 0,
            ]);
        }

        // GR
        $grDate = Carbon::create(2026, 5, 12);
        $this->grSeq++;
        $grNo = 'GR-202605-' . str_pad($this->grSeq, 4, '0', STR_PAD_LEFT);
        $grId = DB::table('goods_receipts')->insertGetId([
            'gr_number'       => $grNo,
            'purchase_order_id'=> $poId,
            'receipt_date'    => $grDate->toDateString(),
            'status'          => 'posted',
            'posted_by'       => 1,
            'posted_at'       => $grDate->copy()->setTime(10,0),
            'created_at'      => $grDate,
            'updated_at'      => $grDate,
        ]);

        foreach ($prItems as [$pn,$qty]) {
            $sp = $this->sparepartMap[$pn];
            DB::table('goods_receipt_items')->insert([
                'goods_receipt_id'      => $grId,
                'sparepart_id'          => $sp['id'],
                'warehouse_location_id' => $this->warehouseId,
                'qty_received'          => $qty,
            ]);

            $newStock = ($this->sparepartMap[$pn]['stock'] ?? 0) + $qty;
            $this->sparepartMap[$pn]['stock'] = $newStock;

            DB::table('stock_movements')->insert([
                'movement_date'         => $grDate->toDateString(),
                'sparepart_id'          => $sp['id'],
                'warehouse_location_id' => $this->warehouseId,
                'movement_type'         => 'in',
                'reference_type'        => 'goods_receipt',
                'reference_id'          => $grId,
                'qty_in'                => $qty,
                'qty_out'               => 0,
                'balance_after'         => $newStock,
                'unit_price'            => $sp['price'],
                'created_at'            => $grDate,
            ]);
        }

        // PR bulan Juni
        $prDate2 = Carbon::create(2026, 6, 2);
        $this->prSeq++;
        $prNo2 = 'PR-202606-' . str_pad($this->prSeq, 4, '0', STR_PAD_LEFT);
        $prItems2 = [
            ['BAN-DL-1122', 12],
            ['REM-KMP-001', 8],
            ['HYD-SIL-001', 6],
            ['ELE-LAM-001', 10],
            ['OLI-HY-46', 6],
        ];
        $prId2 = DB::table('purchase_requests')->insertGetId([
            'pr_number'       => $prNo2,
            'request_date'    => $prDate2->toDateString(),
            'request_by'      => 1,
            'remarks'         => 'Pengadaan sparepart & ban Juni 2026',
            'status'          => 'submitted',
            'estimated_total' => array_sum(array_map(fn($x)=>$this->sparepartMap[$x[0]]['price']*$x[1], $prItems2)),
            'created_at'      => $prDate2,
            'updated_at'      => $prDate2,
        ]);
        foreach ($prItems2 as [$pn,$qty]) {
            DB::table('purchase_request_items')->insert([
                'purchase_request_id' => $prId2,
                'sparepart_id'        => $this->sparepartMap[$pn]['id'],
                'qty'                 => $qty,
            ]);
        }
    }

    // ── UPDATE FINAL ODOMETERS ─────────────────────────────────────────────────
    private function updateUnitOdometers(): void
    {
        foreach ($this->unitIds as $uid) {
            DB::table('units')->where('id', $uid)->update([
                'current_odometer' => $this->units[$uid]['odo'],
                'hour_meter'       => round($this->units[$uid]['odo'] / 20, 2),
                'updated_at'       => now(),
            ]);
        }

        // Update sparepart stock_on_hand dari running balance
        foreach ($this->sparepartMap as $pn => $sp) {
            DB::table('spareparts')->where('id', $sp['id'])->update([
                'stock_on_hand' => max(0, $sp['stock']),
            ]);
        }
    }

    // ── SUMMARY ───────────────────────────────────────────────────────────────
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('✅ Seeding selesai!');
        $this->command->info('   Units        : ' . DB::table('units')->count());
        $this->command->info('   Operators    : ' . DB::table('operators')->count());
        $this->command->info('   Technicians  : ' . DB::table('technicians')->count());
        $this->command->info('   Spareparts   : ' . DB::table('spareparts')->count());
        $this->command->info('   P2H Checks   : ' . DB::table('p2h_checks')->count());
        $this->command->info('   Timesheets   : ' . DB::table('timesheets')->count());
        $this->command->info('   Work Orders  : ' . DB::table('work_orders')->count());
        $this->command->info('   Goods Issues : ' . DB::table('goods_issues')->count());
        $this->command->info('   Odometer Log : ' . DB::table('unit_odometer_readings')->count());
        $this->command->info('   Monthly Costs: ' . DB::table('unit_monthly_costs')->count());
        $this->command->info('   Unit Tires   : ' . DB::table('unit_tires')->count());
        $this->command->info('   Over Budget  : ' . DB::table('unit_monthly_costs')->where('is_over_budget',1)->count() . ' record(s)');
        $this->command->info('   Over KM Bdgt : ' . DB::table('unit_monthly_costs')->where('is_over_km_budget',1)->count() . ' record(s)');
    }

    private function weightedRandom(array $items, array $weights): mixed
    {
        $total = array_sum($weights);
        $rand  = rand(1, $total);
        $cum   = 0;
        foreach ($items as $i => $item) {
            $cum += $weights[$i];
            if ($rand <= $cum) return $item;
        }
        return $items[array_key_last($items)];
    }
}
