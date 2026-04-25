<?php

declare(strict_types=1);

namespace App\Services\Sento;

use App\Models\Sento;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class NearbySentoService
{
    /**
     * 緯度経度から距離(km)順に未訪問銭湯を取得する。
     * Haversine 近似式を SQL で評価する（小規模なので空間関数は不要）。
     *
     * @return Collection<int, Sento>
     */
    public function findUnvisited(User $viewer, float $lat, float $lng, int $limit = 20): Collection
    {
        $haversine = '6371 * acos(cos(radians(?)) * cos(radians(lat))'
            . ' * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))';
        return Sento::query()
            ->operating()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereDoesntHave(
                'reviews',
                fn (Builder $q) => $q->where('user_id', $viewer->id),
            )
            ->selectRaw("sentos.*, ($haversine) AS distance_km", [$lat, $lng, $lat])
            ->orderBy('distance_km')
            ->limit($limit)
            ->get();
    }
}
