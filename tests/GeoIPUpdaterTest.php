<?php namespace Torann\GeoIP\tests;

use Illuminate\Config\Repository;
use \Torann\GeoIP\GeoIPUpdater;
use PHPUnit_Framework_TestCase;

class GeoIPUpdaterTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
	}

	public function test_no_update()
	{
		$geoIPUpdater = new GeoIPUpdater(new Repository());
		$this->assertFalse($geoIPUpdater->update());
	}

	public function test_max_mind_updater()
	{
		$database = __DIR__ . '/data/GeoLite2-City.mmdb';
		$config = new Repository([
			'geoip' => [
				'service'  => 'maxmind',
				'maxmind' => [
					'type' => 'database',
					'database_path' => $database,
					'update_url' => 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz',
				],
			],
		]);

		$geoIPUpdater = new GeoIPUpdater($config);
		$this->assertEquals($geoIPUpdater->update(), $database);
		unlink($database);
	}
}