<?php

namespace Torann\GeoIP\Services;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

class MaxMindDatabase extends AbstractService
{
    /**
     * Service reader instance.
     *
     * @var \GeoIp2\Database\Reader
     */
    protected $reader;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        // Copy test database for now
        if (file_exists($this->config('database_path')) === false) {
            copy(__DIR__ . '/../../resources/geoip.mmdb', $this->config('database_path'));
        }

        $this->reader = new Reader(
            $this->config('database_path'),
            $this->config('locales', ['en'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {
        $record = $this->reader->city($ip);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => $record->country->isoCode,
            'country' => $record->country->name,
            'city' => $record->city->name,
            'state' => $record->mostSpecificSubdivision->isoCode,
            'state_name' => $record->mostSpecificSubdivision->name,
            'postal_code' => $record->postal->code,
            'lat' => $record->location->latitude,
            'lon' => $record->location->longitude,
            'timezone' => $record->location->timeZone,
            'continent' => $record->continent->code,
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
        if ($this->config('database_path', false) === false) {
            throw new Exception('Database path not set in config file.');
        }

        // Get settings
        $url = $this->config('update_url');
        $path = $this->config('database_path');

        // Get header response
        $headers = get_headers($url);

        if (substr($headers[0], 9, 3) != '200') {
            throw new Exception('Unable to download database. ('. substr($headers[0], 13) .')');
        }

        // Download zipped database to a system temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($url, 'r'));

        // Unzip and save database
        file_put_contents($path, gzopen($tmpFile, 'r'));

        // Remove temp file
        @unlink($tmpFile);

        return "Database file ({$path}) updated.";
    }
}
