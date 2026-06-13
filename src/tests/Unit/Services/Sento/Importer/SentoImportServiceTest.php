<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Sento\Importer;

use App\Domain\Enum\Prefecture;
use App\Models\Sento;
use App\Services\Contracts\Geocoder;
use App\Services\Sento\Importer\ScrapedSento;
use App\Services\Sento\Importer\Scrapers\PrefectureScraper;
use App\Services\Sento\Importer\SentoImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SentoImportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_new_sento_with_geocoded_lat_lng(): void
    {
        $scraper = $this->makeScraper(Prefecture::Tokyo, [
            $this->scraped('湊湯', '東京都中央区湊1-6-2'),
        ]);
        $geocoder = Mockery::mock(Geocoder::class);
        $geocoder->shouldReceive('geocode')
            ->with('東京都中央区湊1-6-2')
            ->once()
            ->andReturn(['lat' => 35.6712, 'lng' => 139.7763]);

        $stats = (new SentoImportService([$scraper], $geocoder))->execute();

        $this->assertSame(['processed' => 1, 'created' => 1, 'updated' => 0, 'skipped' => 0], $stats);
        $sento = Sento::query()->firstOrFail();
        $this->assertSame('湊湯', $sento->name);
        $this->assertEqualsWithDelta(35.6712, (float) $sento->lat, 0.0001);
    }

    public function test_skips_records_marked_as_manually_updated(): void
    {
        Sento::factory()->create([
            'name' => '湊湯',
            'prefecture' => '東京都',
            'address' => '東京都中央区湊1-6-2',
            'hours' => '15:00-23:30', // 手動で直された情報
            'is_manually_updated' => true,
        ]);

        $scraper = $this->makeScraper(Prefecture::Tokyo, [
            $this->scraped('湊湯', '東京都中央区湊1-6-2', hours: '14:00-22:00'),
        ]);
        $geocoder = Mockery::mock(Geocoder::class);
        $geocoder->shouldNotReceive('geocode'); // skipped なら Geocoder も呼ばない

        $stats = (new SentoImportService([$scraper], $geocoder))->execute();

        $this->assertSame(['processed' => 1, 'created' => 0, 'updated' => 0, 'skipped' => 1], $stats);
        // 手動編集分が保護されていることを確認
        $this->assertSame('15:00-23:30', Sento::query()->firstOrFail()->hours);
    }

    public function test_updates_record_when_not_manually_updated(): void
    {
        Sento::factory()->create([
            'name' => '湊湯',
            'prefecture' => '東京都',
            'address' => '東京都中央区湊1-6-2',
            'hours' => '15:00-23:30',
            'lat' => 35.0,
            'lng' => 139.0,
            'is_manually_updated' => false,
        ]);

        $scraper = $this->makeScraper(Prefecture::Tokyo, [
            $this->scraped('湊湯', '東京都中央区湊1-6-2', hours: '14:00-22:00'),
        ]);
        $geocoder = Mockery::mock(Geocoder::class);
        // 既に lat/lng があるので geocode は呼ばれない
        $geocoder->shouldNotReceive('geocode');

        $stats = (new SentoImportService([$scraper], $geocoder))->execute();

        $this->assertSame(['processed' => 1, 'created' => 0, 'updated' => 1, 'skipped' => 0], $stats);
        $this->assertSame('14:00-22:00', Sento::query()->firstOrFail()->hours);
    }

    public function test_only_runs_scrapers_for_specified_prefecture(): void
    {
        $tokyo = $this->makeScraper(Prefecture::Tokyo, [$this->scraped('東京湯', '東京都...')]);
        $chiba = $this->makeScraper(Prefecture::Chiba, [$this->scraped('千葉湯', '千葉県...')]);
        $geocoder = Mockery::mock(Geocoder::class);
        $geocoder->shouldReceive('geocode')->andReturn(null);

        $stats = (new SentoImportService([$tokyo, $chiba], $geocoder))->execute(Prefecture::Tokyo);

        $this->assertSame(1, $stats['processed']);
        $this->assertSame('東京湯', Sento::query()->firstOrFail()->name);
    }

    private function scraped(string $name, string $address, ?string $hours = '15:00-23:00'): ScrapedSento
    {
        return new ScrapedSento(
            name: $name,
            nameKana: null,
            prefecture: '東京都',
            city: null,
            address: $address,
            phone: null,
            hours: $hours,
            closedDays: null,
            price: 520,
            sourceUrl: null,
            nearestStation: null,
            walkMinutes: null,
        );
    }

    /** @param list<ScrapedSento> $items */
    private function makeScraper(Prefecture $pref, array $items): PrefectureScraper
    {
        $mock = Mockery::mock(PrefectureScraper::class);
        $mock->shouldReceive('prefecture')->andReturn($pref);
        $mock->shouldReceive('scrape')->andReturn($items);
        return $mock;
    }
}
