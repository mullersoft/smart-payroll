<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pay_month',
        'working_days',
        'base_salary',
        'earned_salary',
        'position_allowance_taxable',
        'position_allowance_non_tax',
        'transport_allowance',
        'other_commission',
        'gross_pay',
        'taxable_income',
        'income_tax',
        'employee_pension',
        'employer_pension',
        'pension_contribution',
        'total_deduction',
        'net_payment',
        'status',
        'prepared_by',
        'approved_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    // ✅ FIX: must be declared at top-level, not nested
    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }
}
