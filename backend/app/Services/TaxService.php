<?php

namespace App\Services;

class TaxService
{
    /**
     * Calculate income tax based on Ethiopian progressive tax brackets.
     *
     * @param float $income
     * @return float
     */
    public function calculateIncomeTax(float $income): float
    {
        if ($income <= 600) {
            return 0;
        } elseif ($income <= 1650) {
            return ($income * 0.10) - 60;
        } elseif ($income <= 3200) {
            return ($income * 0.15) - 142.5;
        } elseif ($income <= 5250) {
            return ($income * 0.20) - 302.5;
        } elseif ($income <= 7800) {
            return ($income * 0.25) - 565;
        } elseif ($income <= 10900) {
            return ($income * 0.30) - 955;
        } else {
            return ($income * 0.35) - 1500;
        }
    }
}
