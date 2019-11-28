<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;

/**
 * Class GeoIP
 * @package Torann\GeoIP\Services
 */
class IPData extends AbstractService
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
            'base_uri' => 'https://api.ipdata.co/',
            'query'    => [
                'api-key' => $this->config('key'),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
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
            'iso_code' => $json['country_code'],
            'country' => $json['country_name'],
            'city' => $json['city'],
            'state' => $json['region_code'],
            'state_name' => $json['region'],
            'postal_code' => $json['postal'],
            'lat' => $json['latitude'],
            'lon' => $json['longitude'],
            'timezone' => Arr::get($json, 'time_zone.name'),
            'continent' => Arr::get($json, 'continent_code'),
            'currency' => Arr::get($json, 'currency.code'),
        ]);
    }
}
