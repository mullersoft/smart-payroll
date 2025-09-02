<?php

namespace App\Constants;

class UserStatus
{
    public const PENDING      = 'pending';
    public const ACTIVE       = 'active';
    public const DEACTIVATED  = 'deactivated';

    public const ALL = [
        self::PENDING,
        self::ACTIVE,
        self::DEACTIVATED,
    ];
}
