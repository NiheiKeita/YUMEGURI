<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum Prefecture: string
{
    case Tokyo = 'tokyo';
    case Kanagawa = 'kanagawa';
    case Saitama = 'saitama';
    case Chiba = 'chiba';

    public function jaName(): string
    {
        return match ($this) {
            self::Tokyo => '東京都',
            self::Kanagawa => '神奈川県',
            self::Saitama => '埼玉県',
            self::Chiba => '千葉県',
        };
    }
}
