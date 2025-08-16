<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'full_name' => 'Kasawu Mengesha',
                'employee_id' => 'emp1',
                'position_id' => 2,
                'employment_type_id' => 1,
                'base_salary' => 35000,
                'gender' => 'Male',
                'employment_date' => '03-03-2024',
            ],

        ];

        foreach ($types as $type) {
            Employee::create($type);
        }
    }
}
