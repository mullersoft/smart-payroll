<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ $month }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            /* smaller font */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            /* fix table layout */
            word-wrap: break-word;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            /* less padding */
            text-align: center;
            white-space: normal;
        }

        th {
            background-color: #eee;
        }
    </style>

</head>

<body>

    <!-- Header -->
    <div style="text-align: center; width: 100%;">
        <h3 style="margin: 0;">QELEM MEDA TECHNOLOGIES PLC</h3>
        <h3 style="margin: 0;">EMPLOYEES SALARY SHEET</h3>
        <h4 style="margin: 0;">
            FOR THE MONTH OF {{ strtoupper(date('F', strtotime($month))) }} {{ date('Y', strtotime($month)) }}
        </h4>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2" class="text-center">Employee Name</th>
                <th rowspan="2" class="text-center">Employment Date</th>
                <th rowspan="2" class="text-center">Basic Salary</th>
                <th rowspan="2" class="text-center">W/Days</th>
                <th rowspan="2" class="text-center">Earned Salary</th>
                <th colspan="2" class="text-center">Position Allowance</th>
                <th colspan="2" class="text-center">Transport Allowances</th>
                <th rowspan="2" class="text-center">OT/Com/Other Benefit</th>
                <th rowspan="2" class="text-center">Gross Pay</th>
                <th rowspan="2" class="text-center">Taxable Income</th>
                <th colspan="5" class="text-center">Deductions</th>
                <th rowspan="2" class="text-center">Total Deduction</th>
                <th rowspan="2" class="text-center">Net Payment</th>
            </tr>
            <tr>
                <th>Non Taxable</th>
                <th>Taxable</th>
                <th>Non Taxable</th>
                <th>Taxable</th>
                <th>Income Tax</th>
                <th>Pension (Emp.)</th>
                <th>Pension (Er.)</th>
                <th>Total Pension</th>
                <th>Loan/Penalty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-center">{{ $row['employee_name'] }}</td>
                <td class="text-center">{{ $row['employment_date'] }}</td>
                <td>{{ number_format($row['base_salary'], 2) }}</td>
                <td>{{ $row['working_days'] }}</td>
                <td>{{ number_format($row['earned_salary'], 2) }}</td>
                <td>{{ number_format($row['position_non_tax'], 2) }}</td>
                <td>{{ number_format($row['position_taxable'], 2) }}</td>
                <td>{{ number_format($row['transport_non_tax'], 2) }}</td>
                <td>{{ number_format($row['transport_taxable'], 2) }}</td>
                <td>{{ number_format($row['other_commission'], 2) }}</td>
                <td>{{ number_format($row['gross_pay'], 2) }}</td>
                <td>{{ number_format($row['taxable_income'], 2) }}</td>
                <td>{{ number_format($row['income_tax'], 2) }}</td>
                <td>{{ number_format($row['employee_pension'], 2) }}</td>
                <td>{{ number_format($row['employer_pension'], 2) }}</td>
                <td>{{ number_format($row['employee_pension'] + $row['employer_pension'], 2) }}</td>
                <td>{{ number_format($row['loan_penalty'], 2) }}</td>
                <td>{{ number_format($row['total_deduction'], 2) }}</td>
                <td>{{ number_format($row['net_payment'], 2) }}</td>
            </tr>
            @endforeach

            <!-- Totals -->
            <tr>
                <th colspan="3" class="text-center">Total</th>
                <th>{{ number_format($total_base_salary, 2) }}</th>
                <th></th>
                <th>{{ number_format($total_earned, 2) }}</th>
                <th>{{ number_format($total_pos_non_tax, 2) }}</th>
                <th>{{ number_format($total_pos_taxable, 2) }}</th>
                <th>{{ number_format($total_trans_non, 2) }}</th>
                <th>{{ number_format($total_trans_tax, 2) }}</th>
                <th></th>
                <th>{{ number_format($total_gross, 2) }}</th>
                <th>{{ number_format($total_taxable, 2) }}</th>
                <th>{{ number_format($total_income_tax, 2) }}</th>
                <th>{{ number_format($total_pen_emp, 2) }}</th>
                <th>{{ number_format($total_pen_empr, 2) }}</th>
                <th>{{ number_format($total_pen_emp + $total_pen_empr, 2) }}</th>
                <th>{{ number_format($total_loan, 2) }}</th>
                <th>{{ number_format($total_deduction, 2) }}</th>
                <th>{{ number_format($total_net_payment, 2) }}</th>
            </tr>
        </tbody>
    </table>

   <!-- Footer -->
<br><br>
<table class="no-border" style="width:100%; border:none;">
    <tr>
        <td style="width:50%; text-align:center; border:none;">
            Prepared By:<br>
            {{ $prepared_by }}<br><br>
            __________________<br>
            Signature<br>
            Date: {{ $report_date }}
        </td>
        <td style="width:50%; text-align:center; border:none;">
            Approved By:<br>
            {{ $approved_by }}<br><br>
            __________________<br>
            Signature<br>
            Date: {{ $report_date }}
        </td>
    </tr>
</table>
</body>

</html>