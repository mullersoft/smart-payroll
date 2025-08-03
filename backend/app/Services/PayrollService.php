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

        // Simplified allowance logic
        $position_allowance = in_array($employee->position, ['CEO']) ? 0.10 * $base : 0.0;
        $transport_allowance = in_array($employee->position, ['Normal Employee']) ? 600 : 2220;
        $other = $data['other_commission'] ?? 0;

        $gross = $earned_salary + $position_allowance + $transport_allowance + $other;
        $taxable_income = $earned_salary + $other + ($employee->position == 'CEO' ? 0 : $position_allowance);

        $income_tax = 0.15 * $taxable_income; // simple mock
        $employee_pension = $employee->employment_type === 'permanent' ? 0.07 * $base : 0;
        $employer_pension = $employee->employment_type === 'permanent' ? 0.11 * $base : 0;

        $total_deduction = $income_tax + $employee_pension;
        $net_payment = $gross - $total_deduction;

        return Payroll::create([
            'employee_id' => $employee->id,
            'pay_month' => $data['pay_month'],
            'working_days' => $working_days,
            'base_salary' => $base,
            'earned_salary' => $earned_salary,
            'position_allowance' => $position_allowance,
            'transport_allowance' => $transport_allowance,
            'other_commission' => $other,
            'gross_pay' => $gross,
            'taxable_income' => $taxable_income,
            'income_tax' => $income_tax,
            'employee_pension' => $employee_pension,
            'employer_pension' => $employer_pension,
            'total_deduction' => $total_deduction,
            'net_payment' => $net_payment,
            'status' => 'prepared',
            'prepared_by' => $data['prepared_by'],
            'approved_by' => null,
        ]);
    }
}
