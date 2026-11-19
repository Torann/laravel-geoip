<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;

/**
 * Class InternetData
 *
 * @package Torann\GeoIP\Services
 */
class InternetData extends AbstractService
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
        $headers = [
            'Accept: application/json',
        ];

        // Without a key the answer stops at the country
        if ($this->config('key')) {
            $headers[] = 'Authorization: Bearer ' . $this->config('key');
        }

        $this->client = new HttpClient([
            'base_uri' => 'https://api.internetdata.io/',
            'headers' => $headers,
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
        $data = $this->client->get($ip);

        // Verify server response
        if ($this->client->getErrors() !== null || empty($data[0])) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        $json = json_decode($data[0], true);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => Arr::get($json, 'location.country'),
            'country' => Arr::get($json, 'location.country_name'),
            'city' => Arr::get($json, 'location.city'),
            'state' => Arr::get($json, 'location.region_code'),
            'state_name' => Arr::get($json, 'location.region'),
            'postal_code' => Arr::get($json, 'location.postal_code'),
            'lat' => Arr::get($json, 'location.latitude'),
            'lon' => Arr::get($json, 'location.longitude'),
            'timezone' => Arr::get($json, 'location.timezone'),
            'continent' => Arr::get($json, 'location.continent'),
            'currency' => Arr::get($json, 'location.currency_code'),
        ]);
    }
}
