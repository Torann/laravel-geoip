<?php

namespace Torann\GeoIP\Services;

use Exception;
use GeoIp2\Database\Reader;

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
        $url = 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz';
        $path = $this->config('database_path');

        // Get header response
        $headers = get_headers($url);

        if (substr($headers[0], 9, 3) != '200') {
            throw new Exception('Unable to download database. (' . substr($headers[0], 13) . ')');
        }

        // Download zipped database to a system temp file
       
        $tmpFile = storage_path('app/GeoLite2.tar.gz');
        file_put_contents($tmpFile, fopen($url, 'r'));
        
        $archive = new \PharData($tmpFile);
        $archive->decompress(); // decompress app/GeoLite2.tar
        // unarchive from the tar
        $phar = new \PharData(storage_path('app/GeoLite2.tar'));
        $phar->extractTo(storage_path('app/tmp'));  

        
        copy(glob(storage_path('app/tmp') . "/*/*.mmdb")[0] , $path );

        // Remove temp file
        unlink($tmpFile);
        unlink(storage_path('app/GeoLite2.tar'));
        $this->removeDirectory(storage_path('app/tmp'));

        return "Database file ({$path}) updated.";
    }
    
    public function removeDirectory($path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
        return;
    }
}
