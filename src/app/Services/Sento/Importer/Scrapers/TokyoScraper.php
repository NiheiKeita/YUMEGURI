<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use App\Domain\Enum\Prefecture;

/**
 * 東京都浴場組合 https://www.1010.or.jp/map/item の HTML パーサ。
 *
 * 実装メモ:
 * - 一覧ページから個別ページへのリンクを集め、個別ページで詳細を抽出する想定。
 * - DOM 構造は変動するので、実装は本物の HTML を見ながら別 PR で詰める。
 */
final class TokyoScraper extends AbstractScraper
{
    public function prefecture(): Prefecture
    {
        return Prefecture::Tokyo;
    }

    public function scrape(): iterable
    {
        // TODO: https://www.1010.or.jp/map/item を辿って ScrapedSento を yield する
        return [];
    }
}
