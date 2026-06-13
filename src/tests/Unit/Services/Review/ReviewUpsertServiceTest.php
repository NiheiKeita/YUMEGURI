<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Review;

use App\Models\Sento;
use App\Models\SentoReview;
use App\Models\User;
use App\Services\Review\ReviewUpsertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewUpsertServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_review_when_no_existing(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();

        $service = new ReviewUpsertService();
        $review = $service->execute($user, $sento->id, [
            'visited_at' => '2026-04-20',
            'rating' => 5,
            'body' => '良かった',
        ]);

        $this->assertSame(5, $review->rating);
        $this->assertSame(1, SentoReview::count());
    }

    public function test_updates_existing_review_for_same_visit_date(): void
    {
        $user = User::factory()->create();
        $sento = Sento::factory()->create();
        SentoReview::factory()->create([
            'user_id' => $user->id,
            'sento_id' => $sento->id,
            'visited_at' => '2026-04-20',
            'rating' => 3,
        ]);

        $service = new ReviewUpsertService();
        $service->execute($user, $sento->id, [
            'visited_at' => '2026-04-20',
            'rating' => 5,
        ]);

        $this->assertSame(1, SentoReview::count());
        $this->assertSame(5, SentoReview::first()->rating);
    }
}
