<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Return monthly payroll summary in JSON format
     */
  public function monthlyPayroll($month)
{
    // $month will be like "2025-07"
    $records = Payroll::with('employee')
        ->whereRaw("DATE_FORMAT(pay_month, '%Y-%m') = ?", [$month])
        ->get();

    $report = $records->map(function ($payroll) {
        return [
            'employee_name'        => $payroll->employee->full_name,
            'employment_date'      => $payroll->employee->employment_date,
            'position'             => $payroll->employee->position,
            'base_salary'          => $payroll->base_salary,
            'working_days'         => $payroll->working_days,
            'earned_salary'        => $payroll->earned_salary,
            'position_allowance'   => $payroll->position_allowance,
            'transport_allowance'  => $payroll->transport_allowance,
            'other_commission'     => $payroll->other_commission,
            'gross_pay'            => $payroll->gross_pay,
            'taxable_income'       => $payroll->taxable_income,
            'income_tax'           => $payroll->income_tax,
            'employee_pension'     => $payroll->employee_pension,
            'employer_pension'     => $payroll->employer_pension,
            'total_deduction'      => $payroll->total_deduction,
            'net_payment'          => $payroll->net_payment,
        ];
    });

    return response()->json([
        'month' => $month,
        'total_employees' => $records->count(),
        'total_net_payment' => $records->sum('net_payment'),
        'records' => $report,
    ]);
}


    /**
     * Export payroll report as downloadable CSV
     */
    public function exportCsv($month)
    {
        $records = Payroll::with('employee')->where('pay_month', $month)->get();
        $filename = "payroll_report_{$month}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, [
                'Employee Name', 'Position', 'Employment Date', 'Base Salary', 'Working Days',
                'Earned Salary', 'Position Allowance', 'Transport Allowance', 'Other',
                'Gross Pay', 'Taxable Income', 'Income Tax', 'Pension (Employee)',
                'Pension (Employer)', 'Total Deduction', 'Net Pay'
            ]);

            foreach ($records as $row) {
                fputcsv($handle, [
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
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
