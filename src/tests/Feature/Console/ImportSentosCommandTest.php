<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Domain\Enum\Prefecture;
use App\Models\Sento;
use App\Services\Contracts\Geocoder;
use App\Services\Sento\Importer\ScrapedSento;
use App\Services\Sento\Importer\Scrapers\ChibaScraper;
use App\Services\Sento\Importer\Scrapers\KanagawaScraper;
use App\Services\Sento\Importer\Scrapers\PrefectureScraper;
use App\Services\Sento\Importer\Scrapers\SaitamaScraper;
use App\Services\Sento\Importer\Scrapers\TokyoScraper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ImportSentosCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_failure_for_unknown_prefecture(): void
    {
        $this->artisan('sento:import', ['--prefecture' => 'osaka'])
            ->assertExitCode(1);
    }

    public function test_runs_only_specified_prefecture_scraper(): void
    {
        // tag 経由で取得される Tokyo スクレイパー実装を mock に差し替える
        $tokyoMock = $this->scraperMock(Prefecture::Tokyo, [
            $this->scraped('湊湯', '東京都中央区湊1-6-2'),
        ]);
        $this->app->instance(TokyoScraper::class, $tokyoMock);

        // 他の都県のスクレイパーは呼ばれてはいけない
        $this->app->instance(KanagawaScraper::class, $this->expectNotCalled());
        $this->app->instance(SaitamaScraper::class, $this->expectNotCalled());
        $this->app->instance(ChibaScraper::class, $this->expectNotCalled());

        $geocoder = Mockery::mock(Geocoder::class);
        $geocoder->shouldReceive('geocode')->andReturn(['lat' => 35.67, 'lng' => 139.77]);
        $this->app->instance(Geocoder::class, $geocoder);

        $this->artisan('sento:import', ['--prefecture' => 'tokyo'])
            ->assertExitCode(0);

        $this->assertSame(1, Sento::count());
        $this->assertSame('湊湯', Sento::first()->name);
    }

    public function test_runs_all_scrapers_when_prefecture_omitted(): void
    {
        $this->app->instance(TokyoScraper::class, $this->scraperMock(Prefecture::Tokyo, [
            $this->scraped('東京湯', '東京都...'),
        ]));
        $this->app->instance(KanagawaScraper::class, $this->scraperMock(Prefecture::Kanagawa, [
            $this->scraped('神奈川湯', '神奈川県...'),
        ]));
        $this->app->instance(SaitamaScraper::class, $this->scraperMock(Prefecture::Saitama, []));
        $this->app->instance(ChibaScraper::class, $this->scraperMock(Prefecture::Chiba, []));

        $geocoder = Mockery::mock(Geocoder::class);
        $geocoder->shouldReceive('geocode')->andReturn(null);
        $this->app->instance(Geocoder::class, $geocoder);

        $this->artisan('sento:import')->assertExitCode(0);

        $this->assertSame(2, Sento::count());
    }

    /** @param list<ScrapedSento> $items */
    private function scraperMock(Prefecture $pref, array $items): PrefectureScraper
    {
        $mock = Mockery::mock(PrefectureScraper::class);
        $mock->shouldReceive('prefecture')->andReturn($pref);
        $mock->shouldReceive('scrape')->andReturn($items);
        return $mock;
    }

    private function expectNotCalled(): PrefectureScraper
    {
        $mock = Mockery::mock(PrefectureScraper::class);
        // prefecture() は呼ばれて pass される（filter で弾かれる）
        $mock->shouldReceive('prefecture')->andReturn(Prefecture::Kanagawa); // any
        $mock->shouldNotReceive('scrape');
        return $mock;
    }

    private function scraped(string $name, string $address): ScrapedSento
    {
        return new ScrapedSento(
            name: $name,
            nameKana: null,
            prefecture: '東京都',
            city: null,
            address: $address,
            phone: null,
            hours: null,
            closedDays: null,
            price: null,
            sourceUrl: null,
            nearestStation: null,
            walkMinutes: null,
        );
    }
}
