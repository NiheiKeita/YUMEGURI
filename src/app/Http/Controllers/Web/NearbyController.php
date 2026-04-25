<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SentoResource;
use App\Models\User;
use App\Services\Sento\NearbySentoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NearbyController extends Controller
{
    public function __construct(
        private readonly NearbySentoService $nearby,
    ) {
    }

    public function index(Request $request): Response
    {
        $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        $lat = (float) $request->input('lat', 35.6812);
        $lng = (float) $request->input('lng', 139.7671);
        $user = $request->user();
        $sentos = $user instanceof User
            ? $this->nearby->findUnvisited($user, $lat, $lng, 20)
            : collect();

        return Inertia::render('Web/Nearby', [
            'origin' => ['lat' => $lat, 'lng' => $lng],
            'sentos' => SentoResource::collection($sentos)->resolve(),
        ]);
    }
}
