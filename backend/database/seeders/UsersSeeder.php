<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Clear old users without breaking foreign key constraints
        DB::table('users')->delete();
// php artisan db:seed --class=UsersSeeder

        $types = [
            [
                'name' => 'Mulugeta Linger',
                'email' => 'mulerselinger@gmail.com',
                'password' => bcrypt('123456'),
                'role' => 'admin',
                'status' => 'active',
            ],
             [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('123456'),
                'role' => 'admin',
                'status' => 'active',
            ],
             [
                'name' => 'preparer',
                'email' => 'preparer@gmail.com',
                'password' => bcrypt('123456'),
                'role' => 'preparer',
                'status' => 'active',
            ],
             [
                'name' => 'approver',
                'email' => 'approver@gmail.com',
                'password' => bcrypt('123456'),
                'role' => 'approver',
                'status' => 'active',
            ],
             [
                'name' => 'employee',
                'email' => 'employee@gmail.com',
                'password' => bcrypt('123456'),
                'status' => 'active',
            ],
        ];

        foreach ($types as $type) {
            User::create($type);
        }
    }
}
