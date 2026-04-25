<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum PhotoCategory: string
{
    case Exterior = 'exterior';
    case Interior = 'interior';
    case Locker = 'locker';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Exterior => '外観',
            self::Interior => '内部',
            self::Locker => 'ロッカー',
            self::Other => 'その他',
        };
    }
}
