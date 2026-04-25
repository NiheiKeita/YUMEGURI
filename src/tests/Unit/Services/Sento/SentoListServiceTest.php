<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Sento;

use App\Domain\Enum\SentoStatus;
use App\Models\Sento;
use App\Models\SentoReview;
use App\Models\User;
use App\Services\Sento\SentoListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoListServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_excludes_permanently_closed(): void
    {
        Sento::factory()->create(['status' => SentoStatus::Open->value]);
        Sento::factory()->create(['status' => SentoStatus::ClosedPerm->value]);

        $page = (new SentoListService())->paginate([]);

        $this->assertSame(1, $page->total());
    }

    public function test_filters_by_visited_for_viewer(): void
    {
        $viewer = User::factory()->create();
        $visited = Sento::factory()->create();
        Sento::factory()->create(); // 未訪問

        SentoReview::factory()->create([
            'user_id' => $viewer->id,
            'sento_id' => $visited->id,
        ]);

        $service = new SentoListService();
        $page = $service->paginate(['visited' => 'visited'], $viewer);

        $this->assertSame(1, $page->total());
        $this->assertSame($visited->id, $page->items()[0]->id);
    }
}
