<?php

namespace Torann\GeoIP\Services;

use Exception;
use Illuminate\Support\Arr;
use Torann\GeoIP\Support\HttpClient;

class IPApi extends AbstractService
{
    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * An array of continents.
     *
     * @var array
     */
    protected $continents;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        $base = [
            'base_uri' => 'http://ip-api.com/',
            'headers' => [
                'User-Agent' => 'Laravel-GeoIP',
            ],
            'query' => [
                'fields' => 49663,
                'lang' => $this->config('lang', ['en']),
            ],
        ];

        // Using the Pro service
        if ($this->config('key')) {
            $base['base_uri'] = ($this->config('secure') ? 'https' : 'http') . '://pro.ip-api.com/';
            $base['query']['key'] = $this->config('key');
        }

        $this->client = new HttpClient($base);

        // Set continents
        if (file_exists($this->config('continent_path'))) {
            $this->continents = json_decode(file_get_contents($this->config('continent_path')), true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {
        // Get data from client
        $data = $this->client->get('json/' . $ip);

        // Verify server response
        if ($this->client->getErrors() !== null) {
            throw new Exception('Request failed (' . $this->client->getErrors() . ')');
        }

        // Parse body content
        $json = json_decode($data[0]);

        // Verify response status
        if ($json->status !== 'success') {
            throw new Exception('Request failed (' . $json->message . ')');
        }

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => $json->countryCode,
            'country' => $json->country,
            'city' => $json->city,
            'state' => $json->region,
            'state_name' => $json->regionName,
            'postal_code' => $json->zip,
            'lat' => $json->lat,
            'lon' => $json->lon,
            'timezone' => $json->timezone,
            'continent' => $this->getContinent($json->countryCode),
        ]);
    }

    /**
     * Update function for service.
     *
     * @return string
     * @throws Exception
     */
    public function update()
    {
        $data = $this->client->get('https://dev.maxmind.com/static/csv/codes/country_continent.csv');

        // Verify server response
        if ($this->client->getErrors() !== null) {
            throw new Exception($this->client->getErrors());
        }

        $lines = explode("\n", $data[0]);

        array_shift($lines);

        $output = [];

        foreach ($lines as $line) {
            $arr = str_getcsv($line);

            if (count($arr) < 2) {
                continue;
            }

            $output[$arr[0]] = $arr[1];
        }

        // Get path
        $path = $this->config('continent_path');

        file_put_contents($path, json_encode($output));

        return "Continent file ({$path}) updated.";
    }

    /**
     * Get continent based on country code.
     *
     * @param string $code
     *
     * @return string
     */
    private function getContinent($code)
    {
        return Arr::get($this->continents, $code, 'Unknown');
    }
}
