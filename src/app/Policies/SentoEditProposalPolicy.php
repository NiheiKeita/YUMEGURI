<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SentoEditProposal;
use App\Models\User;

class SentoEditProposalPolicy
{
    // 提案の作成は member 以上ならOK
    public function create(User $_user): bool
    {
        return true;
    }

    // 承認・却下は admin のみ
    public function review(User $user, SentoEditProposal $_proposal): bool
    {
        return $user->isAdmin();
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
