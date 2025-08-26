<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ $month }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            word-wrap: break-word;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: center;
            white-space: normal;
        }

        th {
            background-color: #eee;
        }
    </style>

</head>

<body>

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
                {{-- <th rowspan="2" class="text-center">Employment Date</th> --}}
                <th rowspan="2" class="text-center">W/Days</th>

                <th rowspan="2" class="text-center">Basic Salary</th>
                {{-- <th rowspan="2" class="text-center">W/Days</th> --}}
                <th rowspan="2" class="text-center">Earned Salary</th>
                  @php
                    $PositionAllowanceColspan = 1; // Start with the fixed columns
                    if ($total_pos_non_tax > 0 ) {
                        $PositionAllowanceColspan += 1;
                    }
                @endphp
                <th colspan="{{$PositionAllowanceColspan}}" class="text-center">Position Allow.</th>
                 @php
                    $TransportAllowanceColspan = 1;
                    if ($total_trans_tax > 0 ) {
                        $TransportAllowanceColspan += 1;
                    }
                @endphp
                <th colspan="{{$TransportAllowanceColspan}}" class="text-center">Transport Allow.</th>
                @if($total_overtime>0)
                <th rowspan="2" class="text-center">Overtime</th>
                @endif
                {{-- <th rowspan="2" class="text-center">Other Benefit</th> --}}
                @if($total_commission>0)
                <th rowspan="2" class="text-center">Other Benefit</th>
                @endif
                <th rowspan="2" class="text-center">Gross Pay</th>
                <th rowspan="2" class="text-center">Taxable Income</th>
                {{-- Calculate colspan for Deductions based on conditional columns --}}
                @php
                    $deductionsColspan = 4; // Start with the fixed columns
                    if ($total_loan > 0) {
                        $deductionsColspan += 1; // Add 1 if Loan/Penalty is visible
                    }
                @endphp
                <th colspan="{{ $deductionsColspan }}" class="text-center">Deductions</th>
                <th rowspan="2" class="text-center">Total Deduction</th>
                <th rowspan="2" class="text-center">Net Payment</th>
            </tr>
            <tr>
                    @if ($total_pos_non_tax > 0)
                        <th>Non Taxable</th>
                    @endif
                    <th>Taxable</th>
                <th>Non Taxable</th>
                @if($total_trans_tax > 0)
                    <th>Taxable</th>
                @endif
                {{-- <th>Taxable</th> --}}
                <th>Income Tax</th>
                <th>Pension (Emp.)</th>
                <th>Pension (Er.)</th>
                <th>Total Pension</th>
                @if ($total_loan > 0)
                    <th>Loan/Penalty</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row['employee_name'] }}</td>
                    {{-- <td class="text-center">{{ $row['employment_date'] }}</td> --}}
                    <td>{{ $row['working_days'] }}</td>

                    <td>{{ number_format($row['base_salary'], 2) }}</td>
                    {{-- <td>{{ $row['working_days'] }}</td> --}}
                    <td>{{ number_format($row['earned_salary'], 2) }}</td>
                    {{-- <td>{{ number_format($row['position_non_tax'], 2) }}</td> --}}
                     @if ($total_pos_non_tax > 0)
                        <td>{{ number_format($row['position_non_tax'], 2) }}</td>
                    @endif
                    {{-- <td>{{ number_format($row['position_taxable'], 2) }}</td> --}}
                    @if ($total_pos_taxable > 0)
                        <td>{{ number_format($row['position_taxable'], 2) }}</td>
                    @endif
                    <td>{{ number_format($row['transport_non_tax'], 2) }}</td>
                    {{-- <td>{{ number_format($row['transport_taxable'], 2) }}</td> --}}
                    @if($total_trans_tax > 0)
                    <td>{{ number_format($row['transport_taxable'], 2) }}</td>
                      @endif
                    {{-- <td>{{ number_format($row['transport_taxable'], 2) }}</td> --}}
                    @if($total_overtime>0)
                    <td>{{ number_format($row['overtime'], 2) }}</td>
                    @endif
                    {{-- <td>{{ number_format($row['other_commission'], 2) }}</td> --}}
                    @if($total_commission>0)
                    <td>{{ number_format($row['other_commission'], 2) }}</td>
                    @endif
                    <td>{{ number_format($row['gross_pay'], 2) }}</td>
                    <td>{{ number_format($row['taxable_income'], 2) }}</td>
                    <td>{{ number_format($row['income_tax'], 2) }}</td>
                    <td>{{ number_format($row['employee_pension'], 2) }}</td>
                    <td>{{ number_format($row['employer_pension'], 2) }}</td>
                    <td>{{ number_format($row['employee_pension'] + $row['employer_pension'], 2) }}</td>
                    @if ($total_loan > 0)
                        <td>{{ number_format($row['loan_penalty'], 2) }}</td>
                    @endif
                    <td>{{ number_format($row['total_deduction'], 2) }}</td>
                    <td>{{ number_format($row['net_payment'], 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <th colspan="3" class="text-center">Total</th>
                {{-- <th></th>  // for working days --}}

                <th>{{ number_format($total_base_salary, 2) }}</th>
                <th>{{ number_format($total_earned, 2) }}</th>
                {{-- <th>{{ number_format($total_pos_non_tax, 2) }}</th> --}}
                    @if ($total_pos_non_tax > 0)
                            <th>{{ number_format($total_pos_non_tax, 2) }}</th>
                        @endif
                    {{-- <th>{{ number_format($total_pos_taxable, 2) }}</th> --}}
                    @if ($total_pos_taxable > 0)
                <th>{{ number_format($total_pos_taxable, 2) }}</th>
                    @endif
                <th>{{ number_format($total_trans_non, 2) }}</th>
                {{-- <th>{{ number_format($total_trans_tax, 2) }}</th> --}}
                @if($total_trans_tax>0)
                <th>{{ number_format($total_trans_tax, 2) }}</th>
                  @endif
                  @if($total_overtime>0)
                <th>{{ number_format($total_overtime, 2) }}</th>
                    @endif
                {{-- <th>{{ number_format($total_commission, 2) }}</th> --}}
                @if($total_commission>0)
                <th>{{ number_format($total_commission, 2) }}</th>
                @endif
                <th>{{ number_format($total_gross, 2) }}</th>
                <th>{{ number_format($total_taxable, 2) }}</th>
                <th>{{ number_format($total_income_tax, 2) }}</th>
                <th>{{ number_format($total_pen_emp, 2) }}</th>
                <th>{{ number_format($total_pen_empr, 2) }}</th>
                <th>{{ number_format($total_pen_emp + $total_pen_empr, 2) }}</th>
                @if ($total_loan > 0)
                    <th>{{ number_format($total_loan, 2) }}</th>
                @endif
                <th>{{ number_format($total_deduction, 2) }}</th>
                <th>{{ number_format($total_net_payment, 2) }}</th>
            </tr>
        </tbody>
    </table>

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
