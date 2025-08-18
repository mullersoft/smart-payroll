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
            ['name' => 'internship', 'description' => 'Internship employment'],
            ['name' => 'freelance', 'description' => 'Freelance employment'],
            ['name' => 'part-time', 'description' => 'Part-time employment'],
            ['name' => 'consultancy', 'description' => 'Consultancy employment'],
        ];

        foreach ($types as $type) {
            EmploymentType::updateOrCreate($type);
        }
    }
}
