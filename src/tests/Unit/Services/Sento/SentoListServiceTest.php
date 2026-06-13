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

        $page = (new SentoListService())->paginate(['visited' => 'visited'], $viewer);

        $this->assertSame(1, $page->total());
        $this->assertSame($visited->id, $page->items()[0]->id);
    }

    public function test_filters_by_unvisited_for_viewer(): void
    {
        $viewer = User::factory()->create();
        $visited = Sento::factory()->create();
        $unvisited = Sento::factory()->create();
        SentoReview::factory()->create([
            'user_id' => $viewer->id,
            'sento_id' => $visited->id,
        ]);

        $page = (new SentoListService())->paginate(['visited' => 'unvisited'], $viewer);

        $this->assertSame([$unvisited->id], collect($page->items())->pluck('id')->all());
    }

    public function test_filters_by_prefecture_and_city(): void
    {
        Sento::factory()->create(['prefecture' => '東京都', 'city' => '中央区']);
        Sento::factory()->create(['prefecture' => '東京都', 'city' => '杉並区']);
        Sento::factory()->create(['prefecture' => '神奈川県', 'city' => '横浜市']);

        $service = new SentoListService();
        $tokyo = $service->paginate(['prefecture' => '東京都']);
        $chuo = $service->paginate(['prefecture' => '東京都', 'city' => '中央区']);

        $this->assertSame(2, $tokyo->total());
        $this->assertSame(1, $chuo->total());
    }

    public function test_filters_by_equipment(): void
    {
        Sento::factory()->create(['has_shampoo' => true, 'has_soap' => true]);
        Sento::factory()->create(['has_shampoo' => false, 'has_soap' => true]);
        Sento::factory()->create(['has_shampoo' => true, 'has_soap' => false]);

        $service = new SentoListService();
        $shampoo = $service->paginate(['has_shampoo' => true]);
        $both = $service->paginate(['has_shampoo' => true, 'has_soap' => true]);

        $this->assertSame(2, $shampoo->total());
        $this->assertSame(1, $both->total());
    }

    public function test_filters_by_sauna_and_mizuburo_via_reviews(): void
    {
        $sauna = Sento::factory()->create();
        $other = Sento::factory()->create();
        SentoReview::factory()->create(['sento_id' => $sauna->id, 'has_sauna' => true]);
        SentoReview::factory()->create(['sento_id' => $other->id, 'has_sauna' => false]);

        $page = (new SentoListService())->paginate(['has_sauna' => true]);

        $this->assertSame([$sauna->id], collect($page->items())->pluck('id')->all());
    }

    public function test_bath_types_filter_uses_or_semantics(): void
    {
        $tansan = Sento::factory()->create();
        $kusuri = Sento::factory()->create();
        $silk = Sento::factory()->create();
        SentoReview::factory()->create([
            'sento_id' => $tansan->id,
            'bath_types' => ['炭酸泉'],
        ]);
        SentoReview::factory()->create([
            'sento_id' => $kusuri->id,
            'bath_types' => ['薬湯'],
        ]);
        SentoReview::factory()->create([
            'sento_id' => $silk->id,
            'bath_types' => ['シルク'],
        ]);

        // 炭酸泉 OR 薬湯 を含むレビューがある銭湯
        $page = (new SentoListService())->paginate([
            'bath_types' => ['炭酸泉', '薬湯'],
        ]);

        $this->assertSame(2, $page->total());
        $this->assertEqualsCanonicalizing(
            [$tansan->id, $kusuri->id],
            collect($page->items())->pluck('id')->all(),
        );
    }

    public function test_default_sort_is_by_average_rating_desc(): void
    {
        $high = Sento::factory()->create();
        $low = Sento::factory()->create();
        SentoReview::factory()->create(['sento_id' => $high->id, 'rating' => 5]);
        SentoReview::factory()->create(['sento_id' => $low->id, 'rating' => 2]);

        $page = (new SentoListService())->paginate([]);
        $items = collect($page->items())->pluck('id')->all();

        $this->assertSame([$high->id, $low->id], $items);
    }

    public function test_sort_by_want_revisit_count(): void
    {
        $popular = Sento::factory()->create();
        $unpopular = Sento::factory()->create();
        SentoReview::factory(3)->create([
            'sento_id' => $popular->id, 'want_revisit' => true,
        ]);
        SentoReview::factory()->create([
            'sento_id' => $unpopular->id, 'want_revisit' => false,
        ]);

        $page = (new SentoListService())->paginate(['sort' => 'want_revisit']);

        $this->assertSame($popular->id, $page->items()[0]->id);
    }

    public function test_sort_by_visited_at_for_viewer(): void
    {
        $viewer = User::factory()->create();
        $recent = Sento::factory()->create();
        $old = Sento::factory()->create();
        SentoReview::factory()->create([
            'user_id' => $viewer->id, 'sento_id' => $recent->id, 'visited_at' => '2026-04-20',
        ]);
        SentoReview::factory()->create([
            'user_id' => $viewer->id, 'sento_id' => $old->id, 'visited_at' => '2026-01-10',
        ]);

        $page = (new SentoListService())->paginate(
            ['visited' => 'visited', 'sort' => 'visited_at'],
            $viewer,
        );

        $this->assertSame([$recent->id, $old->id], collect($page->items())->pluck('id')->all());
    }

    public function test_sort_by_visited_at_falls_back_to_created_at_when_no_viewer(): void
    {
        // 未ログイン状態で sort=visited_at を渡されてもエラーにならず created_at で並ぶこと
        Sento::factory()->create();
        Sento::factory()->create();

        $page = (new SentoListService())->paginate(['sort' => 'visited_at'], null);

        $this->assertSame(2, $page->total());
    }

    public function test_status_filter_all_includes_temp_closed(): void
    {
        Sento::factory()->create(['status' => SentoStatus::Open->value]);
        Sento::factory()->create(['status' => SentoStatus::ClosedTemp->value]);
        // ClosedPerm は operating() で除外されるので status=all でも出ない
        Sento::factory()->create(['status' => SentoStatus::ClosedPerm->value]);

        $page = (new SentoListService())->paginate(['status' => 'all']);

        $this->assertSame(2, $page->total());
    }

    public function test_status_filter_specific_status(): void
    {
        Sento::factory()->create(['status' => SentoStatus::Open->value]);
        Sento::factory()->create(['status' => SentoStatus::ClosedTemp->value]);

        $page = (new SentoListService())->paginate(['status' => SentoStatus::Open->value]);

        $this->assertSame(1, $page->total());
    }
}
