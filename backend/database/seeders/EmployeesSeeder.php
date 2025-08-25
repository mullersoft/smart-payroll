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
                'employment_date' => '2025-08-16',
            ],
            [
                'full_name' => 'Abebe Kasegn',
                'employee_id' => 'emp2',
                'position_id' => 3,
                'employment_type_id' => 1,
                'base_salary' => 25000,
                'gender' => 'Male',
                'employment_date' => '2025-08-16',
            ],
            [
                'full_name' => 'finote Adefiris',
                'employee_id' => 'emp3',
                'position_id' => 4,
                'employment_type_id' => 1,
                'base_salary' => 35000,
                'gender' => 'Male',
                'employment_date' => '2025-08-16',

            ],
            [
                'full_name' => 'Alemitu Gemeda',
                'employee_id' => 'emp4',
                'position_id' => 1,
                'employment_type_id' => 1,
                'base_salary' => 50000,
                'gender' => 'Female',
                'employment_date' => '2025-08-16',

            ],
            [
                'full_name' => 'Haftom gebiru',
                'employee_id' => 'emp5',
                'position_id' => 6,
                'employment_type_id' => 1,
                'base_salary' => 23310.50,
                'gender' => 'Male',
                'employment_date' => '2025-08-16',


            ],

        ];

        foreach ($types as $type) {
            Employee::create($type);
        }
    }
}
