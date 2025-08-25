<?php

namespace Database\Seeders;

use App\Models\Position;
use FontLib\Table\Type\name;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'CEO', 'description' => 'Chief Executive Officer', 'allowance' => 0], // 10% of base salary, non-taxable
            ['name' => 'COO', 'description' => 'Chief Operating Officer', 'allowance' => 2015.38],
            ['name' => 'CTO', 'description' => 'Chief Technology Officer', 'allowance' => 2615.38],
            ['name' => 'CISO', 'description' => 'Chief Information Security Officer', 'allowance' => 1574.14],
            ['name' => 'Director', 'description' => 'Department Director', 'allowance' => 982.76],
            ['name' => 'Dept Lead', 'description' => 'Department Lead', 'allowance' => 982.76],
            ['name' => 'Normal Employee', 'description' => 'Regular employee', 'allowance' => 982.76],


        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position['name']], // Search condition
                $position // Data to update/create
            );
        }
    }
}
