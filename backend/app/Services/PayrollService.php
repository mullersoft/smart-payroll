<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Allowance;
use App\Services\OvertimeService;
use App\Services\TaxService;

class PayrollService
{
    protected $taxService;
    protected $overtimeService;

    public function __construct(TaxService $taxService, OvertimeService $overtimeService)
    {
        $this->taxService = $taxService;
        $this->overtimeService = $overtimeService;
    }

    /**
     * Generate a new payroll record
     */
    public function generatePayroll(array $data)
    {
        $employee = Employee::with(['position', 'employmentType', 'allowances'])
            ->findOrFail($data['employee_id']);

        $base = $employee->base_salary;
        $working_days = $data['working_days'];
        $earned_salary = ($base / 30) * $working_days;

        // ---- Position Allowance ----
        $position_allowance_taxable = $employee->position->allowance ?? 0;
        $position_allowance_non_tax = 0;

        if ($employee->position && strtolower($employee->position->name) === 'ceo') {
            $position_allowance_non_tax = 0.10 * $base; // 10% non-taxable for CEO
        }

        // ---- Employee Allowances ----
        $taxable_allowances = 0;
        $non_taxable_allowances = 0;
        foreach ($employee->allowances as $allowance) {
            if ($allowance->is_taxable) {
                $taxable_allowances += $allowance->value;
            } else {
                $non_taxable_allowances += $allowance->value;
            }
        }

        // ---- Transport Allowance ----
        $transport_allowance = ($employee->position && strtolower($employee->position->name) === 'normal employee')
            ? 600
            : (Allowance::where('name', 'Transportation Allowance')->value('value') ?? 2200);

        // ---- Other commissions ----
        $other = $data['other_commission'] ?? 0;

        // ---- Overtime ----
        $overtimeResult = $this->overtimeService->calculate($employee, $data['overtimes'] ?? []);
        $overtimeTotal = $overtimeResult['total'];

        // ---- Gross & Taxable Income ----
        $gross = $earned_salary
            + $position_allowance_taxable
            + $position_allowance_non_tax
            + $taxable_allowances
            + $non_taxable_allowances
            + $other
            // + $transport_allowance
            + $overtimeTotal;

        $taxable_income = $earned_salary + $other + $position_allowance_taxable + $taxable_allowances + $overtimeTotal;

        // ---- Income Tax ----
        $income_tax = $this->taxService->calculateIncomeTax($taxable_income);

        // ---- Pension (permanent employees only) ----
        $isPermanent = strtolower($employee->employmentType->name) === 'permanent';
        $employee_pension = $isPermanent ? 0.07 * $earned_salary : 0;
        $employer_pension = $isPermanent ? 0.11 * $earned_salary : 0;
        $pension_contribution = $isPermanent ? $employee_pension + $employer_pension : 0;

        // ---- Total deduction & Net Pay ----
        $total_deduction = $income_tax + $employee_pension;
        $net_payment = $gross - $total_deduction;

        // ✅ Create payroll record
        $payroll = Payroll::create([
            'employee_id'                => $employee->id,
            'pay_month'                  => $data['pay_month'],
            'working_days'               => $working_days,
            'base_salary'                => $base,
            'earned_salary'              => $earned_salary,
            'position_allowance_taxable' => $position_allowance_taxable,
            'position_allowance_non_tax' => $position_allowance_non_tax,
            'transport_allowance'        => $transport_allowance,
            'other_commission'           => $other,
            'gross_pay'                  => $gross,
            'taxable_income'             => $taxable_income,
            'income_tax'                 => $income_tax,
            'employee_pension'           => $employee_pension,
            'employer_pension'           => $employer_pension,
            'pension_contribution'       => $pension_contribution,
            'total_deduction'            => $total_deduction,
            'net_payment'                => $net_payment,
            'status'                     => 'prepared',
            'prepared_by'                => $data['prepared_by'],
            'approved_by'                => null,
        ]);

        // ✅ Save allowances snapshot
        foreach ($employee->allowances as $allowance) {
            $payroll->allowances()->create([
                'name'       => $allowance->name,
                'amount'     => $allowance->value,
                'is_taxable' => $allowance->is_taxable,
            ]);
        }

        // ✅ Save overtime snapshot
        foreach ($overtimeResult['records'] as $ot) {
            $payroll->overtimes()->create(array_merge($ot, [
                'payroll_id'  => $payroll->id,
                'employee_id' => $employee->id,
            ]));
        }

        return $payroll->load('allowances', 'overtimes', 'employee.position', 'employee.employmentType');
    }

