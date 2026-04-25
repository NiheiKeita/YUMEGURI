<?php

declare(strict_types=1);

namespace App\Services\Review;

use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ReviewUpsertService
{
    /**
     * 訪問記録を upsert する。1ユーザー × 1銭湯 × 1訪問日 で重複しない設計。
     *
     * @param array<string, mixed> $data
     */
    public function execute(User $user, int $sentoId, array $data): SentoReview
    {
        return DB::transaction(function () use ($user, $sentoId, $data) {
            return SentoReview::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sento_id' => $sentoId,
                    'visited_at' => $data['visited_at'],
                ],
                $data,
            );
        });
    }
}
