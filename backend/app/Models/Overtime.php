<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'hours',
        'rate_type', // e.g. "weekday_evening", "night", "rest_day", "holiday"
        'multiplier', // e.g. 1.25, 1.5, 2.0, 2.5
        'amount',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
