<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sento;
use App\Models\User;

class SentoPolicy
{
    // 銭湯情報の直接編集は admin のみ
    public function update(User $user, Sento $_sento): bool
    {
        return $user->isAdmin();
    }

    public function propose(User $_user, Sento $_sento): bool
    {
        return true;
    }
}
