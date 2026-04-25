<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum UserRole: string
{
    case Admin = 'admin';
    case Member = 'member';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
