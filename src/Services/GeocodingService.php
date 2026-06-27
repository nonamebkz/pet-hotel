<?php

declare(strict_types=1);

namespace App\Services;

final class GeocodingService
{
    /**
     * @return array{lat: float, lng: float, display_name?: string}|null
     */
    public function geocode(string $address): ?array
    {
        $address = trim($address);

        if ($address === '') {
            return null;
        }

        $query = http_build_query([
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'countrycodes' => 'id',
        ]);

        $url = 'https://nominatim.openstreetmap.org/search?' . $query;
        $appConfig = config('app');

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: {$appConfig['geocoding_user_agent']}\r\nAccept: application/json\r\n",
                'timeout' => (float) $appConfig['geocoding_timeout'],
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        /** @var list<array{lat?: string, lon?: string, display_name?: string}>|null $data */
        $data = json_decode($response, true);

        if (!is_array($data) || $data === []) {
            return null;
        }

        $first = $data[0];

        if (!isset($first['lat'], $first['lon'])) {
            return null;
        }

        $result = [
            'lat' => (float) $first['lat'],
            'lng' => (float) $first['lon'],
        ];

        if (!empty($first['display_name'])) {
            $result['display_name'] = (string) $first['display_name'];
        }

        return $result;
    }
}
