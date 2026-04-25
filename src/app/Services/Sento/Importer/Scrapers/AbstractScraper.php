<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer\Scrapers;

use GuzzleHttp\Client;

abstract class AbstractScraper implements PrefectureScraper
{
    public function __construct(
        protected readonly Client $http = new Client(['timeout' => 30, 'headers' => [
            'User-Agent' => 'YumeguriBot/1.0 (+https://yumeguri.example.com)',
        ]]),
    ) {
    }

    protected function fetch(string $url): string
    {
        $res = $this->http->get($url);
        return (string) $res->getBody();
    }
}
