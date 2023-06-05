<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeatherServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('weather_services')->insert([
            [
                'name' => 'openweathermap',
                'url' => 'https://api.openweathermap.org/data/3.0/onecall?lat={lat}&lon={lon}&exclude=current,minutely,hourly&appid={key}',
                'api_key' => '579916a544e92f5e5efa3383f44146a8'
            ],
            [
                'name' => 'weatherapi',
                'url' => 'http://api.weatherapi.com/v1/forecast.json?key={key}&q={ip}&days=5&aqi=no&alerts=no',
                'api_key' => 'ecfa4c80cacc4e6ea1d122148230306'
            ]
        ]);
    }
}
