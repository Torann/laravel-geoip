<?php

namespace Torann\GeoIP\Tests;

use Mockery;

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

        $this->assertInstanceOf(\Torann\GeoIP\Contracts\ServiceInterface::class, $geo_ip->getService());
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $geo_ip = $this->makeGeoIP();

        $this->assertInstanceOf(\Torann\GeoIP\Cache::class, $geo_ip->getCache());
    }
}