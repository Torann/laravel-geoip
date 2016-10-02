<?php

namespace Torann\GeoIP\Tests\Cache;

use Mockery;
use Torann\GeoIP\Tests\TestCase;

class SessionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnConfigValue()
    {
        $sessionMock = Mockery::mock('Illuminate\Session\SessionManager');

        list($service, $config) = $this->getCache($sessionMock);

        $this->assertEquals($service->config('class'), $config['class']);
    }

    /**
     * @test
     */
    public function shouldReturnValidLocation()
    {
        $location = [
            'ip' => '81.2.69.142',
            'iso_code' => 'US',
            'lat' => 41.31,
            'lon' => -72.92,
        ];

        $sessionMock = Mockery::mock('Illuminate\Session\SessionManager');
        $sessionMock->shouldReceive('get')->with('torann-geoip-location')->andReturn($location);

        list($service, $config) = $this->getCache($sessionMock);

        $location = $service->get($location['ip']);

        $this->assertInstanceOf(\Torann\GeoIP\Location::class, $location);
        $this->assertEquals($location->ip, '81.2.69.142');
        $this->assertEquals($location->default, false);
    }

    /**
     * @test
     */
    public function shouldReturnInvalidLocation()
    {
        $sessionMock = Mockery::mock('Illuminate\Session\SessionManager');
        $sessionMock->shouldReceive('get')->with('torann-geoip-location')->andReturn(null);

        list($service, $config) = $this->getCache($sessionMock);

        $this->assertEquals($service->get('81.2.69.142'), null);
    }

    /**
     * @test
     */
    public function shouldSetLocation()
    {
        $location = new \Torann\GeoIP\Location([
            'ip' => '81.2.69.142',
            'iso_code' => 'US',
            'lat' => 41.31,
            'lon' => -72.92,
        ]);

        $sessionMock = Mockery::mock('Illuminate\Session\SessionManager');

        list($service, $config) = $this->getCache($sessionMock);

        $sessionMock->shouldReceive('set')->withArgs(['torann-geoip-location', $location->toArray()])->andReturn(null);

        $this->assertEquals($service->set('81.2.69.142', $location), null);
    }

    protected function getCache($sessionMock)
    {
        self::$functions->shouldReceive('app')->with('session', null)->andReturn($sessionMock);

        $config = $this->getConfig()['cache_drivers']['session'];

        $service = new $config['class']($config);

        return [$service, $config];
    }
}