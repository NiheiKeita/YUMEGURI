<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SentoEditProposalStoreRequest;
use App\Http\Resources\SentoResource;
use App\Models\Sento;
use App\Services\Proposal\ProposalSubmitService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SentoEditProposalController extends Controller
{
    public function __construct(
        private readonly ProposalSubmitService $submitService,
    ) {
    }

    public function create(Sento $sento): Response
    {
        return Inertia::render('Web/Sento/Propose', [
            'sento' => (new SentoResource($sento))->resolve(),
        ]);
    }

    public function store(SentoEditProposalStoreRequest $request, Sento $sento): RedirectResponse
    {
        $validated = $request->validated();
        // 空の値を除いて差分だけを送る
        $changes = array_filter(
            $validated['changes'],
            fn ($v) => $v !== null && $v !== ''
        );
        $this->submitService->execute(
            $request->user(),
            $sento->id,
            $changes,
            $validated['reason'] ?? null,
        );
        return redirect()->route('web.sentos.show', $sento)
            ->with('status', '編集提案を送信しました');
    }
}
