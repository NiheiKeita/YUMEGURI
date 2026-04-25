<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Proposal;

use App\Domain\Enum\ProposalStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\User;
use App\Services\Proposal\ProposalReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ProposalReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_applies_changes_and_marks_manually_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create(['hours' => '15:00-23:00']);
        $proposal = SentoEditProposal::factory()->create([
            'sento_id' => $sento->id,
            'changes' => ['hours' => '14:00-23:30'],
        ]);

        $service = new ProposalReviewService();
        $result = $service->approve($admin, $proposal);

        $this->assertSame(ProposalStatus::Approved, $result->status);
        $this->assertSame('14:00-23:30', $sento->fresh()->hours);
        $this->assertTrue($sento->fresh()->is_manually_updated);
    }

    public function test_reject_does_not_change_sento(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create(['hours' => '15:00-23:00']);
        $proposal = SentoEditProposal::factory()->create([
            'sento_id' => $sento->id,
            'changes' => ['hours' => '14:00-23:30'],
        ]);

        $service = new ProposalReviewService();
        $result = $service->reject($admin, $proposal);

        $this->assertSame(ProposalStatus::Rejected, $result->status);
        $this->assertSame('15:00-23:00', $sento->fresh()->hours);
        $this->assertFalse($sento->fresh()->is_manually_updated);
    }

    public function test_cannot_review_already_processed_proposal(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create();
        $proposal = SentoEditProposal::factory()->create([
            'sento_id' => $sento->id,
            'status' => ProposalStatus::Approved->value,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->expectException(RuntimeException::class);
        (new ProposalReviewService())->approve($admin, $proposal);
    }
}
