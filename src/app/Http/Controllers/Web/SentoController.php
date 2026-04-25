<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SentoUpdateRequest;
use App\Http\Resources\SentoResource;
use App\Models\Sento;
use App\Models\User;
use App\Services\Sento\SentoListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SentoController extends Controller
{
    public function __construct(
        private readonly SentoListService $listService,
    ) {
    }

    public function index(Request $request): Response
    {
        $filters = $request->only([
            'prefecture', 'city', 'visited', 'has_sauna', 'has_mizuburo',
            'has_shampoo', 'has_soap', 'bath_types', 'status', 'sort',
        ]);
        // SentoListService は User のみ理解する（AdminUser は viewer 扱いしない）
        $viewer = $request->user() instanceof User ? $request->user() : null;
        $page = $this->listService->paginate($filters, $viewer);

        return Inertia::render('Web/Sento/Index', [
            'sentos' => SentoResource::collection($page)->response()->getData(true),
            'filters' => $filters,
        ]);
    }

    public function show(Sento $sento): Response
    {
        // 詳細ページに表示する最新レビューは上限を設ける（人気銭湯で payload が爆発するのを防ぐ）
        $sento->load([
            'reviews' => fn ($q) => $q->latest('visited_at')->limit(50),
            'reviews.user',
            'reviews.photos',
            'photos' => fn ($q) => $q->latest()->limit(30),
        ]);
        return Inertia::render('Web/Sento/Show', [
            'sento' => (new SentoResource($sento))->resolve(),
        ]);
    }

    // edit / update は admin middleware で gate 済みなので Policy 認可は不要
    public function edit(Sento $sento): Response
    {
        return Inertia::render('Web/Sento/Edit', [
            'sento' => (new SentoResource($sento))->resolve(),
        ]);
    }

    public function update(SentoUpdateRequest $request, Sento $sento): RedirectResponse
    {
        $sento->fill($request->validated());
        $sento->is_manually_updated = true;
        $sento->info_updated_at = now();
        $sento->save();
        return redirect()->route('web.sentos.show', $sento)->with('status', '更新しました');
    }
}
