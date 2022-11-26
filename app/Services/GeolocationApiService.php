<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeolocationApiService
{
    public $lat;
    public $lon;
    public $city;
    public $regionName;
    public $country;
    public $timezone;
    public $ip;

    public function __construct(string $ip)
    {
        $this->ip = $ip;
        $this->fillGeopLocation();
    }

    public function fillGeopLocation()
    {
        $geoInfo = $this->callGeolocationApi();
        $this->lat = (float) data_get($geoInfo, 'geoplugin_latitude');
        $this->lon = (float) data_get($geoInfo, 'geoplugin_longitude');
        $this->city = data_get($geoInfo, 'geoplugin_city');
        $this->regionName = data_get($geoInfo, 'geoplugin_regionName');
        $this->country = data_get($geoInfo, 'geoplugin_countryName');
        $this->timezone = data_get($geoInfo, 'geoplugin_timezone');
    }

    private function callGeolocationApi(): array
    {
        $client = new Client(['base_uri' => 'http://www.geoplugin.net/json.gp']);
        $response = $client->request(
            'GET',
            '',
            [
                ['query' => ['ip' => $this->ip]]
            ]
        );
        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }
}
