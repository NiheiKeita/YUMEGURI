<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Domain\Enum\SentoStatus;
use App\Models\Sento;
use App\Models\SentoPhoto;
use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapAndNearbyTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_returns_pins_with_visited_flag(): void
    {
        $viewer = User::factory()->create();
        $visited = Sento::factory()->create(['lat' => 35.68, 'lng' => 139.77]);
        $unvisited = Sento::factory()->create(['lat' => 35.69, 'lng' => 139.78]);
        Sento::factory()->create(['lat' => null, 'lng' => null]); // 除外
        Sento::factory()->create([
            'lat' => 35.7, 'lng' => 139.8,
            'status' => SentoStatus::ClosedPerm->value,
        ]); // 除外
        SentoReview::factory()->create([
            'user_id' => $viewer->id, 'sento_id' => $visited->id,
        ]);

        $this->actingAs($viewer)
            ->get('/map')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Map')
                ->has('pins', 2)
                ->where('pins.0.visited', true)
                ->where('pins.1.visited', false));
    }

    public function test_map_filters_by_prefecture(): void
    {
        Sento::factory()->create(['lat' => 35.68, 'lng' => 139.77, 'prefecture' => '東京都']);
        Sento::factory()->create(['lat' => 35.5, 'lng' => 139.6, 'prefecture' => '神奈川県']);

        // 日本語 query は URL エンコードして渡す
        $url = '/map?' . http_build_query(['prefecture' => '東京都']);
        $this->get($url)
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('pins', 1));
    }

    public function test_nearby_returns_unvisited_sentos(): void
    {
        $user = User::factory()->create();
        $unvisited = Sento::factory()->create(['lat' => 35.681, 'lng' => 139.768]);
        $visited = Sento::factory()->create(['lat' => 35.682, 'lng' => 139.769]);
        SentoReview::factory()->create([
            'user_id' => $user->id, 'sento_id' => $visited->id,
        ]);

        $this->actingAs($user)
            ->get('/nearby?lat=35.681&lng=139.767')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Web/Nearby')
                ->has('sentos', 1)
                ->where('sentos.0.id', $unvisited->id));
    }

    public function test_nearby_validates_coordinates(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/nearby?lat=999&lng=0')
            ->assertSessionHasErrors('lat');
    }
}
