<?php

namespace Torann\GeoIP\Services;

use Exception;
use Torann\GeoIP\Support\HttpClient;

/**
 * Class GeoIP
 * @package Torann\GeoIP\Services
 */
class ApiBundle extends AbstractService
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
        $base = [
            'base_uri' => 'https://api.apibundle.io/ip-lookup?apikey=' . $this->config('key'),
        ];

        if ($this->config('lang')) {
            $base['base_uri'] .= '&language=' . $this->config('lang');
        }

        $this->client = new HttpClient($base);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function locate($ip)
    {
        // Get data from client
        $data = $this->client->get('&ip=' . $ip);

        // Verify server response
        if ($this->client->getErrors() !== null || empty($data[0])) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        $json = json_decode($data[0], true);

        return $this->hydrate($json);
    }
}
