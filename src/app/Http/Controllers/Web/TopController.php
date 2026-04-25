<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SentoReviewResource;
use App\Models\SentoReview;
use Inertia\Inertia;
use Inertia\Response;

class TopController extends Controller
{
    public function index(): Response
    {
        $latestReviews = SentoReview::query()
            ->with(['sento', 'user', 'photos'])
            ->latest('visited_at')
            ->limit(12)
            ->get();

        return Inertia::render('Web/Top', [
            'latestReviews' => SentoReviewResource::collection($latestReviews)->resolve(),
        ]);
    }
}
