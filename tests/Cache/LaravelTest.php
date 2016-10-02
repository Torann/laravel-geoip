<?php

namespace Torann\GeoIP\Tests\Cache;

use Mockery;
use Torann\GeoIP\Tests\TestCase;

class LaravelTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnConfigValue()
    {
        $cacheMock = Mockery::mock('Illuminate\Cache\CacheManager');

        list($service, $config) = $this->getCache($cacheMock);

        $this->assertEquals($service->config('expires'), $config['expires']);
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

        $cacheMock = Mockery::mock('Illuminate\Cache\CacheManager');
        $cacheMock->shouldReceive('get')->with($location['ip'])->andReturn($location);
        $cacheMock->shouldReceive('tags')->with(['torann-geoip-location'])->andReturnSelf();

        list($service, $config) = $this->getCache($cacheMock);

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
        $cacheMock = Mockery::mock('Illuminate\Cache\CacheManager');
        $cacheMock->shouldReceive('get')->with('81.2.69.142')->andReturn(null);
        $cacheMock->shouldReceive('tags')->with(['torann-geoip-location'])->andReturnSelf();

        list($service, $config) = $this->getCache($cacheMock);

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

        $cacheMock = Mockery::mock('Illuminate\Cache\CacheManager');

        list($service, $config) = $this->getCache($cacheMock);

        $cacheMock->shouldReceive('put')->withArgs(['81.2.69.142', $location->toArray(), $config['expires']])->andReturn(null);
        $cacheMock->shouldReceive('tags')->with(['torann-geoip-location'])->andReturnSelf();

        $this->assertEquals($service->set('81.2.69.142', $location), null);
    }

    /**
     * @test
     */
    public function shouldFlushLocations()
    {
        $cacheMock = Mockery::mock('Illuminate\Cache\CacheManager');

        list($service, $config) = $this->getCache($cacheMock);

        $cacheMock->shouldReceive('flush')->andReturn(true);
        $cacheMock->shouldReceive('tags')->with(['torann-geoip-location'])->andReturnSelf();

        $this->assertEquals($service->flush(), true);
    }

    protected function getCache($cacheMock)
    {
        self::$functions->shouldReceive('app')->with('cache', null)->andReturn($cacheMock);

        $config = $this->getConfig()['cache_drivers']['laravel'];

        $service = new $config['class']($config);

        return [$service, $config];
    }
}