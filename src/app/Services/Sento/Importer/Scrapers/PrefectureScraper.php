<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use App\Domain\Enum\Prefecture;
use App\Services\Sento\Importer\ScrapedSento;

interface PrefectureScraper
{
    public function prefecture(): Prefecture;

    /**
     * @return iterable<ScrapedSento>
     */
    public function scrape(): iterable;
}
