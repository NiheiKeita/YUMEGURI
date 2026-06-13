<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use App\Domain\Enum\Prefecture;

final class ChibaScraper extends AbstractScraper
{
    public function prefecture(): Prefecture
    {
        return Prefecture::Chiba;
    }

    public function scrape(): iterable
    {
        // TODO: https://chiba1126sento.com の店舗一覧を解析する
        return [];
    }
}
