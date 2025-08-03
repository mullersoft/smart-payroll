<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
    'payroll_id',
    'transaction_type',
    'amount',
    'from_account',
    'to_account',
    'transaction_date',
    'processed_by',
];


    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
