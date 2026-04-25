<?php

declare(strict_types=1);

namespace App\Services\Sento;

use App\Models\Sento;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

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
            $query->whereHas('reviews', function (Builder $q) use ($filters) {
                foreach ($filters['bath_types'] as $type) {
                    $q->whereJsonContains('bath_types', $type);
                }
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
            'visited_at' => $query->orderByDesc(
                $this->latestVisitForViewerSubquery($viewer)
            )->paginate($perPage),
            'want_revisit' => $query->withCount(['reviews as want_revisit_count' => function ($q) {
                $q->where('want_revisit', true);
            }])->orderByDesc('want_revisit_count')->paginate($perPage),
            default => $query->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->paginate($perPage),
        };
    }

    private function latestVisitForViewerSubquery(?User $viewer): \Illuminate\Database\Query\Builder
    {
        $sub = \DB::table('sento_reviews')
            ->select(\DB::raw('MAX(visited_at)'))
            ->whereColumn('sento_reviews.sento_id', 'sentos.id');
        if ($viewer) {
            $sub->where('user_id', $viewer->id);
        }
        return $sub;
    }
}
