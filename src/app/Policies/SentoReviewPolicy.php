<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SentoReview;
use App\Models\User;

class SentoReviewPolicy
{
    public function create(User $_user): bool
    {
        return true;
    }

    public function update(User $user, SentoReview $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }

    public function delete(User $user, SentoReview $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }
}
