<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface Geocoder
{
    /**
     * 住所から緯度経度を取得する。失敗時は null。
     *
     * @return array{lat: float, lng: float}|null
     */
    public function geocode(string $address): ?array;
}
