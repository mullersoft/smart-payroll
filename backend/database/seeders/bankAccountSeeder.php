<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear the table first
        DB::table('bank_accounts')->truncate();

        $accounts = [
            [
                'id' => 2,
                'account_number' => 'QM-COMPANY-001',
                'owner_name' => 'Qelemeda Technology',
                'balance' => 100000,
                'bank_name' => 'Zemen Bank',

            ],
            [
                'id' => 3,
                'account_number' => 'TAX-AUTH-001',
                'owner_name' => 'addis ababa revenu',
                'balance' => 0,
                'bank_name' => 'CBE',

            ],
            [
                'id' => 4,
                'employee_id' => null,
                'account_number' => 'PENSION-AUTH-001',
                'owner_name' => 'addis ababa pension',
                'balance' => 0,
                'bank_name' => 'Amhara bank',

            ],
            [
                'id' => 5,
                'employee_id' => 9,
                'account_number' => '100000264336108',
                'owner_name' => 'Kasawu Mengesha',
                'balance' => 0,
                'bank_name' => 'Hijira Bank',

            ],


            [
                'id' => 8,
                'employee_id' => 10,
                'account_number' => '100000264336109',
                'owner_name' => 'Abebe Kasegn',
                'balance' => 0,
                'bank_name' => 'Hijira Bank',
            ],
            [
                'id' => 9,
                'employee_id' => 11,
                'account_number' => '100000264336110',
                'owner_name' => 'finote Adefiris',
                'balance' => 0,
                'bank_name' => 'Hijira Bank',
            ],
            [
                'id' => 10,
                'employee_id' => 12,
                'account_number' => '100000264336111',
                'owner_name' => 'Alemitu Gemeda',
                'balance' => 0,
                'bank_name' => 'Hijira Bank',
            ],
            [
                'id' => 11,
                'employee_id' => 13,
                'account_number' => '100000264336112',
                'owner_name' => 'Haftom gebiru',
                'balance' => 0,
                'bank_name' => 'Hijira Bank',
            ],


        ];

        // Insert the data
        foreach ($accounts as $account) {
            BankAccount::create($account);
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
