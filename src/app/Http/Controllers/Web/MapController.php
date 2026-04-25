<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sento;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    public function index(Request $request): Response
    {
        // TODO: 都県数が増えたらピン数が 1000+ になり Inertia payload が肥大する。
        //       bbox / prefecture フィルタを受けるか、別 API エンドポイントから fetch する設計に分離する。
        // YUMEGURI ドメインに属さない AdminUser ログイン状態では viewer を null 扱いにする
        $viewer = $request->user() instanceof User ? $request->user() : null;
        $query = Sento::query()
            ->operating()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(['id', 'name', 'prefecture', 'address', 'lat', 'lng']);

        if ($prefecture = $request->string('prefecture')->toString()) {
            $query->inPrefecture($prefecture);
        }

        $sentos = $query->limit(2000)->get();

        $visitedIds = $viewer
            ? $viewer->sentoReviews()->pluck('sento_id')->unique()->values()
            : collect();

        return Inertia::render('Web/Map', [
            'pins' => $sentos->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'lat' => (float) $s->lat,
                'lng' => (float) $s->lng,
                'visited' => $visitedIds->contains($s->id),
            ])->values(),
        ]);
    }
}
