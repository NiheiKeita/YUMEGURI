<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Sento;

use App\Domain\Enum\SentoStatus;
use App\Models\Sento;
use App\Models\SentoReview;
use App\Models\User;
use App\Services\Sento\NearbySentoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NearbySentoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_by_distance_ascending(): void
    {
        $viewer = User::factory()->create();
        // 中心 (35.681, 139.767) を東京駅付近として、近い順に配置
        $near = Sento::factory()->create(['lat' => 35.681, 'lng' => 139.768]);
        $mid = Sento::factory()->create(['lat' => 35.690, 'lng' => 139.770]);
        $far = Sento::factory()->create(['lat' => 35.800, 'lng' => 139.900]);

        $result = (new NearbySentoService())
            ->findUnvisited($viewer, 35.681, 139.767, 10);

        $this->assertSame(
            [$near->id, $mid->id, $far->id],
            $result->pluck('id')->all(),
        );
    }

    public function test_excludes_already_visited_sentos(): void
    {
        $viewer = User::factory()->create();
        $visited = Sento::factory()->create(['lat' => 35.68, 'lng' => 139.77]);
        $unvisited = Sento::factory()->create(['lat' => 35.69, 'lng' => 139.78]);
        SentoReview::factory()->create([
            'user_id' => $viewer->id,
            'sento_id' => $visited->id,
        ]);

        $result = (new NearbySentoService())
            ->findUnvisited($viewer, 35.681, 139.767, 10);

        $this->assertSame([$unvisited->id], $result->pluck('id')->all());
    }

    public function test_skips_sentos_without_coordinates(): void
    {
        $viewer = User::factory()->create();
        Sento::factory()->create(['lat' => null, 'lng' => null]);
        $valid = Sento::factory()->create(['lat' => 35.68, 'lng' => 139.77]);

        $result = (new NearbySentoService())
            ->findUnvisited($viewer, 35.681, 139.767, 10);

        $this->assertSame([$valid->id], $result->pluck('id')->all());
    }

    public function test_excludes_permanently_closed(): void
    {
        $viewer = User::factory()->create();
        Sento::factory()->create([
            'lat' => 35.68, 'lng' => 139.77,
            'status' => SentoStatus::ClosedPerm->value,
        ]);
        $open = Sento::factory()->create([
            'lat' => 35.69, 'lng' => 139.78,
            'status' => SentoStatus::Open->value,
        ]);

        $result = (new NearbySentoService())
            ->findUnvisited($viewer, 35.681, 139.767, 10);

        $this->assertSame([$open->id], $result->pluck('id')->all());
    }

    public function test_respects_limit(): void
    {
        $viewer = User::factory()->create();
        Sento::factory(5)->create(['lat' => 35.68, 'lng' => 139.77]);

        $result = (new NearbySentoService())
            ->findUnvisited($viewer, 35.681, 139.767, 3);

        $this->assertCount(3, $result);
    }
}
