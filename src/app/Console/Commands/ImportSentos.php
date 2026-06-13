<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Enum\Prefecture;
use App\Services\Contracts\Geocoder;
use App\Services\Sento\Importer\SentoImportService;
use Illuminate\Console\Command;

class ImportSentos extends Command
{
    protected $signature = 'sento:import {--prefecture= : tokyo|kanagawa|saitama|chiba。未指定で全都県}';

    protected $description = '組合サイトをスクレイピングして銭湯マスターを更新する';

    public function handle(Geocoder $geocoder): int
    {
        $only = null;
        if ($value = $this->option('prefecture')) {
            $only = Prefecture::tryFrom((string) $value);
            if (!$only) {
                $this->error("不正な --prefecture: {$value}");
                return self::FAILURE;
            }
        }

        $scrapers = $this->laravel->tagged('sento.scrapers');
        $importer = new SentoImportService($scrapers, $geocoder);
        $stats = $importer->execute($only);

        $this->info(sprintf(
            'processed=%d created=%d updated=%d skipped=%d',
            $stats['processed'],
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
        ));
        return self::SUCCESS;
    }
}
