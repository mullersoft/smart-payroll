<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ $month }}</title>
</head>

<body
    style="font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 0; padding: 20px; background-color: #f7fafc;">

    {{-- Header Section --}}
    <table
        style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <thead>
            @php
                // Calculate the total number of columns dynamically for the header span
                $headerColspan = 17;
                if ($total_pos_non_tax > 0) {
                    $headerColspan++;
                }
                if ($total_trans_tax > 0) {
                    $headerColspan++;
                }
                if ($total_overtime > 0) {
                    $headerColspan++;
                }
                if ($total_commission > 0) {
                    $headerColspan++;
                }
                if ($total_loan > 0) {
                    $headerColspan++;
                }
            @endphp
            <tr>
                <th colspan="{{ $headerColspan }}"
                    style="padding: 12px 6px; text-align:center; vertical-align: middle; background-color: #4a5568; color: white; font-weight: bold; font-size: 14px;">
                    QELEM MEDA TECHNOLOGIES PLC</th>
            </tr>
            <tr>
                <th colspan="{{ $headerColspan }}"
                    style="padding: 12px 6px; text-align:center; vertical-align: middle; background-color: #718096; color: white; font-weight: bold; font-size: 12px;">
                    EMPLOYEES SALARY SHEET</th>
            </tr>
            <tr>
                <th colspan="{{ $headerColspan }}"
                    style="padding: 12px 6px; text-align:center; vertical-align: middle; background-color: #a0aec0; color: #1a202c; font-weight: bold; font-size: 10px;">
                    FOR THE MONTH OF {{ strtoupper(date('F', strtotime($month))) }} {{ date('Y', strtotime($month)) }}
                </th>
            </tr>
        </thead>
    </table>

    {{-- Main Payroll Table --}}
    <table
        style="width: 100%; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    No.</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Employee Name</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    W/Days</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Basic Salary</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Earned Salary</th>

                @php
                    $PositionAllowanceColspan = 1;
                    if ($total_pos_non_tax > 0) {
                        $PositionAllowanceColspan += 1;
                    }
                @endphp
                <th colspan="{{ $PositionAllowanceColspan }}"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #c6f6d5; font-weight: bold; color: #2f855a;">
                    Position Allow.</th>

                @php
                    $TransportAllowanceColspan = 1;
                    if ($total_trans_tax > 0) {
                        $TransportAllowanceColspan += 1;
                    }
                @endphp
                <th colspan="{{ $TransportAllowanceColspan }}"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #b2f5ea; font-weight: bold; color: #2c7a7b;">
                    Transport Allow.</th>

                @if ($total_overtime > 0)
                    <th rowspan="2"
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                        Overtime</th>
                @endif

                @if ($total_commission > 0)
                    <th rowspan="2"
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                        Other Benefit</th>
                @endif

                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Gross Pay</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Taxable Income</th>

                @php
                    $deductionsColspan = 4;
                    if ($total_loan > 0) {
                        $deductionsColspan += 1;
                    }
                @endphp
                <th colspan="{{ $deductionsColspan }}"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #fed7d7; font-weight: bold; color:#c53030;">
                    Deductions</th>

                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Total Deduction</th>
                <th rowspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #cbd5e0; font-weight: bold;">
                    Net Payment</th>
            </tr>
            <tr>
                @if ($total_pos_non_tax > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #9ae6b4; font-weight: bold; font-size: 9px;">
                        Non Taxable</th>
                @endif
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #9ae6b4; font-weight: bold; font-size: 9px;">
                    Taxable</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #81e6d9; font-weight: bold; font-size: 9px;">
                    Non Taxable</th>
                @if ($total_trans_tax > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #81e6d9; font-weight: bold; font-size: 9px;">
                        Taxable</th>
                @endif
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #feb2b2; font-weight: bold; font-size: 9px;">
                    Income Tax</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #feb2b2; font-weight: bold; font-size: 9px;">
                    Pension (Emp.)</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #feb2b2; font-weight: bold; font-size: 9px;">
                    Pension (Er.)</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #feb2b2; font-weight: bold; font-size: 9px;">
                    Total Pension</th>
                @if ($total_loan > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle; background-color: #feb2b2; font-weight: bold; font-size: 9px;">
                        Loan/Penalty</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $row)
                <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f7fafc' }};">
                    <td
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle;">
                        {{ $index + 1 }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: left;">
                        {{ $row['employee_name'] }}</td>
                    <td
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; text-align: center; vertical-align: middle;">
                        {{ $row['working_days'] }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['base_salary'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['earned_salary'], 2) }}</td>
                    @if ($total_pos_non_tax > 0)
                        <td
                            style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                            {{ number_format($row['position_non_tax'], 2) }}</td>
                    @endif
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['position_taxable'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['transport_non_tax'], 2) }}</td>
                    @if ($total_trans_tax > 0)
                        <td
                            style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                            {{ number_format($row['transport_taxable'], 2) }}</td>
                    @endif
                    @if ($total_overtime > 0)
                        <td
                            style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                            {{ number_format($row['overtime'], 2) }}</td>
                    @endif
                    @if ($total_commission > 0)
                        <td
                            style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                            {{ number_format($row['other_commission'], 2) }}</td>
                    @endif
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['gross_pay'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['taxable_income'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['income_tax'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['employee_pension'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['employer_pension'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['employee_pension'] + $row['employer_pension'], 2) }}</td>
                    @if ($total_loan > 0)
                        <td
                            style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                            {{ number_format($row['loan_penalty'], 2) }}</td>
                    @endif
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['total_deduction'], 2) }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; text-align: right;">
                        {{ number_format($row['net_payment'], 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <th colspan="3"
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf;">
                    Total</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_base_salary, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_earned, 2) }}</th>
                @if ($total_pos_non_tax > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                        {{ number_format($total_pos_non_tax, 2) }}</th>
                @endif
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_pos_taxable, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_trans_non, 2) }}</th>
                @if ($total_trans_tax > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                        {{ number_format($total_trans_tax, 2) }}</th>
                @endif
                @if ($total_overtime > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                        {{ number_format($total_overtime, 2) }}</th>
                @endif
                @if ($total_commission > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                        {{ number_format($total_commission, 2) }}</th>
                @endif
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_gross, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_taxable, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_income_tax, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_pen_emp, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_pen_empr, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_pen_emp + $total_pen_empr, 2) }}</th>
                @if ($total_loan > 0)
                    <th
                        style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                        {{ number_format($total_loan, 2) }}</th>
                @endif
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_deduction, 2) }}</th>
                <th
                    style="border: 1px solid #e2e8f0; padding: 8px 6px; vertical-align: middle; background-color: #e2e8f0; font-weight: bold; color: #4c51bf; text-align: right;">
                    {{ number_format($total_net_payment, 2) }}</th>
            </tr>
        </tbody>
    </table>

    {{-- Footer section for signatures --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; word-wrap: break-word;">
        <tbody>
            <tr>
                <td colspan="{{ floor($headerColspan / 2) }}" rowspan="5"
                    style="border: none; padding: 20px 6px; vertical-align: top; text-align:center; font-weight: bold; border-top: 1px solid #a0aec0;">
                    Prepared By:<br>
                    <span style="display: block; margin-top: 8px;">{{ $prepared_by }}</span><br>
                    <span style="display: block; margin-top: 16px;">__________________</span><br>
                    <span style="display: block;">Signature</span><br>
                    <span style="display: block;">Date: {{ $report_date }}</span>
                </td>
                <td colspan="{{ ceil($headerColspan / 2) }}" rowspan="5"
                    style="border: none; padding: 20px 6px; vertical-align: top; text-align:center; font-weight: bold; border-top: 1px solid #a0aec0;">
                    Approved By:<br>
                    <span style="display: block; margin-top: 8px;">{{ $approved_by }}</span><br>
                    <span style="display: block; margin-top: 16px;">__________________</span><br>
                    <span style="display: block;">Signature</span><br>
                    <span style="display: block;">Date: {{ $report_date }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
