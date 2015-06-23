<?php namespace Torann\GeoIP;

use GuzzleHttp\Client as Client;

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
	public function __construct(array $config)
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
		if (array_get($this->config, 'geoip.database_path', false)) {
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
		$maxMindDatabaseUrl = 'http://geolite.maxmind.com/download/geoip/database/';
		$databasePath = array_get($this->config, 'geoip.database_path', storage_path('geoip.mmdb'));

		$client = new Client(['base_uri' => $maxMindDatabaseUrl]);
		$response = $client->get('GeoLite2-City.mmdb.gz');
		$file = $response->getBody();

		@file_put_contents($databasePath, gzdecode($file));

		return $databasePath;
	}
}