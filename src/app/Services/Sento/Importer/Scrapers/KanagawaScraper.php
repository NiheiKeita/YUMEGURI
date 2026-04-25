<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use App\Domain\Enum\Prefecture;

final class KanagawaScraper extends AbstractScraper
{
    public function prefecture(): Prefecture
    {
        return Prefecture::Kanagawa;
    }

    public function scrape(): iterable
    {
        // TODO: https://k-o-i.jp の店舗一覧を解析する
        return [];
    }
}
