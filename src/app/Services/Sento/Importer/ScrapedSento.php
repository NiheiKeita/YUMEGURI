<?php

declare(strict_types=1);

namespace App\Services\Sento\Importer;

/**
 * 各都県の組合サイトから抽出した銭湯情報の中間表現。
 * Sento モデルへの値オブジェクト的なブリッジ。
 */
final class ScrapedSento
{
    /**
     * @param list<string> $images
     */
    public function __construct(
        public string $name,
        public ?string $nameKana,
        public string $prefecture,
        public ?string $city,
        public string $address,
        public ?string $phone,
        public ?string $hours,
        public ?string $closedDays,
        public ?int $price,
        public ?string $sourceUrl,
        public ?string $nearestStation,
        public ?int $walkMinutes,
        public bool $hasShampoo = false,
        public bool $hasSoap = false,
        public array $images = [],
    ) {
    }

    /**
     * Sento モデルへ反映する属性のうち、is_manually_updated=true のレコードでも
     * 上書きしてよい安全なキーだけを返す。
     *
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'name' => $this->name,
            'name_kana' => $this->nameKana,
            'prefecture' => $this->prefecture,
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'hours' => $this->hours,
            'closed_days' => $this->closedDays,
            'price' => $this->price,
            'source_url' => $this->sourceUrl,
            'nearest_station' => $this->nearestStation,
            'walk_minutes' => $this->walkMinutes,
            'has_shampoo' => $this->hasShampoo,
            'has_soap' => $this->hasSoap,
        ];
    }
}
