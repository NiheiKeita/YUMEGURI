<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Enum\ProposalStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_view_proposals_index(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/proposals')
            ->assertForbidden();
    }

    public function test_admin_sees_proposals_index(): void
    {
        $admin = User::factory()->admin()->create();
        SentoEditProposal::factory()->count(2)->create();

        $this->actingAs($admin)
            ->get('/admin/proposals')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Proposals/Index'));
    }

    public function test_approve_applies_changes_and_marks_manually_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create(['hours' => 'old']);
        $proposal = SentoEditProposal::factory()->create([
            'sento_id' => $sento->id,
            'changes' => ['hours' => 'new'],
        ]);

        $this->actingAs($admin)
            ->post("/admin/proposals/{$proposal->id}/approve")
            ->assertRedirect();

        $this->assertSame('new', $sento->fresh()->hours);
        $this->assertTrue($sento->fresh()->is_manually_updated);
        $this->assertSame(ProposalStatus::Approved, $proposal->fresh()->status);
    }

    public function test_reject_does_not_change_sento(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create(['hours' => 'old']);
        $proposal = SentoEditProposal::factory()->create([
            'sento_id' => $sento->id,
            'changes' => ['hours' => 'new'],
        ]);

        $this->actingAs($admin)
            ->post("/admin/proposals/{$proposal->id}/reject")
            ->assertRedirect();

        $this->assertSame('old', $sento->fresh()->hours);
        $this->assertFalse($sento->fresh()->is_manually_updated);
        $this->assertSame(ProposalStatus::Rejected, $proposal->fresh()->status);
    }

    public function test_member_cannot_approve(): void
    {
        $member = User::factory()->create();
        $proposal = SentoEditProposal::factory()->create();

        $this->actingAs($member)
            ->post("/admin/proposals/{$proposal->id}/approve")
            ->assertForbidden();
    }
}
