<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Pro', 'urls_limit' => 1000],
            ['name' => 'Premium', 'urls_limit' => -1], // Unlimited quota, use -1 or any other convention to represent unlimited
        ];

        // Insert plans into the database
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
