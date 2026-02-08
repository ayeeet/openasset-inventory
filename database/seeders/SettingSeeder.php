<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'OpenAsset', 'type' => 'string'],
            ['key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'string'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
