<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use App\Domain\Enum\Prefecture;

final class SaitamaScraper extends AbstractScraper
{
    public function prefecture(): Prefecture
    {
        return Prefecture::Saitama;
    }

    public function scrape(): iterable
    {
        // TODO: 埼玉県浴場組合サイトの店舗一覧を解析する
        return [];
    }
}
