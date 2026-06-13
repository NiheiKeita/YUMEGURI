<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\SentoPhotoResource;
use App\Http\Resources\SentoReviewResource;
use App\Models\User;
use App\Services\Sento\NearbySentoService;
use App\Http\Resources\SentoResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function __construct(
        private readonly NearbySentoService $nearby,
    ) {
    }

    public function show(Request $request, User $user): Response
    {
        $stats = [
            'visited_count' => $user->sentoReviews()->count(),
            'prefecture_count' => $user->sentoReviews()
                ->join('sentos', 'sentos.id', '=', 'sento_reviews.sento_id')
                ->distinct()->count('sentos.prefecture'),
            'city_count' => $user->sentoReviews()
                ->join('sentos', 'sentos.id', '=', 'sento_reviews.sento_id')
                ->whereNotNull('sentos.city')
                ->distinct()->count('sentos.city'),
        ];

        $reviews = $user->sentoReviews()
            ->with(['sento', 'photos'])
            ->latest('visited_at')
            ->limit(60)
            ->get();

        $posts = $user->posts()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('visited_at')
            ->get();

        return Inertia::render('Web/User/Show', [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'reviews' => SentoReviewResource::collection($reviews)->resolve(),
            'posts' => $posts->map(fn ($p) => (new PostResource($p))->resolve())->values()->all(),
            'stats' => $stats,
            'canEditProfile' => $request->user()?->id === $user->id,
        ]);
    }

    public function edit(Request $request, User $user): Response
    {
        abort_unless($request->user()?->id === $user->id, 403);

        return Inertia::render('Web/User/Edit', [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tel' => $user->tel,
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()
            ->route('web.users.show', $user)
            ->with('message', 'プロフィールを更新しました');
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
                'visited_at' => $r->visited_at->toDateString(),
                'rating' => $r->rating,
            ])->filter(fn ($p) => $p['lat'] !== 0.0 && $p['lng'] !== 0.0)->values(),
        ]);
    }

    public function nearby(Request $request, User $user): Response
    {
        $viewer = $request->user();
        abort_unless($viewer instanceof User && $viewer->id === $user->id, 404);

        $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
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
        $photos = $user->sentoPhotos()->with('sento')->latest()->limit(120)->get();
        return Inertia::render('Web/User/Photos', [
            'profile' => ['id' => $user->id, 'name' => $user->name],
            'photos' => SentoPhotoResource::collection($photos)->resolve(),
        ]);
    }
}
