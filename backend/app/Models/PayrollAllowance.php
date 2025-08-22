<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
    protected $fillable = ['payroll_id', 'name', 'amount', 'is_taxable'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
