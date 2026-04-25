<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum ProposalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
