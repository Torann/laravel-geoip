<?php

namespace Torann\GeoIP\Tests;

class GeoIPTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetUSDCurrency()
    {
        $geo_ip = $this->makeGeoIP();

        $this->assertEquals($geo_ip->getCurrency('US'), 'USD');
    }

    /**
     * @test
     */
    public function testGetService()
    {
        $geo_ip = $this->makeGeoIP([
            'service' => 'maxmind_database',
        ]);

        // Get config values
        $config = $this->getConfig()['services']['maxmind_database'];
        unset($config['class']);

        self::$functions->shouldReceive('app')->with('Torann\GeoIP\Services\MaxMindDatabase', [$config])->andReturn(true);

        $this->assertEquals($geo_ip->getService(), true);
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $geo_ip = $this->makeGeoIP([
            'cache' => 'session',
        ]);

        // Get config values
        $config = $this->getConfig()['cache_drivers']['session'];
        unset($config['class']);

        self::$functions->shouldReceive('app')->with('Torann\GeoIP\Cache\Session', [$config])->andReturn(true);

        $this->assertEquals($geo_ip->getCache(), true);
    }
}