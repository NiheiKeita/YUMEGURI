<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sento;
use App\Models\User;

class SentoPolicy
{
    // 銭湯情報の直接編集は admin のみ
    public function update(User $user, Sento $sento): bool
    {
        return $user->isAdmin();
    }

    public function propose(User $user, Sento $sento): bool
    {
        return true;
    }
}
