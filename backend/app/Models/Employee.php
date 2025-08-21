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
        'email',
        'position_id',
        'employment_type_id',
        'base_salary',
        'gender',
        'employment_date',
        'is_active'
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class, 'employee_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }
    public function allowances()
    {
        return $this->belongsToMany(Allowance::class, 'employee_allowance');
    }

    public function positionAllowancePosition()
    {
        return $this->belongsTo(Position::class, 'position_allowance_position_id');
    }
}
