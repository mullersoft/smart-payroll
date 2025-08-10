<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\PayrollReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Payroll;

class ReportController extends Controller
{
    public function monthlyPayroll($month)
    {
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

    public function exportExcel($month)
    {
        return Excel::download(new PayrollReportExport($month), "payroll_report_{$month}.xlsx");
    }

    public function exportPdf($month)
    {
        $user = auth()->user(); // currently logged in preparer
        $approver = \App\Models\User::where('role', 'approver')->first(); // adjust role check as needed

        $recordsRaw = Payroll::with('employee')
            ->whereRaw("DATE_FORMAT(pay_month, '%Y-%m') = ?", [$month])
            ->get();

        $records = $recordsRaw->map(function ($payroll) {
            return [
                'employee_name'       => $payroll->employee->full_name,
                'employment_date'     => $payroll->employee->employment_date,
                'base_salary'         => $payroll->base_salary,
                'working_days'        => $payroll->working_days,
                'earned_salary'       => $payroll->earned_salary,
                'position_non_tax'    => $payroll->position_allowance_non_tax ?? 0,
                'position_taxable'    => $payroll->position_allowance_taxable ?? 0,
                'transport_non_tax'   => $payroll->transport_allowance ?? 0,
                'transport_taxable'   => $payroll->transport_taxable ?? 0,
                'other_commission'    => $payroll->other_commission ?? 0,
                'gross_pay'           => $payroll->gross_pay,
                'taxable_income'      => $payroll->taxable_income,
                'income_tax'          => $payroll->income_tax,
                'employee_pension'    => $payroll->employee_pension,
                'employer_pension'    => $payroll->employer_pension,
                'loan_penalty'        => $payroll->loan_penalty ?? 0,
                'total_deduction'     => $payroll->total_deduction,
                'net_payment'         => $payroll->net_payment,
            ];
        });

        $data = [
            'month'             => $month,
            'records'           => $records,
            'total_base_salary' => $recordsRaw->sum('base_salary'),
            'total_earned'      => $recordsRaw->sum('earned_salary'),
            'total_pos_non_tax' => $records->sum('position_non_tax'),
            'total_pos_taxable' => $records->sum('position_taxable'),
            'total_trans_non'   => $records->sum('transport_non_tax'),
            'total_trans_tax'   => $records->sum('transport_taxable'),
            'total_gross'       => $recordsRaw->sum('gross_pay'),
            'total_taxable'     => $recordsRaw->sum('taxable_income'),
            'total_income_tax'  => $recordsRaw->sum('income_tax'),
            'total_pen_emp'     => $recordsRaw->sum('employee_pension'),
            'total_pen_empr'    => $recordsRaw->sum('employer_pension'),
            'total_loan'        => $records->sum('loan_penalty'),
            'total_deduction'   => $recordsRaw->sum('total_deduction'),
            'total_net_payment' => $recordsRaw->sum('net_payment'),
            'prepared_by'       => $user->name ?? 'Prepared User',
            'approved_by'       => $approver->name ?? 'Not Approved',
            'report_date'       => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('reports.payroll_pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("payroll_report_{$month}.pdf");
    }
}
