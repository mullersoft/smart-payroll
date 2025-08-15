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
        'status',
        'failure_reason',
    ];


    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function fromBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from_account', 'account_number');
    }

    public function toBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to_account', 'account_number');
    }
}
