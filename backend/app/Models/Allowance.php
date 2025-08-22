<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'is_taxable',
        'position_id',
        'employee_id'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_allowance');
    }
    public function payrolls()
    {
        return $this->belongsToMany(Payroll::class, 'payroll_allowance')
            ->withPivot(['value', 'is_taxable'])
            ->withTimestamps();
    }
}