    /**
     * Recalculate payroll for an existing record
     */
    public function regeneratePayroll(Payroll $payroll)
    {
        $employee = $payroll->employee()->with(['position', 'employmentType', 'allowances'])->first();

        $base = $employee->base_salary;
        $working_days = $payroll->working_days;
        $earned_salary = ($base / 30) * $working_days;

        // ---- Position Allowance ----
        $position_allowance_taxable = $employee->position->allowance ?? 0;
        $position_allowance_non_tax = 0;

        if ($employee->position && strtolower($employee->position->name) === 'ceo') {
            $position_allowance_non_tax = 0.10 * $base;
        }

        // ---- Employee Allowances ----
        $taxable_allowances = 0;
        $non_taxable_allowances = 0;
        foreach ($employee->allowances as $allowance) {
            if ($allowance->is_taxable) {
                $taxable_allowances += $allowance->value;
            } else {
                $non_taxable_allowances += $allowance->value;
            }
        }

        // ---- Transport Allowance ----
        $transport_allowance = ($employee->position && strtolower($employee->position->name) === 'normal employee')
            ? 600
            : (Allowance::where('name', 'Transportation Allowance')->value('value') ?? 2200);

        // ---- Other commissions ----
        $other = $payroll->other_commission ?? 0;

        // ---- Overtime ---- (recalculate from DB records)
        $overtimeResult = $this->overtimeService->calculate($employee, $payroll->overtimes()->get()->toArray());
        $overtimeTotal = $overtimeResult['total'];

        // ---- Gross & Taxable Income ----
        $gross = $earned_salary
            + $position_allowance_taxable
            + $position_allowance_non_tax
            + $taxable_allowances
            + $non_taxable_allowances
            + $transport_allowance
            + $other
            + $overtimeTotal;

        $taxable_income = $earned_salary + $other + $position_allowance_taxable + $taxable_allowances + $overtimeTotal;

        // ---- Income Tax ----
        $income_tax = $this->taxService->calculateIncomeTax($taxable_income);

        // ---- Pension ----
        $isPermanent = strtolower($employee->employmentType->name) === 'permanent';
        $employee_pension = $isPermanent ? 0.07 * $earned_salary : 0;
        $employer_pension = $isPermanent ? 0.11 * $earned_salary : 0;
        $pension_contribution = $isPermanent ? $employee_pension + $employer_pension : 0;

        $total_deduction = $income_tax + $employee_pension;
        $net_payment = $gross - $total_deduction;

        // ✅ Update payroll record
        $payroll->fill([
            'earned_salary'              => $earned_salary,
            'position_allowance_taxable' => $position_allowance_taxable,
            'position_allowance_non_tax' => $position_allowance_non_tax,
            'transport_allowance'        => $transport_allowance,
            'gross_pay'                  => $gross,
            'taxable_income'             => $taxable_income,
            'income_tax'                 => $income_tax,
            'employee_pension'           => $employee_pension,
            'employer_pension'           => $employer_pension,
            'pension_contribution'       => $pension_contribution,
            'total_deduction'            => $total_deduction,
            'net_payment'                => $net_payment,
            'status'                     => 'prepared',
            'rejection_reason'           => null,
            'rejected_at'                => null,
            'approved_by'                => null,
        ])->save();

        // ✅ Refresh allowances snapshot
        $payroll->allowances()->delete();
        foreach ($employee->allowances as $allowance) {
            $payroll->allowances()->create([
                'name'       => $allowance->name,
                'amount'     => $allowance->value,
                'is_taxable' => $allowance->is_taxable,
            ]);
        }

        // ✅ Refresh overtime snapshot
        $payroll->overtimes()->delete();
        foreach ($overtimeResult['records'] as $ot) {
            $payroll->overtimes()->create(array_merge($ot, [
                'payroll_id'  => $payroll->id,
                'employee_id' => $employee->id,
            ]));
        }

        return $payroll->load('allowances', 'overtimes', 'employee.position', 'employee.employmentType');
    }
}
