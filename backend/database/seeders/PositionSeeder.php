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
            ['name' => 'CEO', 'description' => 'Chief Executive Officer', 'allowance' => 0],
            ['name' => 'COO', 'description' => 'Chief Operating Officer', 'allowance' => 2015.38],
            ['name' => 'CTO', 'description' => 'Chief Technology Officer', 'allowance' => 2615.38],
            ['name' => 'CISO', 'description' => 'Chief Information Security Officer', 'allowance' => 1574.14],
            ['name' => 'Director', 'description' => 'Department Director', 'allowance' => 982.76],
            ['name' => 'Dept Lead', 'description' => 'Department Lead', 'allowance' => 982.76],
            ['name' => 'Normal Employee', 'description' => 'Regular employee', 'allowance' => 982.76],
            // ['name' => 'Intern', 'description' => 'Intern', 'allowance' => 0],
            // ['name' => 'Contractor', 'description' => 'Contractor', 'allowance' => 0],
            // ['name' => 'Freelancer', 'description' => 'Freelancer', 'allowance' => 0],
            // ['name' => 'Part-time', 'description' => 'Part-time employee', 'allowance' => 0],
            // ['name' => 'Consultant', 'description' => 'Consultant', 'allowance' => 0],
            // ['name' => 'Advisor', 'description' => 'Advisor', 'allowance' => 0],

        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position['name']], // Search condition
                $position // Data to update/create
            );
        }
    }
}
