<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Proposal;

use App\Domain\Enum\ProposalStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\User;
use App\Services\Proposal\ProposalSubmitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalSubmitServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_pending_proposal_with_changes_and_reason(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $proposal = (new ProposalSubmitService())->execute(
            $user,
            $sento->id,
            ['hours' => '14:00-23:30'],
            '営業時間が変わったみたいです',
        );

        $this->assertSame(ProposalStatus::Pending, $proposal->status);
        $this->assertSame(['hours' => '14:00-23:30'], $proposal->changes);
        $this->assertSame('営業時間が変わったみたいです', $proposal->reason);
        $this->assertSame($user->id, $proposal->proposed_by);
        $this->assertSame($sento->id, $proposal->sento_id);
        $this->assertSame(1, SentoEditProposal::count());
    }

    public function test_allows_null_reason(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $proposal = (new ProposalSubmitService())->execute(
            $user,
            $sento->id,
            ['phone' => '03-1234-5678'],
            null,
        );

        $this->assertNull($proposal->reason);
    }
}
