<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankService
{
    public function processPayrollTransaction(Payroll $payroll)
    {
        // Check approval status
        if ($payroll->status !== 'approved') {
            throw new \Exception('Payroll must be approved before processing.');
        }

        // Prevent duplicate processing
        if ($payroll->is_processed) {
            throw new \Exception('Payroll has already been processed.');
        }

        try {
            DB::transaction(function () use ($payroll) {
                // Load related employee and their bank account
                $payroll->load(['employee.bankAccount']);
                $employee = $payroll->employee;

                if (!$employee || !$employee->bankAccount) {
                    throw new \Exception('Employee does not have a bank account.');
                }

                $employeeBank = $employee->bankAccount;

                // Get account numbers from .env
                // $companyAccNo = env('COMPANY_ACCOUNT', 'QM-COMPANY-001');
                // $taxAccNo = env('TAX_ACCOUNT', 'TAX-AUTH-001');
                $companyAccNo = env('COMPANY_ACCOUNT');
                $taxAccNo = env('TAX_ACCOUNT');


                // Lock company and tax accounts for update
                $company = BankAccount::where('account_number', $companyAccNo)->lockForUpdate()->firstOrFail();
                $tax = BankAccount::where('account_number', $taxAccNo)->lockForUpdate()->firstOrFail();

                $total = $payroll->net_payment + $payroll->income_tax;

                if ($company->balance < $total) {
                    throw new \Exception('Company account has insufficient balance.');
                }

                // ➕ Transfer to employee
                $company->balance -= $payroll->net_payment;
                $employeeBank->balance += $payroll->net_payment;

                // ➕ Transfer tax
                $company->balance -= $payroll->income_tax;
                $tax->balance += $payroll->income_tax;

                // Save updated balances
                $company->save();
                $employeeBank->save();
                $tax->save();

                // ➕ Log transactions (status completed after successful balance updates)
                Transaction::create([
                    'payroll_id' => $payroll->id,
                    'transaction_type' => 'salary',
                    'amount' => $payroll->net_payment,
                    'from_account' => $company->account_number,
                    'to_account' => $employeeBank->account_number,
                    'transaction_date' => Carbon::now(),
                    'processed_by' => auth()->user()->name ?? 'System',
                    'status' => 'completed',
                ]);

                Transaction::create([
                    'payroll_id' => $payroll->id,
                    'transaction_type' => 'tax',
                    'amount' => $payroll->income_tax,
                    'from_account' => $company->account_number,
                    'to_account' => $tax->account_number,
                    'transaction_date' => Carbon::now(),
                    'processed_by' => auth()->user()->name ?? 'System',
                    'status' => 'completed',
                ]);

                // ✅ Mark as processed
                $payroll->is_processed = true;
                $payroll->save();
            });
        } catch (\Exception $e) {
            // Record failed attempt
            Transaction::create([
                'payroll_id' => $payroll->id,
                'transaction_type' => 'payroll_processing',
                'amount' => $payroll->net_payment + $payroll->income_tax,
                'from_account' => env('COMPANY_ACCOUNT', 'N/A'),
                'to_account' => 'N/A',
                'transaction_date' => Carbon::now(),
                'processed_by' => auth()->user()->name ?? 'System',
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            // Re-throw the exception so the controller can handle it
            throw $e;
        }
    }
}
