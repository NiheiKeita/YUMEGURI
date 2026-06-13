<?php

declare(strict_types=1);

namespace App\Services\Proposal;

use App\Domain\Enum\ProposalStatus;
use App\Models\SentoEditProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class ProposalReviewService
{
    public function approve(User $admin, SentoEditProposal $proposal): SentoEditProposal
    {
        if ($proposal->status !== ProposalStatus::Pending) {
            throw new RuntimeException('既に処理済みの提案は変更できません');
        }
        return DB::transaction(function () use ($admin, $proposal) {
            $sento = $proposal->sento()->lockForUpdate()->firstOrFail();
            $sento->fill($proposal->changes);
            $sento->is_manually_updated = true;
            $sento->info_updated_at = now();
            $sento->save();

            $proposal->status = ProposalStatus::Approved;
            $proposal->reviewed_by = (int) $admin->id;
            $proposal->reviewed_at = now();
            $proposal->save();

            /** @var SentoEditProposal $fresh */
            $fresh = $proposal->fresh();
            return $fresh;
        });
    }

    public function reject(User $admin, SentoEditProposal $proposal): SentoEditProposal
    {
        if ($proposal->status !== ProposalStatus::Pending) {
            throw new RuntimeException('既に処理済みの提案は変更できません');
        }
        $proposal->status = ProposalStatus::Rejected;
        $proposal->reviewed_by = (int) $admin->id;
        $proposal->reviewed_at = now();
        $proposal->save();
        /** @var SentoEditProposal $fresh */
        $fresh = $proposal->fresh();
        return $fresh;
    }
}
