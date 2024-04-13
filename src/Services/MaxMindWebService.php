<?php

namespace Torann\GeoIP\Services;

use GeoIp2\Model\City;
use GeoIp2\WebService\Client;
use Illuminate\Support\Arr;

class MaxMindWebService extends AbstractService
{
    /**
     * Service client instance.
     *
     * @var \GeoIp2\WebService\Client
     */
    protected $client;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        $this->client = new Client(
            $this->config('user_id'),
            $this->config('license_key'),
            $this->config('locales', ['en'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function locate($ip)
    {
        $record = $this->client->city($ip);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => $record->country->isoCode,
            'country' => $record->country->name,
            'city' => $record->city->name,
            'state' => $record->mostSpecificSubdivision->isoCode,
            'state_name' => $record->mostSpecificSubdivision->name,
            'postal_code' => $record->postal->code,
            'lat' => $record->location->latitude,
            'lon' => $record->location->longitude,
            'timezone' => $record->location->timeZone,
            'continent' => $record->continent->code,
            'localizations' => $this->getLocallizations($record),
        ]);
    }

    /**
     * Get localized country name, state name and city name based on config languages
     *
     * @param City $record
     * @return array
     */
    private function getLocallizations(City $record)
    {
        $locales = [];

        foreach ($this->config('locales') as $lang) {
            $locales[$lang]['country'] = Arr::get($record->country->names, $lang);
            $locales[$lang]['state_name'] = Arr::get($record->mostSpecificSubdivision->names, $lang);
            $locales[$lang]['city'] = Arr::get($record->city->names, $lang);
        }

        return $locales;
    }
}