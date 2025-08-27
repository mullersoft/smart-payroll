<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PayrollReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Payroll;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Prepares the payroll data for both PDF and Excel reports.
     *
     * @param string $month The month in 'YYYY-MM' format.
     * @param string $status The payroll status to filter by.
     * @return array The prepared data array for the report view.
     */
    private function preparePayrollData($month, $status)
    {
        $query = Payroll::with(['employee', 'overtimes'])
            ->whereRaw("DATE_FORMAT(pay_month, '%Y-%m') = ?", [$month]);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $recordsRaw = $query->get();

        // Get preparer and approver names
        $preparer = 'Unknown Preparer';
        $approver = 'Not Approved';

        if ($recordsRaw->count() > 0) {
            $firstPayroll = $recordsRaw->first();

            if ($firstPayroll->prepared_by) {
                $preparerUser = User::find($firstPayroll->prepared_by);
                $preparer = $preparerUser ? $preparerUser->name : 'Unknown Preparer';
            }

            $approvedPayroll = $recordsRaw->where('status', 'approved')->first();
            if ($approvedPayroll && $approvedPayroll->approved_by) {
                $approverUser = User::find($approvedPayroll->approved_by);
                $approver = $approverUser ? $approverUser->name : 'Unknown Approver';
            }
        }

        $records = $recordsRaw->map(function ($payroll) {
            $totalOvertime = $payroll->overtimes->sum('amount');
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
                'overtime'            => $totalOvertime,
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

        return [
            'month'              => $month,
            'records'            => $records,
            'total_base_salary'  => $recordsRaw->sum('base_salary'),
            'total_earned'       => $recordsRaw->sum('earned_salary'),
            'total_pos_non_tax'  => $records->sum('position_non_tax'),
            'total_pos_taxable'  => $records->sum('position_taxable'),
            'total_trans_non'    => $records->sum('transport_non_tax'),
            'total_trans_tax'    => $records->sum('transport_taxable'),
            'total_commission'   => $records->sum('other_commission'),
            'total_overtime' => $recordsRaw->map(function ($payroll) {
                return $payroll->overtimes->sum('amount');
            })->sum(),
            'total_gross'        => $recordsRaw->sum('gross_pay'),
            'total_taxable'      => $recordsRaw->sum('taxable_income'),
            'total_income_tax'   => $recordsRaw->sum('income_tax'),
            'total_pen_emp'      => $recordsRaw->sum('employee_pension'),
            'total_pen_empr'     => $recordsRaw->sum('employer_pension'),
            'total_loan'         => $records->sum('loan_penalty'),
            'total_deduction'    => $recordsRaw->sum('total_deduction'),
            'total_net_payment'  => $recordsRaw->sum('net_payment'),
            'prepared_by'        => $preparer,
            'approved_by'        => $approver,
            'report_date'        => now()->format('d/m/Y'),
        ];
    }

    public function exportExcel(Request $request, $month)
    {
        $status = $request->query('status', 'all');
        $data = $this->preparePayrollData($month, $status);
        return Excel::download(new PayrollReportExport($data), "payroll_report_{$month}_{$status}.xlsx");
    }

    public function exportPdf(Request $request, $month)
    {
        $status = $request->query('status', 'approved');
        $data = $this->preparePayrollData($month, $status);

        $pdf = Pdf::loadView('reports.payroll_pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("payroll_report_{$month}.pdf");
    }
}
