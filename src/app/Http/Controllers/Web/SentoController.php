<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SentoUpdateRequest;
use App\Http\Resources\SentoResource;
use App\Models\Sento;
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
        $page = $this->listService->paginate($filters, $request->user());

        return Inertia::render('Web/Sento/Index', [
            'sentos' => SentoResource::collection($page)->response()->getData(true),
            'filters' => $filters,
        ]);
    }

    public function show(Sento $sento): Response
    {
        $sento->load(['reviews.user', 'reviews.photos', 'photos']);
        return Inertia::render('Web/Sento/Show', [
            'sento' => (new SentoResource($sento))->resolve(),
        ]);
    }

    public function edit(Sento $sento): Response
    {
        $this->authorize('update', $sento);
        return Inertia::render('Web/Sento/Edit', [
            'sento' => (new SentoResource($sento))->resolve(),
        ]);
    }

    public function update(SentoUpdateRequest $request, Sento $sento): RedirectResponse
    {
        $this->authorize('update', $sento);
        $sento->fill($request->validated());
        $sento->is_manually_updated = true;
        $sento->info_updated_at = now()->toDateString();
        $sento->save();
        return redirect()->route('web.sentos.show', $sento)->with('status', '更新しました');
    }
}
