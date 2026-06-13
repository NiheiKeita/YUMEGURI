<?php

declare(strict_types=1);

namespace App\Services\Review;

use App\Models\SentoReview;
use App\Models\User;
use Carbon\Carbon;
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
        // 訪問日は日付（Y-m-d）に揃える。
        $visitedAt = Carbon::parse($data['visited_at'])->toDateString();
        $data['visited_at'] = $visitedAt;

        return DB::transaction(function () use ($user, $sentoId, $data, $visitedAt) {
            // updateOrCreate は完全一致比較なので、Eloquent の date cast が保存時に
            // datetime フォーマット（Y-m-d H:i:s）にする実装と相性が悪い。
            // whereDate で日付部分だけ比較して既存レコードを取り、明示的に分岐する。
            $existing = SentoReview::query()
                ->where('user_id', $user->id)
                ->where('sento_id', $sentoId)
                ->whereDate('visited_at', $visitedAt)
                ->first();

            if ($existing) {
                $existing->fill($data);
                $existing->save();
                return $existing;
            }

            return SentoReview::create([
                'user_id' => $user->id,
                'sento_id' => $sentoId,
                ...$data,
            ]);
        });
    }
}
