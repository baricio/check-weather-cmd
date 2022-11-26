<?php

namespace App\Commands;

use App\Services\GeolocationApiService;
use App\Services\NslookupService;
use App\Services\WeatherApiService;
use LaravelZero\Framework\Commands\Command;

class CheckWeatherCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'check:temperature';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check temperature for next week';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $ip = (new NslookupService)->getPublicIp();
            $geoLocation = new GeolocationApiService($ip);
            $weather = new WeatherApiService();
            $weather->fillDailyTemperature($geoLocation);
            $this->info("{$geoLocation->city}, {$geoLocation->regionName}, {$geoLocation->country}");
            $this->table($weather->weekLabels, $weather->weekTemperature);
        } catch (\Throwable $th) {
            $this->newLine();
            $this->error($th->getMessage());
        }
    }
}
