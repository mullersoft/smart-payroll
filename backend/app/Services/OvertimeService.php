<?php

namespace App\Services;

use App\Models\Employee;

class OvertimeService
{
    public function calculate(Employee $employee, array $overtimeData): array
    {
        // Hourly rate = base_salary / 30 days / 8 hours
        $hourlyRate = $employee->base_salary / 30 / 8;

        $total = 0;
        $records = [];

        foreach ($overtimeData as $ot) {
            switch ($ot['rate_type']) {
                case 'weekday_evening': // 6:30 a.m - 10:00 p.m
                    $multiplier = 1.25;
                    break;
                case 'night': // 10:00 p.m - 6:00 a.m
                    $multiplier = 1.5;
                    break;
                case 'rest_day': // weekly rest day
                    $multiplier = 2.0;
                    break;
                case 'holiday': // public holiday
                    $multiplier = 2.5;
                    break;
                default:
                    $multiplier = 1; // fallback (should not happen)
                    break;
            }

            $amount = $ot['hours'] * $hourlyRate * $multiplier;
            $total += $amount;

            $records[] = [
                'employee_id' => $employee->id,
                'rate_type'   => $ot['rate_type'],
                'hours'       => $ot['hours'],
                'multiplier'  => $multiplier,
                'amount'      => $amount,
            ];
        }

        return [
            'total' => $total,
            'records' => $records,
        ];
    }
}
