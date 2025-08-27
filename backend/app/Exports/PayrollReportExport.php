<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

/**
 * Exports payroll data to an Excel file using a Blade view.
 * This class uses Maatwebsite\Excel's FromView concern.
 */
class PayrollReportExport implements FromView
{
    protected $data;

    /**
     * Initializes the export with the payroll data.
     *
     * @param array $data The data array prepared by the ReportController.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns the Blade view to be used for the Excel export.
     *
     * @return View
     */
    public function view(): View
    {
        // Use the correct view for the Excel report.
        // The original code was using 'reports.payroll_pdf'.
        return view('reports.payroll_excel_report', $this->data);
    }
}

