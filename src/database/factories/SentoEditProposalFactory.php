<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Enum\ProposalStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SentoEditProposal>
 */
class SentoEditProposalFactory extends Factory
{
    protected $model = SentoEditProposal::class;

    public function definition(): array
    {
        return [
            'sento_id' => Sento::factory(),
            'proposed_by' => User::factory(),
            'changes' => ['hours' => '14:00-23:30'],
            'reason' => fake()->sentence(),
            'status' => ProposalStatus::Pending->value,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
