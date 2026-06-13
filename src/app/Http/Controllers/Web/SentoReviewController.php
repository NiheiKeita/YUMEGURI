<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SentoReviewStoreRequest;
use App\Http\Resources\SentoResource;
use App\Http\Resources\SentoReviewResource;
use App\Models\Sento;
use App\Models\User;
use App\Services\Review\ReviewUpsertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SentoReviewController extends Controller
{
    public function __construct(
        private readonly ReviewUpsertService $upsertService,
    ) {
    }

    public function create(Request $request, Sento $sento): Response
    {
        /** @var User $user (auth middleware で guarantee 済み) */
        $user = $request->user();
        $existing = $sento->reviews()
            ->where('user_id', $user->id)
            ->latest('visited_at')
            ->first();

        return Inertia::render('Web/Sento/Review', [
            'sento' => (new SentoResource($sento))->resolve(),
            'review' => $existing ? (new SentoReviewResource($existing))->resolve() : null,
        ]);
    }

    public function store(SentoReviewStoreRequest $request, Sento $sento): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->upsertService->execute(
            $user,
            $sento->id,
            $request->validated(),
        );
        return redirect()->route('web.sentos.show', $sento)
            ->with('status', '記録を保存しました');
    }
}
