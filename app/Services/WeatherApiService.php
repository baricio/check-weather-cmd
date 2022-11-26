<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherApiService
{
    public $weather;
    public $minUnit;
    public $maxUnit;
    public $weekTemperature;
    public $weekLabels = [
        'Date',
        'min',
        'max'
    ];

    public function fillDailyTemperature(GeolocationApiService $geoLocation)
    {
        $this->weather = $this->callWeatherApi($geoLocation);
        $this->minUnit = data_get($this->weather, 'daily_units.temperature_2m_min');
        $this->maxUnit = data_get($this->weather, 'daily_units.temperature_2m_max');
        $this->weekTemperature = $this->fillWeekTemperature();
    }

    private function fillWeekTemperature()
    {
        return collect(data_get($this->weather, 'daily.time'))
            ->map(function ($value, $index) {
                    $min = data_get($this->weather, "daily.temperature_2m_min.$index");
                    $max = data_get($this->weather, "daily.temperature_2m_max.$index");
                    $data = [
                        $value,
                        "$min{$this->minUnit}",
                        "$max{$this->maxUnit}"
                    ];
                    return $data;
                }
            );
    }

    private function callWeatherApi(GeolocationApiService $geoLocation): array
    {
        $client = new Client(['base_uri' => 'https://api.open-meteo.com/v1/forecast']);
        $params = [];
        data_set($params, 'latitude', $geoLocation->lat);
        data_set($params, 'longitude', $geoLocation->lon);
        data_set($params, 'timezone', $geoLocation->timezone);
        data_set($params, 'daily', 'temperature_2m_max,temperature_2m_min');
        $query = urldecode(http_build_query($params));
        $response = $client->request('GET', "?$query");
        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }
}
