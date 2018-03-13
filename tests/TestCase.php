<?php

namespace Torann\GeoIP\Tests;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    public static $functions;

    public function setUp()
    {
        self::$functions = Mockery::mock();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function makeGeoIP(array $config = [], $cacheMock = null)
    {
        $cacheMock = $cacheMock ?: Mockery::mock('Illuminate\Cache\CacheManager');

        $config = array_merge($this->getConfig(), $config);

        $cacheMock->shouldReceive('tags')->with(['torann-geoip-location'])->andReturnSelf();

        return new \Torann\GeoIP\GeoIP($config, $cacheMock);
    }

    protected function getConfig()
    {
        $config = include(__DIR__ . '/../config/geoip.php');

        $this->databaseCheck($config['services']['maxmind_database']['database_path']);

        return $config;
    }

    /**
     * Check for test database and make a copy of it
     * if it does not exist.
     *
     * @param string $database
     */
    protected function databaseCheck($database)
    {
        if (file_exists($database) === false) {
            @mkdir(dirname($database), 0755, true);
            copy(__DIR__ . '/../resources/geoip.mmdb', $database);
        }
    }
}