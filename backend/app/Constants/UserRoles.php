<?php

namespace App\Constants;

class UserRoles
{
    public const PREPARER = 'preparer';
    public const APPROVER = 'approver';
    public const ADMIN    = 'admin';
    public const PENDING    = 'pending';


    public const ROLES = [
        self::PREPARER,
        self::APPROVER,
        self::ADMIN,
        self::PENDING,

    ];
}
