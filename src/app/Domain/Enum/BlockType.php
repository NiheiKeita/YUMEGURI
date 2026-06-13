<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum BlockType: string
{
    case Text = 'text';
    case Image = 'image';
}
