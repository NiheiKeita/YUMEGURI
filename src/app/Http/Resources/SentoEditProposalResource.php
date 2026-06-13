<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SentoEditProposal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SentoEditProposal
 */
class SentoEditProposalResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sento_id' => $this->sento_id,
            'sento' => $this->whenLoaded('sento', fn () => [
                'id' => $this->sento->id,
                'name' => $this->sento->name,
            ]),
            'proposer' => $this->whenLoaded('proposer', fn () => [
                'id' => $this->proposer->id,
                'name' => $this->proposer->name,
            ]),
            'changes' => $this->changes,
            'reason' => $this->reason,
            'status' => $this->status->value,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
