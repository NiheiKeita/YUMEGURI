<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SentoEditProposalResource;
use App\Models\SentoEditProposal;
use App\Models\User;
use App\Services\Proposal\ProposalReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProposalController extends Controller
{
    public function __construct(
        private readonly ProposalReviewService $reviewService,
    ) {
    }

    public function index(): Response
    {
        $proposals = SentoEditProposal::query()
            ->with(['sento', 'proposer', 'reviewer'])
            ->latest()
            ->paginate(30);

        return Inertia::render('Admin/Proposals/Index', [
            'proposals' => SentoEditProposalResource::collection($proposals)
                ->response()->getData(true),
        ]);
    }

    public function approve(Request $request, SentoEditProposal $proposal): RedirectResponse
    {
        /** @var User $admin (admin middleware で gate 済み) */
        $admin = $request->user();
        $this->reviewService->approve($admin, $proposal);
        return back()->with('status', '提案を反映しました');
    }

    public function reject(Request $request, SentoEditProposal $proposal): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();
        $this->reviewService->reject($admin, $proposal);
        return back()->with('status', '提案を却下しました');
    }
}
