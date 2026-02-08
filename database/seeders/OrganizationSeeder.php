<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hq = \App\Models\Location::create([
            'name' => 'Headquarters',
            'address' => '123 Tech Park',
            'description' => 'Main Office'
        ]);

        $branch = \App\Models\Location::create([
            'name' => 'Branch Office',
            'address' => '456 Downtown',
            'description' => 'Sales Office'
        ]);

        \App\Models\Department::create(['name' => 'IT', 'location_id' => $hq->id]);
        \App\Models\Department::create(['name' => 'HR', 'location_id' => $hq->id]);
        \App\Models\Department::create(['name' => 'Sales', 'location_id' => $branch->id]);
    }
}
