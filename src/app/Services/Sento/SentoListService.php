<?php

declare(strict_types=1);

namespace App\Services\Sento;

use App\Models\Sento;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

final class SentoListService
{
    /**
     * 一覧画面の絞り込み・ソートを集約する。
     * Controller を薄く保つため、フィルタの組み立ては全部ここで行う。
     *
     * @param array{
     *     prefecture?: string|null,
     *     city?: string|null,
     *     visited?: string|null,
     *     has_sauna?: bool|null,
     *     has_mizuburo?: bool|null,
     *     has_shampoo?: bool|null,
     *     has_soap?: bool|null,
     *     bath_types?: list<string>|null,
     *     status?: string|null,
     *     sort?: string|null
     * } $filters
     */
    public function paginate(array $filters, ?User $viewer = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Sento::query()->operating();

        if (!empty($filters['prefecture'])) {
            $query->inPrefecture($filters['prefecture']);
        }
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        if (!empty($filters['has_shampoo'])) {
            $query->where('has_shampoo', true);
        }
        if (!empty($filters['has_soap'])) {
            $query->where('has_soap', true);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['has_sauna'])) {
            $query->whereHas('reviews', fn (Builder $q) => $q->where('has_sauna', true));
        }
        if (!empty($filters['has_mizuburo'])) {
            $query->whereHas('reviews', fn (Builder $q) => $q->where('has_mizuburo', true));
        }
        if (!empty($filters['bath_types'])) {
            // OR セマンティクス: チェックされたお湯のいずれか1つでも含むレビューがあれば該当
            // （ユーザがチェックした項目が増えるほど結果が広がる、典型的なフィルタ UX）
            $query->whereHas('reviews', function (Builder $q) use ($filters) {
                $q->where(function (Builder $inner) use ($filters) {
                    foreach ($filters['bath_types'] as $type) {
                        $inner->orWhereJsonContains('bath_types', $type);
                    }
                });
            });
        }

        if ($viewer && ($filters['visited'] ?? null) === 'visited') {
            $query->whereHas('reviews', fn (Builder $q) => $q->where('user_id', $viewer->id));
        }
        if ($viewer && ($filters['visited'] ?? null) === 'unvisited') {
            $query->whereDoesntHave(
                'reviews',
                fn (Builder $q) => $q->where('user_id', $viewer->id),
            );
        }

        return match ($filters['sort'] ?? 'rating') {
            'visited_at' => $this->orderByVisitedAt($query, $viewer)->paginate($perPage),
            'want_revisit' => $query->withCount(['reviews as want_revisit_count' => function ($q) {
                $q->where('want_revisit', true);
            }])->orderByDesc('want_revisit_count')->paginate($perPage),
            default => $query->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->paginate($perPage),
        };
    }

    /**
     * @param Builder<Sento> $query
     * @return Builder<Sento>
     */
    private function orderByVisitedAt(Builder $query, ?User $viewer): Builder
    {
        // 未ログイン時は「自分の訪問日」が定義できないので created_at で代替
        if ($viewer === null) {
            return $query->orderByDesc('created_at');
        }
        return $query->orderByDesc($this->latestVisitForViewerSubquery($viewer));
    }

    private function latestVisitForViewerSubquery(User $viewer): QueryBuilder
    {
        return DB::table('sento_reviews')
            ->select(DB::raw('MAX(visited_at)'))
            ->whereColumn('sento_reviews.sento_id', 'sentos.id')
            ->where('user_id', $viewer->id);
    }
}
