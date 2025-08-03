<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
    'full_name',
    'employee_id',
    'position',
    'employment_type',
    'base_salary',
    // 'allowance',
    // 'deduction',
    'gender',             
    'employment_date',    
    // 'bank_account',
];
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    public function bankAccount()
   {
       return $this->hasOne(BankAccount::class, 'employee_id');
   }
   public function user() {
    return $this->hasOne(User::class);
}
}
