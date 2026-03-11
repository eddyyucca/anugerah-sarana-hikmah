<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        DB::table('users')->insert([
            ['name' => 'Admin Workshop', 'email' => 'admin@workshop.local', 'password' => Hash::make('password'), 'role' => 'admin', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager Site', 'email' => 'manager@workshop.local', 'password' => Hash::make('password'), 'role' => 'manager', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Staff Warehouse', 'email' => 'staff@workshop.local', 'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Unit Categories
        DB::table('unit_categories')->insert([
            ['name' => 'Excavator', 'description' => 'Hydraulic excavator units', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dump Truck', 'description' => 'Off-highway dump trucks', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bulldozer', 'description' => 'Crawler dozers', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grader', 'description' => 'Motor graders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loader', 'description' => 'Wheel loaders', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sparepart Categories
        DB::table('sparepart_categories')->insert([
            ['name' => 'Engine', 'description' => 'Engine components', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hydraulic', 'description' => 'Hydraulic system parts', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Undercarriage', 'description' => 'Track and undercarriage', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Electrical', 'description' => 'Electrical components', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Filter', 'description' => 'Oil, air, fuel filters', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Warehouse Locations
        DB::table('warehouse_locations')->insert([
            ['code' => 'WH-A1', 'name' => 'Warehouse A - Rack 1', 'description' => 'Main warehouse', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'WH-A2', 'name' => 'Warehouse A - Rack 2', 'description' => 'Main warehouse', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'WH-B1', 'name' => 'Warehouse B - Rack 1', 'description' => 'Secondary warehouse', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Units
        $units = [
            ['unit_code' => 'EX-001', 'unit_model' => 'Komatsu PC200-8', 'unit_type' => 'Excavator', 'category_id' => 1, 'department' => 'Mining Pit 1', 'current_status' => 'available', 'hour_meter' => 12500.5],
            ['unit_code' => 'EX-002', 'unit_model' => 'CAT 320D', 'unit_type' => 'Excavator', 'category_id' => 1, 'department' => 'Mining Pit 2', 'current_status' => 'available', 'hour_meter' => 8700.0],
            ['unit_code' => 'DT-001', 'unit_model' => 'Komatsu HD785-7', 'unit_type' => 'Dump Truck', 'category_id' => 2, 'department' => 'Hauling', 'current_status' => 'under_repair', 'hour_meter' => 15200.3],
            ['unit_code' => 'DT-002', 'unit_model' => 'CAT 773F', 'unit_type' => 'Dump Truck', 'category_id' => 2, 'department' => 'Hauling', 'current_status' => 'available', 'hour_meter' => 11300.0],
            ['unit_code' => 'DT-003', 'unit_model' => 'Komatsu HD785-7', 'unit_type' => 'Dump Truck', 'category_id' => 2, 'department' => 'Hauling', 'current_status' => 'standby', 'hour_meter' => 9800.5],
            ['unit_code' => 'BD-001', 'unit_model' => 'Komatsu D155A', 'unit_type' => 'Bulldozer', 'category_id' => 3, 'department' => 'Mining Pit 1', 'current_status' => 'available', 'hour_meter' => 7650.0],
            ['unit_code' => 'GR-001', 'unit_model' => 'CAT 14M', 'unit_type' => 'Grader', 'category_id' => 4, 'department' => 'Road Maintenance', 'current_status' => 'available', 'hour_meter' => 5400.0],
            ['unit_code' => 'LD-001', 'unit_model' => 'Komatsu WA500', 'unit_type' => 'Loader', 'category_id' => 5, 'department' => 'Crushing Plant', 'current_status' => 'available', 'hour_meter' => 6200.0],
        ];
        foreach ($units as $u) {
            DB::table('units')->insert(array_merge($u, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }

        // Spareparts
        $parts = [
            ['part_number' => 'FLT-OIL-001', 'part_name' => 'Oil Filter Element', 'category_id' => 5, 'unit_price' => 350000, 'minimum_stock' => 20, 'stock_on_hand' => 45, 'uom' => 'PCS'],
            ['part_number' => 'FLT-AIR-001', 'part_name' => 'Air Filter Primary', 'category_id' => 5, 'unit_price' => 850000, 'minimum_stock' => 10, 'stock_on_hand' => 15, 'uom' => 'PCS'],
            ['part_number' => 'FLT-FUEL-001', 'part_name' => 'Fuel Filter Water Separator', 'category_id' => 5, 'unit_price' => 450000, 'minimum_stock' => 15, 'stock_on_hand' => 8, 'uom' => 'PCS'],
            ['part_number' => 'HYD-PUMP-001', 'part_name' => 'Hydraulic Main Pump', 'category_id' => 2, 'unit_price' => 85000000, 'minimum_stock' => 2, 'stock_on_hand' => 3, 'uom' => 'PCS'],
            ['part_number' => 'HYD-HOSE-001', 'part_name' => 'Hydraulic Hose 1"', 'category_id' => 2, 'unit_price' => 1200000, 'minimum_stock' => 10, 'stock_on_hand' => 25, 'uom' => 'MTR'],
            ['part_number' => 'ENG-BELT-001', 'part_name' => 'V-Belt Fan Drive', 'category_id' => 1, 'unit_price' => 650000, 'minimum_stock' => 5, 'stock_on_hand' => 12, 'uom' => 'PCS'],
            ['part_number' => 'UC-SHOE-001', 'part_name' => 'Track Shoe Assembly', 'category_id' => 3, 'unit_price' => 2500000, 'minimum_stock' => 20, 'stock_on_hand' => 40, 'uom' => 'PCS'],
            ['part_number' => 'ELC-BATT-001', 'part_name' => 'Battery 12V 150Ah', 'category_id' => 4, 'unit_price' => 3500000, 'minimum_stock' => 4, 'stock_on_hand' => 6, 'uom' => 'PCS'],
            ['part_number' => 'ENG-OIL-001', 'part_name' => 'Engine Oil 15W-40', 'category_id' => 1, 'unit_price' => 85000, 'minimum_stock' => 100, 'stock_on_hand' => 200, 'uom' => 'LTR'],
            ['part_number' => 'HYD-OIL-001', 'part_name' => 'Hydraulic Oil ISO 46', 'category_id' => 2, 'unit_price' => 95000, 'minimum_stock' => 100, 'stock_on_hand' => 150, 'uom' => 'LTR'],
        ];
        foreach ($parts as $p) {
            DB::table('spareparts')->insert(array_merge($p, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }

        // Suppliers
        DB::table('suppliers')->insert([
            ['supplier_code' => 'SUP-001', 'supplier_name' => 'PT Komatsu Indonesia', 'contact_person' => 'Budi Santoso', 'phone' => '021-5551234', 'email' => 'sales@komatsu.co.id', 'address' => 'Jakarta', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['supplier_code' => 'SUP-002', 'supplier_name' => 'PT Trakindo Utama', 'contact_person' => 'Ahmad Fauzi', 'phone' => '021-5559876', 'email' => 'parts@trakindo.co.id', 'address' => 'Jakarta', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['supplier_code' => 'SUP-003', 'supplier_name' => 'CV Mitra Bearing', 'contact_person' => 'Siti Rahayu', 'phone' => '0511-3345678', 'email' => 'order@mitrabearing.com', 'address' => 'Banjarmasin', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Technicians
        DB::table('technicians')->insert([
            ['technician_code' => 'TCH-001', 'technician_name' => 'Agus Setiawan', 'skill' => 'Engine Specialist', 'phone' => '0812-1111-2222', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['technician_code' => 'TCH-002', 'technician_name' => 'Dedi Kurniawan', 'skill' => 'Hydraulic Specialist', 'phone' => '0813-3333-4444', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['technician_code' => 'TCH-003', 'technician_name' => 'Rudi Hartono', 'skill' => 'Electrical Specialist', 'phone' => '0815-5555-6666', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['technician_code' => 'TCH-004', 'technician_name' => 'Wahyu Pratama', 'skill' => 'Undercarriage Specialist', 'phone' => '0816-7777-8888', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
