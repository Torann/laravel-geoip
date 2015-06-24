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

		$client = new Client();
		$response = $client->get($maxMindDatabaseUrl);
		$file = $response->getBody();

		@file_put_contents($databasePath, gzdecode($file));

		return $databasePath;
	}
}