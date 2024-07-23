<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;

/**
 * Class GeoIP
 *
 * @package Torann\GeoIP\Services
 */
class IPFinder extends AbstractService
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
            'base_uri' => 'https://api.ipfinder.io/v1/',
            'headers' => [
                'User-Agent' => 'Laravel-GeoIP-Torann',
            ],
            'query'    => [
                'token' => $this->config('key'),
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
        $data = $this->client->get($ip);

        // Verify server response
        if ($this->client->getErrors() !== null || empty($data[0])) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        $json = json_decode($data[0], true);

        return $this->hydrate($json);
    }
}
