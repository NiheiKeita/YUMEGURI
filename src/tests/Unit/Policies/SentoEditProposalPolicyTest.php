<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\SentoEditProposal;
use App\Models\User;
use App\Policies\SentoEditProposalPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoEditProposalPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_user_can_create_proposal(): void
    {
        $user = User::factory()->create();
        $this->assertTrue((new SentoEditProposalPolicy())->create($user));
    }

    public function test_only_admin_can_review_proposals(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $proposal = SentoEditProposal::factory()->create();

        $policy = new SentoEditProposalPolicy();
        $this->assertTrue($policy->review($admin, $proposal));
        $this->assertFalse($policy->review($member, $proposal));
    }

    public function test_only_admin_can_view_any(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $policy = new SentoEditProposalPolicy();
        $this->assertTrue($policy->viewAny($admin));
        $this->assertFalse($policy->viewAny($member));
    }
}
