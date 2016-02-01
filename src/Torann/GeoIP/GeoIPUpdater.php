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
		$tmpPath = $this->config->get('geoip.maxmind.tmp_path');
		$mmdb = $tmpPath = $this->config->get('geoip.maxmind.mmdb');


      	if(!is_dir($tmp_path)) {
			mkdir($tmp_path);
		}

		$tmp = $tmp_path . 'tmp.tar.gz';
        file_put_contents($tmp, fopen($maxMindDatabaseUrl, 'r'));

        $archive = new PharData($tmp);
        $archive->extractTo($tmp_path);
		$contents = scandir($tmp_path);
		$tmp_dir;
		foreach ($contents as $content) {
		    if ($content!= '.' &&  $content != '..'){
			    if(is_dir($tmp_path .$content)) {
			    	$tmp_dir = $tmp_path .$content;
			    	file_put_contents($databasePath,  fopen($tmp_path .$content.'/' . $mmdb, 'r'));
			   	}
			}
		}
		
		unlink($tmp);
		foreach (scandir($tmp_dir) as $item) {
		    if ($item == '.' || $item == '..') continue;
		    unlink($tmp_dir.DIRECTORY_SEPARATOR.$item);
		}
		rmdir($tmp_dir);

		return $databasePath;
	}
}