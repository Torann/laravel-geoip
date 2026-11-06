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

    /**
     * @test
     */
    public function shouldLogFailureToDefaultFile()
    {
        $log_file = storage_path('logs/geoip.log');
        @unlink($log_file);

        $geo_ip = $this->makeGeoIP([
            'service' => 'maxmind_database',
            'cache' => 'none',
        ]);

        $geo_ip->getLocation('1.1.1.1');

        $this->assertStringContainsString('The address 1.1.1.1 is not in the database.', file_get_contents($log_file));

        @unlink($log_file);
    }

    /**
     * @test
     */
    public function shouldLogFailureToConfiguredChannel()
    {
        $log_file = storage_path('logs/geoip.log');
        @unlink($log_file);

        $channelMock = Mockery::mock();
        $channelMock->shouldReceive('error')
            ->with('The address 1.1.1.1 is not in the database.', Mockery::type('array'))
            ->once();

        $logMock = Mockery::mock();
        $logMock->shouldReceive('channel')
            ->with('geoip')
            ->once()
            ->andReturn($channelMock);

        self::$functions->shouldReceive('app')
            ->with('log', null)
            ->once()
            ->andReturn($logMock);

        $geo_ip = $this->makeGeoIP([
            'service' => 'maxmind_database',
            'cache' => 'none',
            'log_channel' => 'geoip',
        ]);

        $geo_ip->getLocation('1.1.1.1');

        $this->assertFileDoesNotExist($log_file);
    }

    /**
     * @test
     */
    public function shouldNotLogFailureWhenDisabled()
    {
        $log_file = storage_path('logs/geoip.log');
        @unlink($log_file);

        self::$functions->shouldNotReceive('app');

        $geo_ip = $this->makeGeoIP([
            'service' => 'maxmind_database',
            'cache' => 'none',
            'log_failures' => false,
            'log_channel' => 'geoip',
        ]);

        $geo_ip->getLocation('1.1.1.1');

        $this->assertFileDoesNotExist($log_file);
    }
}