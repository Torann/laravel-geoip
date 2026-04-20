<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;

class IP2Geo extends AbstractService
{
    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        $this->client = new HttpClient([
            'base_uri' => 'https://api.ip2geo.dev/',
            'headers' => [
                'User-Agent: Laravel-GeoIP',
                'X-Api-Key: ' . $this->config('key'),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function locate($ip)
    {
        // Get data from client
        $data = $this->client->get('convert', ['ip' => $ip]);

        // Verify server response
        if ($this->client->getErrors() !== null) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        // Parse body content
        $json = json_decode($data[0], true);

        // Verify response status
        if (empty($json) || !Arr::get($json, 'success')) {
            throw new Exception('Request failed (' . Arr::get($json, 'message', 'Unknown error') . ')');
        }

        $data = Arr::get($json, 'data', []);

        $continent = Arr::get($data, 'continent', []);
        $country = Arr::get($continent, 'country', []);
        $city = Arr::get($country, 'city', []);
        $subdivision = Arr::get($country, 'subdivision', []);
        $timezone = Arr::get($city, 'timezone', []);
        $currency = Arr::get($country, 'currency', []);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => Arr::get($country, 'code'),
            'country' => Arr::get($country, 'name'),
            'city' => Arr::get($city, 'name'),
            'state' => Arr::get($subdivision, 'code'),
            'state_name' => Arr::get($subdivision, 'name'),
            'postal_code' => Arr::get($city, 'postal_code'),
            'lat' => Arr::get($city, 'latitude'),
            'lon' => Arr::get($city, 'longitude'),
            'timezone' => Arr::get($timezone, 'name'),
            'continent' => Arr::get($continent, 'code'),
            'currency' => Arr::get($currency, 'code'),
        ]);
    }
}
