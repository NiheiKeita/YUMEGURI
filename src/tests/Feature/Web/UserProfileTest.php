<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\Sento;
use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_show_returns_aggregated_stats(): void
    {
        $owner = User::factory()->create();
        $tokyoSento = Sento::factory()->create(['prefecture' => '東京都', 'city' => '中央区']);
        $kanagawaSento = Sento::factory()->create(['prefecture' => '神奈川県', 'city' => '横浜市']);
        SentoReview::factory()->create(['user_id' => $owner->id, 'sento_id' => $tokyoSento->id]);
        SentoReview::factory()->create(['user_id' => $owner->id, 'sento_id' => $kanagawaSento->id]);

        $this->actingAs($owner)
            ->get("/users/{$owner->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/User/Show')
                ->where('stats.visited_count', 2)
                ->where('stats.prefecture_count', 2)
                ->where('stats.city_count', 2));
    }

    public function test_nearby_only_visible_for_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get("/users/{$owner->id}/nearby")
            ->assertNotFound();

        $this->actingAs($owner)
            ->get("/users/{$owner->id}/nearby")
            ->assertOk();
    }

    public function test_user_map_visible_to_other_user(): void
    {
        $owner = User::factory()->create();
        $sento = Sento::factory()->create(['lat' => 35.68, 'lng' => 139.77]);
        SentoReview::factory()->create(['user_id' => $owner->id, 'sento_id' => $sento->id]);

        $other = User::factory()->create();
        $this->actingAs($other)
            ->get("/users/{$owner->id}/map")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/User/Map')
                ->has('pins', 1));
    }
}
