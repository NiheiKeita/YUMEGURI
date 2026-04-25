<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SentoPhotoResource;
use App\Http\Resources\SentoReviewResource;
use App\Models\User;
use App\Services\Sento\NearbySentoService;
use App\Http\Resources\SentoResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function __construct(
        private readonly NearbySentoService $nearby,
    ) {
    }

    public function show(User $user): Response
    {
        $reviews = $user->sentoReviews()
            ->with(['sento', 'photos'])
            ->latest('visited_at')
            ->get();

        $stats = [
            'visited_count' => $reviews->count(),
            'prefecture_count' => $reviews->pluck('sento.prefecture')->unique()->count(),
            'city_count' => $reviews->pluck('sento.city')->filter()->unique()->count(),
        ];

        return Inertia::render('Web/User/Show', [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'reviews' => SentoReviewResource::collection($reviews)->resolve(),
            'stats' => $stats,
        ]);
    }

    public function map(User $user): Response
    {
        $reviews = $user->sentoReviews()->with('sento')->get();
        return Inertia::render('Web/User/Map', [
            'profile' => ['id' => $user->id, 'name' => $user->name],
            'pins' => $reviews->map(fn ($r) => [
                'id' => $r->sento->id,
                'name' => $r->sento->name,
                'lat' => (float) $r->sento->lat,
                'lng' => (float) $r->sento->lng,
                'visited_at' => $r->visited_at?->toDateString(),
                'rating' => $r->rating,
            ])->filter(fn ($p) => $p['lat'] !== 0.0 && $p['lng'] !== 0.0)->values(),
        ]);
    }

    public function nearby(Request $request, User $user): Response
    {
        // 仕様: 自分のページのみ表示、友達のページでは非表示
        abort_if($request->user()?->id !== $user->id, 404);

        $request->validate([
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        $lat = (float) $request->input('lat', 35.6812);
        $lng = (float) $request->input('lng', 139.7671);
        $sentos = $this->nearby->findUnvisited($user, $lat, $lng, 20);

        return Inertia::render('Web/User/Nearby', [
            'profile' => ['id' => $user->id, 'name' => $user->name],
            'origin' => ['lat' => $lat, 'lng' => $lng],
            'sentos' => SentoResource::collection($sentos)->resolve(),
        ]);
    }

    public function photos(User $user): Response
    {
        $photos = $user->sentoPhotos()->with('sento')->latest()->get();
        return Inertia::render('Web/User/Photos', [
            'profile' => ['id' => $user->id, 'name' => $user->name],
            'photos' => SentoPhotoResource::collection($photos)->resolve(),
        ]);
    }
}
