<?php

namespace Torann\GeoIP\Services;

use GeoIp2\Database\Reader;
use GeoIp2\WebService\Client;
use GeoIp2\Exception\AddressNotFoundException;

class MaxMind extends AbstractService
{
    /**
     * Service client instance.
     *
     * @var Reader|Client
     */
    protected $client;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->getConfig('type') === 'web_service') {
            $this->client = new Client(
                $this->getConfig('user_id'),
                $this->getConfig('license_key'),
                $this->getConfig('locales', ['en'])
            );
        }
        else {
            $this->client = new Reader(
                $this->getConfig('database_path'),
                $this->getConfig('locales', ['en'])
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {
        $record = $this->client->city($ip);

        return [
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
            'default' => false,
        ];
    }

    /**
     * Update function for service.
     *
     * @return string
     */
    public function update()
    {
        if ($this->getConfig('database_path', false) === false) {
            return null;
        }

        $maxMindDatabaseUrl = $this->getConfig('update_url');
        $databasePath = $this->getConfig('database_path');

        // Download zipped database to a system temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($maxMindDatabaseUrl, 'r'));

        // Unzip and save database
        file_put_contents($databasePath, gzopen($tmpFile, 'r'));

        // Remove temp file
        @unlink($tmpFile);

        return "Database file ({$databasePath}) updated.";
    }
}