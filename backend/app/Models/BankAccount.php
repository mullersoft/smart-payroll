<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'owner_name',
        'balance',
        'employee_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // ✅ Add this method
    public function getOwnerDisplayNameAttribute()
    {
        return $this->employee ? $this->employee->full_name : $this->owner_name;
    }
}
