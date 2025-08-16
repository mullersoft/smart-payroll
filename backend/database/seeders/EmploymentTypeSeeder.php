<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'permanent', 'description' => 'Permanent employment'],
            ['name' => 'contract', 'description' => 'Contract employment'],
        ];

        foreach ($types as $type) {
            EmploymentType::create($type);
        }
    }
}
