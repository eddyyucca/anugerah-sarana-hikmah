<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaints = [
            ['name' => 'TRANSMISSION', 'color' => '#FF6B6B', 'order' => 1],
            ['name' => 'ENGINE', 'color' => '#4ECDC4', 'order' => 2],
            ['name' => 'MAINFRAME & WORK EQUIPMENT', 'color' => '#45B7D1', 'order' => 3],
            ['name' => 'ELECTRIC SYSTEM & AC', 'color' => '#FFA07A', 'order' => 4],
            ['name' => 'DIFFERENTIAL & AXLE', 'color' => '#98D8C8', 'order' => 5],
            ['name' => 'TYRE & SPRING', 'color' => '#F7DC6F', 'order' => 6],
            ['name' => 'HYDRAULIC SYSTEM', 'color' => '#BB8FCE', 'order' => 7],
            ['name' => 'FINAL DRIVE', 'color' => '#85C1E2', 'order' => 8],
            ['name' => 'UNDERCARRIAGE', 'color' => '#F8B88B', 'order' => 9],
            ['name' => 'MAINTENANCE', 'color' => '#A9DFBF', 'order' => 10],
            ['name' => 'CONTROL SYSTEM', 'color' => '#F1948A', 'order' => 11],
            ['name' => 'BRAKE SYSTEM', 'color' => '#D7BDE2', 'order' => 12],
            ['name' => 'COOLING SYSTEM', 'color' => '#85C1E9', 'order' => 13],
            ['name' => 'FUEL SYSTEM', 'color' => '#F8B88B', 'order' => 14],
            ['name' => 'STEERING SYSTEM', 'color' => '#ABEBC6', 'order' => 15],
        ];

        foreach ($complaints as $complaint) {
            \App\Models\ComplaintType::create(array_merge($complaint, ['is_active' => true]));
        }
    }
}
