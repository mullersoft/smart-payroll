<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Position;
use App\Models\EmploymentType;
use App\Models\Allowance;

class PayrollService
{
    public function generatePayroll(array $data)
    {
        $employee = Employee::with(['position', 'employmentType'])->findOrFail($data['employee_id']);
        $base = $employee->base_salary;
        $working_days = $data['working_days'];
        $earned_salary = ($base / 30) * $working_days;

        // ---- Position Allowance ----
        $position_allowance_taxable = $employee->position->allowance ?? 0;
        $position_allowance_non_tax = 0;

        // Example: CEO has non-taxable 10% bonus
        if ($employee->position->name === 'CEO') {
            $position_allowance_non_tax = 0.10 * $base;
        }

        // ---- Transport Allowance ----
        // $transport_allowance = ($employee->position->name === 'Normal Employee') ? 600 : 2200;
        // ---- Transport Allowance ----
        if ($employee->position && strtolower($employee->position->name) === 'normal employee') {
            $transport_allowance = 600;
        } else {
            $transport_allowance = 2200;
        }


        // ---- Other commissions ----
        $other = $data['other_commission'] ?? 0;


        // Gross Pay
        $gross = $earned_salary + $position_allowance_taxable + $position_allowance_non_tax + $transport_allowance + $other;

        // Taxable Income
        $taxable_income = $earned_salary + $other + $position_allowance_taxable;

        // Income Tax
        $income_tax = $this->calculateIncomeTax($taxable_income);

        // ---- Pension (only for permanent employees) ----
        $isPermanent = strtolower($employee->employmentType->name) === 'permanent';
        $employee_pension = $isPermanent ? 0.07 * $earned_salary : 0;
        $employer_pension = $isPermanent ? 0.11 * $earned_salary : 0;

        $pension_contribution = $isPermanent ? $employee_pension + $employer_pension : 0;
        // Total deduction
        $total_deduction = $income_tax + $employee_pension;

        // Net Pay
        $net_payment = $gross - $total_deduction;

        return Payroll::create([
            'employee_id'                   => $employee->id,
            'pay_month'                     => $data['pay_month'],
            'working_days'                  => $working_days,
            'base_salary'                   => $base,
            'earned_salary'                 => $earned_salary,
            'position_allowance_taxable'    => $position_allowance_taxable,
            'position_allowance_non_tax'    => $position_allowance_non_tax,
            'transport_allowance'           => $transport_allowance,
            'other_commission'              => $other,
            'gross_pay'                     => $gross,
            'taxable_income'                => $taxable_income,
            'income_tax'                    => $income_tax,
            'employee_pension'              => $employee_pension,
            'employer_pension'              => $employer_pension,
            'pension_contribution' => $pension_contribution,
            'total_deduction'               => $total_deduction,
            'net_payment'                   => $net_payment,
            'status'                        => 'prepared',
            'prepared_by'                   => $data['prepared_by'],
            'approved_by'                   => null,
        ]);
    }


    private function calculateIncomeTax($income)
    {
        if ($income <= 600) {
            return 0;
        } elseif ($income <= 1650) {
            return ($income * 0.10) - 60;
        } elseif ($income <= 3200) {
            return ($income * 0.15) - 142.5;
        } elseif ($income <= 5250) {
            return ($income * 0.20) - 302.5;
        } elseif ($income <= 7800) {
            return ($income * 0.25) - 565;
        } elseif ($income <= 10900) {
            return ($income * 0.30) - 955;
        } else {
            return ($income * 0.35) - 1500;
        }
    }
    public function regeneratePayroll(Payroll $payroll)
    {
        $employee = $payroll->employee()->with(['position', 'employmentType'])->first();

        $base = $employee->base_salary;
        $working_days = $payroll->working_days;
        $earned_salary = ($base / 30) * $working_days;

        // ---- Position Allowance ----
        $position_allowance_taxable = $employee->position->allowance ?? 0;
        $position_allowance_non_tax = 0;

        if ($employee->position->name === 'CEO') {
            $position_allowance_non_tax = 0.10 * $base;
        }

        // ---- Transport Allowance ----
        $transport_allowance = ($employee->position->name === 'Normal Employee') ? 600 : 2200;

        $other = $payroll->other_commission ?? 0;

        // ---- Gross & Taxable ----
        $gross = $earned_salary + $position_allowance_taxable + $position_allowance_non_tax + $transport_allowance + $other;
        $taxable_income = $earned_salary + $other + $position_allowance_taxable;

        // ---- Income Tax ----
        $income_tax = $this->calculateIncomeTax($taxable_income);

        // ---- Pension ----
        $isPermanent = strtolower($employee->employmentType->name) === 'permanent';
        $employee_pension = $isPermanent ? 0.07 * $earned_salary : 0;
        $employer_pension = $isPermanent ? 0.11 * $earned_salary : 0;

        $total_deduction = $income_tax + $employee_pension;
        $net_payment = $gross - $total_deduction;

        $payroll->fill([
            'earned_salary'                 => $earned_salary,
            'position_allowance_taxable'    => $position_allowance_taxable,
            'position_allowance_non_tax'    => $position_allowance_non_tax,
            'transport_allowance'           => $transport_allowance,
            'gross_pay'                     => $gross,
            'taxable_income'                => $taxable_income,
            'income_tax'                    => $income_tax,
            'employee_pension'              => $employee_pension,
            'employer_pension'              => $employer_pension,
            'total_deduction'               => $total_deduction,
            'net_payment'                   => $net_payment,
            'status'                        => 'prepared',
            'rejection_reason'              => null,
            'rejected_at'                   => null,
            'approved_by'                   => null,
        ])->save();

        return $payroll;
    }
}
