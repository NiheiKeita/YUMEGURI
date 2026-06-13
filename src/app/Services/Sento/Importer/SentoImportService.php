<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer;

use App\Domain\Enum\Prefecture;
use App\Models\Sento;
use App\Services\Contracts\Geocoder;
use App\Services\Sento\Importer\Scrapers\PrefectureScraper;
use Illuminate\Support\Facades\DB;

final class SentoImportService
{
    /**
     * @param iterable<PrefectureScraper> $scrapers
     */
    public function __construct(
        private readonly iterable $scrapers,
        private readonly Geocoder $geocoder,
    ) {
    }

    /**
     * @return array{processed:int, created:int, updated:int, skipped:int}
     */
    public function execute(?Prefecture $only = null): array
    {
        $stats = ['processed' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($this->scrapers as $scraper) {
            if ($only !== null && $scraper->prefecture() !== $only) {
                continue;
            }
            foreach ($scraper->scrape() as $scraped) {
                $stats['processed']++;
                $result = $this->upsert($scraped);
                $stats[$result]++;
            }
        }
        return $stats;
    }

    /**
     * @return 'created'|'updated'|'skipped'
     */
    private function upsert(ScrapedSento $scraped): string
    {
        $existing = Sento::query()
            ->where('prefecture', $scraped->prefecture)
            ->where('name', $scraped->name)
            ->where('address', $scraped->address)
            ->first();

        // 手動編集済みのレコードは保護する（仕様: is_manually_updated=true は再投入で上書きしない）
        if ($existing && $existing->is_manually_updated) {
            return 'skipped';
        }

        $attrs = $scraped->toAttributes();
        if (!$existing || $existing->lat === null || $existing->lng === null) {
            $coords = $this->geocoder->geocode($scraped->address);
            if ($coords) {
                $attrs['lat'] = $coords['lat'];
                $attrs['lng'] = $coords['lng'];
            }
        }

        return DB::transaction(function () use ($existing, $attrs) {
            if ($existing) {
                $existing->fill($attrs);
                $existing->save();
                return 'updated';
            }
            Sento::create($attrs);
            return 'created';
        });
    }
}
