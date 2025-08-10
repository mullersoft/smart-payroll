<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection()
    {
        return Payroll::with('employee')
            ->whereRaw("DATE_FORMAT(pay_month, '%Y-%m') = ?", [$this->month])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Employment Date',
            'Base Salary',
            'Working Days',
            'Earned Salary',
            'Position Allowance',
            'Transport Allowance',
            'Other',
            'Gross Pay',
            'Taxable Income',
            'Income Tax',
            'Pension (Employee)',
            'Pension (Employer)',
            'Total Deduction',
            'Net Pay'
        ];
    }

    public function map($row): array
    {
        return [
            $row->employee->full_name,
            $row->employee->position,
            $row->employee->employment_date,
            $row->base_salary,
            $row->working_days,
            $row->earned_salary,
            $row->position_allowance,
            $row->transport_allowance,
            $row->other_commission,
            $row->gross_pay,
            $row->taxable_income,
            $row->income_tax,
            $row->employee_pension,
            $row->employer_pension,
            $row->total_deduction,
            $row->net_payment,
        ];
    }
}
