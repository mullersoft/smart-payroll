<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'CEO', 'description' => 'Chief Executive Officer'],
            ['name' => 'COO', 'description' => 'Chief Operating Officer'],
            ['name' => 'CTO', 'description' => 'Chief Technology Officer'],
            ['name' => 'CISO', 'description' => 'Chief Information Security Officer'],
            ['name' => 'Director', 'description' => 'Department Director'],
            ['name' => 'Dept Lead', 'description' => 'Department Lead'],
            ['name' => 'Normal Employee', 'description' => 'Regular employee'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
