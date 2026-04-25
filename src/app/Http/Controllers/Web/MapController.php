<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    public function index(Request $request): Response
    {
        $viewer = $request->user();
        $sentos = Sento::query()
            ->operating()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(['id', 'name', 'prefecture', 'address', 'lat', 'lng'])
            ->get();

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
