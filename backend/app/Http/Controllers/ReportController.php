<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthlyPayroll($month)
    {
        $records = Payroll::with('employee')->where('pay_month', $month)->get();

        return response()->json([
            'month' => $month,
            'total_employees' => $records->count(),
            'total_net_payment' => $records->sum('net_payment'),
            'payrolls' => $records,
        ]);
    }

    public function exportCsv($month)
{
    $records = Payroll::with('employee')->where('pay_month', $month)->get();

    $filename = "payroll_report_{$month}.csv";

    $callback = function () use ($records) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Employee', 'Position', 'Base Salary', 'Net Payment'
        ]);

        foreach ($records as $row) {
            fputcsv($handle, [
                $row->employee->full_name,
                $row->employee->position,
                $row->base_salary,
                $row->net_payment,
            ]);
        }

        fclose($handle);
    };

    return response()->stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$filename}",
    ]);
}

}
