<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Geocoding;

use App\Services\Geocoding\GoogleGeocoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GoogleGeocoderTest extends TestCase
{
    public function test_returns_null_when_api_key_is_empty(): void
    {
        $geocoder = new GoogleGeocoder(new Client(), '');
        $this->assertNull($geocoder->geocode('東京都中央区湊1-6-2'));
    }

    public function test_returns_lat_lng_on_successful_response(): void
    {
        $body = json_encode([
            'results' => [[
                'geometry' => ['location' => ['lat' => 35.6712, 'lng' => 139.7763]],
            ]],
        ]);
        $client = $this->mockClient([new Response(200, [], $body)]);

        $result = (new GoogleGeocoder($client, 'fake-key'))->geocode('東京都中央区湊1-6-2');

        $this->assertSame(['lat' => 35.6712, 'lng' => 139.7763], $result);
    }

    public function test_returns_null_when_results_array_is_empty(): void
    {
        $client = $this->mockClient([new Response(200, [], json_encode(['results' => []]))]);

        $result = (new GoogleGeocoder($client, 'fake-key'))->geocode('存在しない住所');

        $this->assertNull($result);
    }

    public function test_returns_null_on_http_error(): void
    {
        $client = $this->mockClient([
            new ConnectException('timeout', new Request('GET', 'https://maps.googleapis.com')),
        ]);

        $result = (new GoogleGeocoder($client, 'fake-key'))->geocode('東京駅');

        $this->assertNull($result);
    }

    /** @param array<int, mixed> $responses */
    private function mockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
