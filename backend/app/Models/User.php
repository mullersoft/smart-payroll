<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper
    public function isApprover()
    {
        return $this->role === 'approver';
    }

    public function isPreparer()
    {
        return $this->role === 'preparer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function preparedPayrolls() {
    return $this->hasMany(Payroll::class, 'prepared_by');
}

public function approvedPayrolls() {
    return $this->hasMany(Payroll::class, 'approved_by');
}
}

