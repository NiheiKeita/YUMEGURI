<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SentoPhoto;
use App\Models\User;

class SentoPhotoPolicy
{
    public function delete(User $user, SentoPhoto $photo): bool
    {
        return $user->id === $photo->user_id || $user->isAdmin();
    }
}
