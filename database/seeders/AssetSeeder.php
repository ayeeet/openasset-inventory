<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Asset::create([
            'name' => 'MacBook Pro 16"',
            'serial_number' => 'MBP-2026-001',
            'category' => 'Laptops',
            'status' => 'active',
            'purchase_date' => now()->subMonths(6),
        ]);

        \App\Models\Asset::create([
            'name' => 'Dell XPS 15',
            'serial_number' => 'DXPS-2026-005',
            'category' => 'Laptops',
            'status' => 'active',
            'purchase_date' => now()->subMonths(2),
        ]);

        \App\Models\Asset::create([
            'name' => 'Dell Ultrasharp 27"',
            'serial_number' => 'DELL-2026-002',
            'category' => 'Monitors',
            'status' => 'active',
            'purchase_date' => now()->subMonths(12),
        ]);

        \App\Models\Asset::create([
            'name' => 'Office 365 License',
            'category' => 'Software Licenses',
            'status' => 'active',
        ]);

        \App\Models\Asset::create([
            'name' => 'iPhone 15 Pro',
            'serial_number' => 'IPH-15-999',
            'category' => 'Mobile Phones',
            'status' => 'active',
            'purchase_date' => now()->subMonths(1),
        ]);
        
        \App\Models\Asset::create([
            'name' => 'Samsung S24 Ultra',
            'serial_number' => 'SAM-S24-888',
            'category' => 'Mobile Phones',
            'status' => 'active',
            'purchase_date' => now()->subMonths(1),
        ]);
    }
}
