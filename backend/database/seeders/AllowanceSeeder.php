<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allowance;

class AllowanceSeeder extends Seeder
{
    public function run()
    {


        // Housing allowance (fixed, taxable)
        Allowance::create([
            'name' => 'Housing Allowance',
            'description' => 'Monthly allowance for housing costs',
            'type' => 'fixed',
            'value' => 3000,
            'is_taxable' => true,
        ]);

        // Hardship allowance
        Allowance::create([
            'name' => 'Hardship Allowance',
            'description' => 'Allowance for inconvenience like unexpected transfer',
            'type' => 'fixed',
            'value' => 1500,
            'is_taxable' => true,
        ]);

        // Desert allowance
        Allowance::create([
            'name' => 'Desert Allowance',
            'description' => 'Allowance for assignment in a hot region',
            'type' => 'fixed',
            'value' => 2000,
            'is_taxable' => true,
        ]);

        // Transport (fuel) allowance
        Allowance::create([
            'name' => 'Transportation Allowance',
            'description' => 'Fuel/transportation costs to workplace',
            'type' => 'fixed',
            'value' => 2200,
            'is_taxable' => false,
        ]);
    }
}
