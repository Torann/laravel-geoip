<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;
class IpQuery extends \Torann\GeoIP\Services\AbstractService
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
            'base_uri' => "https://api.ipquery.io/",
        ];


        $this->client = new HttpClient($base);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function locate($ip)
    {
        // Get data from client
        $data = $this->client->get("{$ip}",['format'=>'json']);

        // Verify server response
        if ($this->client->getErrors() !== null || empty($data[0])) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        $json = json_decode($data[0], true)['location'];
        // Check if currency is available IPQuery API doesn't provide it

        if (empty($json['currency'])) {
            $currencies = require __DIR__.'/../support/currencies.php';
            $json['currency'] = $currencies[$json['country_code']];
        }

        return $this->hydrate($json['location']);
    }
}
