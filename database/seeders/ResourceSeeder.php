<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Budget;
use App\Models\Resource;
use App\Models\User;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first() ?? User::first();
        $currentYear = date('Y');

        // Create a budget for the current year
        $budget = Budget::create([
            'year' => $currentYear,
            'monthly_budget' => 5000.00,
            'annual_budget' => 60000.00,
            'created_by' => $admin->id,
        ]);

        // Create some sample resources (expenses/invoices)
        $resources = [
            [
                'title' => 'Office Rent - January',
                'amount' => 2000.00,
                'type' => 'expense',
                'month' => 1,
                'year' => $currentYear,
                'description' => 'Monthly office rent payment.',
            ],
            [
                'title' => 'Software Licenses',
                'amount' => 500.00,
                'type' => 'expense',
                'month' => 1,
                'year' => $currentYear,
                'description' => 'Annual subscriptions for Adobe Creative Cloud.',
            ],
            [
                'title' => 'Consulting Invoice #1023',
                'amount' => 1200.00,
                'type' => 'invoice',
                'month' => 2, // February
                'year' => $currentYear,
                'description' => 'IT Security consultation.',
            ],
            [
                'title' => 'Server Maintenance',
                'amount' => 300.00,
                'type' => 'expense',
                'month' => 2,
                'year' => $currentYear,
                'description' => 'Routine server checkup and patching.',
            ],
             [
                'title' => 'New Hardware Purchase (Laptops)',
                'amount' => 4500.00,
                'type' => 'expense',
                'month' => 3, // March
                'year' => $currentYear,
                'description' => '3x MacBook Pro for Dev Team.',
            ],
        ];

        foreach ($resources as $resource) {
            Resource::create([
                ...$resource,
                'created_by' => $admin->id,
            ]);
        }
    }
}
