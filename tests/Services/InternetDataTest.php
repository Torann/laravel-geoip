<?php

namespace Torann\GeoIP\Tests\Services;

use Mockery;
use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Tests\TestCase;

class InternetDataTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnValidLocation()
    {
        $service = $this->getService(json_encode([
            'ip' => '8.8.8.8',
            'location' => [
                'continent' => 'NA',
                'country' => 'US',
                'country_name' => 'United States',
                'timezone' => 'America/Los_Angeles',
                'currency_code' => 'USD',
            ],
            'asn' => ['asn' => 15169, 'name' => 'Google LLC'],
        ]));

        $location = $service->locate('8.8.8.8');

        $this->assertInstanceOf(\Torann\GeoIP\Location::class, $location);
        $this->assertEquals('8.8.8.8', $location->ip);
        $this->assertEquals('US', $location->iso_code);
        $this->assertEquals('United States', $location->country);
        $this->assertEquals('America/Los_Angeles', $location->timezone);
        $this->assertEquals('NA', $location->continent);
        $this->assertEquals('USD', $location->currency);
        $this->assertNull($location->city);
        $this->assertEquals(false, $location->default);
    }

    /**
     * @test
     */
    public function shouldThrowOnFailedRequest()
    {
        $service = $this->getService(null, 'Could not resolve host');

        $this->expectException(\Exception::class);

        $service->locate('8.8.8.8');
    }

    protected function getService($body, $error = null)
    {
        $config = $this->getConfig()['services']['internetdata'];

        $service = new $config['class']($config);

        $client = Mockery::mock(HttpClient::class);
        $client->shouldReceive('get')->with('8.8.8.8')->andReturn([$body, []]);
        $client->shouldReceive('getErrors')->andReturn($error);

        $property = new \ReflectionProperty($service, 'client');
        $property->setAccessible(true);
        $property->setValue($service, $client);

        return $service;
    }
}
