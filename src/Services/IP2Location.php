<?php

namespace Torann\GeoIP\Services;

use Exception;
use Torann\GeoIP\Location;
use Torann\GeoIP\Support\HttpClient;

class IP2Location extends AbstractService {

    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected HttpClient $client;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->client = new HttpClient([
            'base_uri' => 'http://api.ip2location.io/',
            'query' => [
                'key' => $this->config('key'),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function locate($ip): array|Location
    {
        // Get data from client
        $data = $this->client->get('find', [
            'ip' => $ip,
        ]);

        // Verify server response
        if ($this->client->getErrors() !== null) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        // Parse body content
        $json = json_decode($data[0]);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => $json->country_code,
            'country' => $json->country_name,
            'city' => $json->city_name,
            'state' => null,
            'state_name' => $json->region_name,
            'postal_code' => $json->zip_code,
            'lat' => $json->latitude,
            'lon' => $json->longitude,
            'timezone' => $json->time_zone,
            'continent' => null,
        ]);
    }

    /**
     * Update function for service.
     *
     * @return string
     */
    public function update()
    {
        // Optional artisan command line update method
    }
}
