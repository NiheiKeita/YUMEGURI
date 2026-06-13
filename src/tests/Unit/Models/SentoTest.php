<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Domain\Enum\ProposalStatus;
use App\Domain\Enum\SentoStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\SentoPhoto;
use App\Models\SentoReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_operating_scope_excludes_closed_perm(): void
    {
        Sento::factory()->create(['status' => SentoStatus::Open->value]);
        Sento::factory()->create(['status' => SentoStatus::ClosedTemp->value]);
        Sento::factory()->create(['status' => SentoStatus::ClosedPerm->value]);

        $this->assertSame(2, Sento::operating()->count());
    }

    public function test_in_prefecture_scope(): void
    {
        Sento::factory()->create(['prefecture' => '東京都']);
        Sento::factory()->create(['prefecture' => '千葉県']);

        $this->assertSame(1, Sento::query()->inPrefecture('東京都')->count());
    }

    public function test_relationships(): void
    {
        $sento = Sento::factory()->create();
        SentoReview::factory(2)->create(['sento_id' => $sento->id]);
        SentoPhoto::factory(3)->create(['sento_id' => $sento->id]);
        SentoEditProposal::factory()->create(['sento_id' => $sento->id]);

        $this->assertCount(2, $sento->reviews);
        $this->assertCount(3, $sento->photos);
        $this->assertCount(1, $sento->editProposals);
    }

    public function test_sento_edit_proposal_pending_scope(): void
    {
        SentoEditProposal::factory()->create(['status' => ProposalStatus::Pending->value]);
        SentoEditProposal::factory()->create(['status' => ProposalStatus::Approved->value]);
        SentoEditProposal::factory()->create(['status' => ProposalStatus::Rejected->value]);

        $this->assertSame(1, SentoEditProposal::pending()->count());
    }
}
