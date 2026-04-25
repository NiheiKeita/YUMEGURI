<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum SentoStatus: string
{
    case Open = 'open';
    case ClosedTemp = 'closed_temp';
    case ClosedPerm = 'closed_perm';

    public function label(): string
    {
        return match ($this) {
            self::Open => '営業中',
            self::ClosedTemp => '休業中',
            self::ClosedPerm => '廃業',
        };
    }
}
