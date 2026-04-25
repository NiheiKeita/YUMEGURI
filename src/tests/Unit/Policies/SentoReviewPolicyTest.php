<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\SentoReview;
use App\Models\User;
use App\Policies\SentoReviewPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_user_can_create_review(): void
    {
        $user = User::factory()->create();
        $this->assertTrue((new SentoReviewPolicy())->create($user));
    }

    public function test_owner_can_update_their_review(): void
    {
        $user = User::factory()->create();
        $review = SentoReview::factory()->create(['user_id' => $user->id]);
        $this->assertTrue((new SentoReviewPolicy())->update($user, $review));
    }

    public function test_other_user_cannot_update_someone_elses_review(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $review = SentoReview::factory()->create(['user_id' => $other->id]);
        $this->assertFalse((new SentoReviewPolicy())->update($user, $review));
    }

    public function test_admin_can_update_any_review(): void
    {
        $admin = User::factory()->admin()->create();
        $review = SentoReview::factory()->create();
        $this->assertTrue((new SentoReviewPolicy())->update($admin, $review));
    }

    public function test_owner_can_delete(): void
    {
        $user = User::factory()->create();
        $review = SentoReview::factory()->create(['user_id' => $user->id]);
        $this->assertTrue((new SentoReviewPolicy())->delete($user, $review));
    }

    public function test_other_user_cannot_delete(): void
    {
        $user = User::factory()->create();
        $review = SentoReview::factory()->create();
        $this->assertFalse((new SentoReviewPolicy())->delete($user, $review));
    }
}
