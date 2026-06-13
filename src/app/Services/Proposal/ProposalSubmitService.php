<?php

declare(strict_types=1);

namespace App\Services\Proposal;

use App\Domain\Enum\ProposalStatus;
use App\Models\SentoEditProposal;
use App\Models\User;

final class ProposalSubmitService
{
    /**
     * @param array<string, mixed> $changes
     */
    public function execute(User $user, int $sentoId, array $changes, ?string $reason): SentoEditProposal
    {
        return SentoEditProposal::create([
            'sento_id' => $sentoId,
            'proposed_by' => $user->id,
            'changes' => $changes,
            'reason' => $reason,
            'status' => ProposalStatus::Pending->value,
        ]);
    }
}
