<?php namespace Torann\GeoIP;

use GuzzleHttp\Client as Client;
use Illuminate\Config\Repository;

class GeoIPUpdater
{
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @param array $config
	 */
	public function __construct(Repository $config)
	{
		$this->config = $config;
	}

	/**
	 * Main update function.
	 *
	 * @return bool|string
	 */
	public function update()
	{
		if ($this->config->get('geoip.maxmind.database_path', false)) {
			return $this->updateMaxMindDatabase();
		}

		return false;
	}

	/**
	 * Update function for max mind database.
	 *
	 * @return string
	 */
	protected function updateMaxMindDatabase()
	{
		$maxMindDatabaseUrl = $this->config->get('geoip.maxmind.update_url');
		$databasePath = $this->config->get('geoip.maxmind.database_path');

        // Download zipped database to a system temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($maxMindDatabaseUrl, 'r'));

        // Unzip and save database
		file_put_contents($databasePath, gzopen($tmpFile, 'r'));

        // Remove temp file
        @unlink($tmpFile);

		return $databasePath;
	}
}