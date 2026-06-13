<?php

declare(strict_types=1);

namespace App\Services\Geocoding;

use App\Services\Contracts\Geocoder;
use GuzzleHttp\Client;
use Throwable;

final class GoogleGeocoder implements Geocoder
{
    public function __construct(
        private readonly Client $http,
        private readonly string $apiKey,
    ) {
    }

    public function geocode(string $address): ?array
    {
        if ($this->apiKey === '') {
            return null;
        }
        try {
            $res = $this->http->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address' => $address,
                    'key' => $this->apiKey,
                    'language' => 'ja',
                ],
                'timeout' => 10,
            ]);
        } catch (Throwable) {
            return null;
        }
        $body = json_decode((string) $res->getBody(), true);
        $loc = $body['results'][0]['geometry']['location'] ?? null;
        if (!$loc) {
            return null;
        }
        return ['lat' => (float) $loc['lat'], 'lng' => (float) $loc['lng']];
    }
}
