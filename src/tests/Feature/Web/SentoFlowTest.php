<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Domain\Enum\ProposalStatus;
use App\Models\Sento;
use App\Models\SentoEditProposal;
use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_page_renders_with_latest_reviews(): void
    {
        $sento = Sento::factory()->create();
        SentoReview::factory()->create(['sento_id' => $sento->id]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Top')
                ->has('latestReviews', 1));
    }

    public function test_sentos_index_returns_list(): void
    {
        Sento::factory(3)->create();
        $this->get('/sentos')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Web/Sento/Index'));
    }

    public function test_sento_show_returns_detail(): void
    {
        $sento = Sento::factory()->create();
        $this->get("/sentos/{$sento->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Sento/Show')
                ->where('sento.id', $sento->id));
    }

    public function test_review_store_creates_review(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($user)
            ->post("/sentos/{$sento->id}/review", [
                'visited_at' => '2026-04-20',
                'rating' => 5,
                'body' => 'よかった',
                'has_sauna' => true,
                'sauna_temp' => 90,
                'has_mizuburo' => true,
                'mizuburo_temp' => 18,
                'bath_types' => ['炭酸泉'],
                'want_revisit' => true,
            ])
            ->assertRedirect("/sentos/{$sento->id}");

        $this->assertDatabaseHas('sento_reviews', [
            'user_id' => $user->id,
            'sento_id' => $sento->id,
            'rating' => 5,
        ]);
    }

    public function test_review_validation_rejects_out_of_range_rating(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($user)
            ->post("/sentos/{$sento->id}/review", [
                'visited_at' => '2026-04-20',
                'rating' => 6, // 範囲外
            ])
            ->assertSessionHasErrors('rating');
    }

    public function test_proposal_store_creates_pending_proposal(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($user)
            ->post("/sentos/{$sento->id}/propose", [
                'changes' => ['hours' => '14:00-23:30'],
                'reason' => '時間が変わった',
            ])
            ->assertRedirect("/sentos/{$sento->id}");

        $this->assertDatabaseHas('sento_edit_proposals', [
            'sento_id' => $sento->id,
            'proposed_by' => $user->id,
            'status' => ProposalStatus::Pending->value,
        ]);
    }

    public function test_member_cannot_directly_edit_sento(): void
    {
        $member = User::factory()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($member)
            ->get("/sentos/{$sento->id}/edit")
            ->assertForbidden();
    }

    public function test_admin_can_directly_edit_sento(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create(['hours' => 'old']);

        $this->actingAs($admin)
            ->patch("/sentos/{$sento->id}", ['hours' => 'new'])
            ->assertRedirect("/sentos/{$sento->id}");

        $sento->refresh();
        $this->assertSame('new', $sento->hours);
        $this->assertTrue($sento->is_manually_updated);
    }

    public function test_review_form_requires_login(): void
    {
        $sento = Sento::factory()->create();
        $this->get("/sentos/{$sento->id}/review")
            ->assertRedirect();
    }

    public function test_review_form_renders_existing_review_for_user(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();
        SentoReview::factory()->create([
            'user_id' => $user->id,
            'sento_id' => $sento->id,
            'rating' => 4,
        ]);

        $this->actingAs($user)
            ->get("/sentos/{$sento->id}/review")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Sento/Review')
                ->where('review.rating', 4));
    }

    public function test_propose_form_renders(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($user)
            ->get("/sentos/{$sento->id}/propose")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Web/Sento/Propose'));
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create();

        $this->actingAs($admin)
            ->get("/sentos/{$sento->id}/edit")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Web/Sento/Edit'));
    }

    public function test_proposal_changes_keys_are_allowlisted(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        // 許可外のキー（changes.lat 等）はバリデーションで黙って落ちるべき
        $this->actingAs($user)
            ->post("/sentos/{$sento->id}/propose", [
                'changes' => [
                    'lat' => 99.0, // 許可されていない
                    'name' => '新しい名前',
                ],
            ])
            ->assertRedirect("/sentos/{$sento->id}");

        $proposal = SentoEditProposal::query()->firstOrFail();
        $this->assertArrayNotHasKey('lat', $proposal->changes);
        $this->assertSame('新しい名前', $proposal->changes['name']);
    }
}
