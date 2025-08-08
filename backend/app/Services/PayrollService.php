<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Employee;

class PayrollService
{
    public function generatePayroll(array $data)
    {
        $employee = Employee::findOrFail($data['employee_id']);
        $base = $employee->base_salary;
        $working_days = $data['working_days'];
        $earned_salary = ($base / 30) * $working_days;

        // Position Allowance
        // $position_allowance = $employee->position === 'CEO' ? 0.10 * $base : 0.0;
$position = $employee->position;
$position_allowance = 0;
$position_allowance_taxable = true;

switch ($position) {
    case 'CEO':
        $position_allowance = 0.10 * $base;
        $position_allowance_taxable = false;
        break;
    case 'COO':
        $position_allowance = 2015.38;
        break;
    case 'CTO':
        $position_allowance = 2615.38;
        break;
    case 'CISO':
        $position_allowance = 1574.14;
        break;
    case 'Director':
    case 'Dept Lead':
        $position_allowance = 982.76;
        break;
    default:
        $position_allowance = 0;
        $position_allowance_taxable = false;
}

        // Transport Allowance
        $transport_allowance = $employee->position === 'Normal Employee' ? 600 : 2200;

        $other = $data['other_commission'] ?? 0;

        // Gross Pay
        $gross = $earned_salary + $position_allowance + $transport_allowance + $other;

        // Taxable Income (CEO allowance is non-taxable)
        // $taxable_income = $earned_salary + $other + ($employee->position === 'CEO' ? 0 : $position_allowance);
        $taxable_income = $earned_salary + $other;

        if ($position_allowance_taxable) {
            $taxable_income += $position_allowance;
        }

        // Actual Tax
        $income_tax = $this->calculateIncomeTax($taxable_income);

        // Pensions
        $employee_pension = $employee->employment_type === 'permanent' ? 0.07 * $base : 0;
        $employer_pension = $employee->employment_type === 'permanent' ? 0.11 * $base : 0;

        // Total deduction
        $total_deduction = $income_tax + $employee_pension;

        // Net Pay
        $net_payment = $gross - $total_deduction;

        return Payroll::create([
            'employee_id'         => $employee->id,
            'pay_month'           => $data['pay_month'],
            'working_days'        => $working_days,
            'base_salary'         => $base,
            'earned_salary'       => $earned_salary,
            'position_allowance'  => $position_allowance,
            'transport_allowance' => $transport_allowance,
            'other_commission'    => $other,
            'gross_pay'           => $gross,
            'taxable_income'      => $taxable_income,
            'income_tax'          => $income_tax,
            'employee_pension'    => $employee_pension,
            'employer_pension'    => $employer_pension,
            'total_deduction'     => $total_deduction,
            'net_payment'         => $net_payment,
            'status'              => 'prepared',
            'prepared_by'         => $data['prepared_by'],
            'approved_by'         => null,
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
    $employee = $payroll->employee;
    $base = $employee->base_salary;
    $working_days = $payroll->working_days;
    $earned_salary = ($base / 30) * $working_days;

    $position_allowance = in_array($employee->position, ['CEO']) ? 0.10 * $base : 0.0;
    $transport_allowance = in_array($employee->position, ['Normal Employee']) ? 600 : 2220;
    $other = $payroll->other_commission ?? 0;

    $gross = $earned_salary + $position_allowance + $transport_allowance + $other;
    $taxable_income = $earned_salary + $other + ($employee->position == 'CEO' ? 0 : $position_allowance);

    $income_tax = $this->calculateIncomeTax($taxable_income); // Use actual function
    $employee_pension = $employee->employment_type === 'permanent' ? 0.07 * $base : 0;
    $employer_pension = $employee->employment_type === 'permanent' ? 0.11 * $base : 0;

    $total_deduction = $income_tax + $employee_pension;
    $net_payment = $gross - $total_deduction;

    $payroll->fill([
        'earned_salary' => $earned_salary,
        'position_allowance' => $position_allowance,
        'transport_allowance' => $transport_allowance,
        'gross_pay' => $gross,
        'taxable_income' => $taxable_income,
        'income_tax' => $income_tax,
        'employee_pension' => $employee_pension,
        'employer_pension' => $employer_pension,
        'total_deduction' => $total_deduction,
        'net_payment' => $net_payment,
        'status' => 'prepared',
        'rejection_reason' => null,
        'rejected_at' => null,
        'approved_by' => null,
    ]);

    $payroll->save();

    return $payroll;
}

}
