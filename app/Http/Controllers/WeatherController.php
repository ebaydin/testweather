<?php

namespace App\Http\Controllers;

use App\Models\WeatherResult;
use Illuminate\Http\Request;
use App\Models\WeatherService;
use App\Models\WeatherQuery;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function weather (Request $request) {

        if($request->ip){
            $validated = $request->validate([
                'ip' => 'required|ip'
            ]);
            $ip = $request->ip;
        }
        else{
            $ip = file_get_contents('https://ipapi.co/ip/');
        }

        $latlong = explode(",", file_get_contents("https://ipapi.co/{$ip}/latlong/"));
        //print_r($latlong); die();
        if($latlong[0] == "None" || $latlong[1] == "None"){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'ip' => ["Can't find the location"],
            ]);
            throw $error;
        }

        $serviceOne = WeatherService::where('name', 'openweathermap')->first();
        $dayQuery = WeatherQuery::firstOrCreate([
            'service_id' => $serviceOne->service_id,
            'date' => date("Y-m-d"),
            'ip_address' => $ip,
            'latitude' => $latlong[0],
            'longitude' => $latlong[1]
        ]);
        if ($dayQuery->wasRecentlyCreated) {
            $dayQuery = WeatherQuery::latest()->first();
        }

        $countResults = WeatherResult::where('query_id', $dayQuery->query_id)->count();

        if (!$countResults) {
            $apiUrl = str_replace(['{lat}', '{lon}', '{key}'], [$dayQuery->latitude, $dayQuery->longitude, $serviceOne->api_key], $serviceOne->url);

            $response = Http::get($apiUrl);
            $bulk = $response->json();

            foreach ($bulk["daily"] as $key => $item) {
                if ($key > 4) break; //only 5 days
                $result = new WeatherResult();
                $result->query_id = $dayQuery->query_id;
                $result->date = $item["dt"];
                $result->description = $item["weather"][0]["description"];
                $result->max_c = round($item["temp"]["max"] - 273.15, 1);
                $result->min_c = round($item["temp"]["min"] - 273.15, 1);
                $result->pressure = $item["pressure"];
                $result->maxwind_ms = $item["wind_speed"];
                $result->humidity = $item["humidity"];
                $result->uv = $item["uvi"];
                $result->icon = "http://openweathermap.org/img/w/" . $item["weather"][0]["icon"] . ".png";
                $result->save();
            }
        }

        $weatherSummary['ip'] = $ip;
        $weatherSummary['coordinates']['lat'] = $dayQuery->latitude;
        $weatherSummary['coordinates']['lon'] = $dayQuery->longitude;
        $weatherSummary['coordinates']['source'] = 'ipapi.co';
        $weatherSummary['forecast'] = WeatherResult::where('query_id', $dayQuery->query_id)->get()->toArray();



        $serviceTwo = WeatherService::where('name', 'weatherapi')->first();
        $dayQueryTwo = WeatherQuery::firstOrCreate([
            'service_id' => $serviceTwo->service_id,
            'date' => date("Y-m-d"),
            'ip_address' => $ip,
        ]);
        if ($dayQueryTwo->wasRecentlyCreated) {
            $dayQueryTwo = WeatherQuery::latest()->first();
        }
        $countResults = WeatherResult::where('query_id', $dayQueryTwo->query_id)->count();
        if (!$countResults) {

            $apiUrl = str_replace(['{ip}', '{key}'], [$ip, $serviceTwo->api_key], $serviceTwo->url);
            $response = Http::get($apiUrl);
            $bulk = $response->json();

            foreach ($bulk["forecast"]["forecastday"] as $item) {
                $result = new WeatherResult();
                $result->query_id = $dayQueryTwo->query_id;
                $result->date = $item["date_epoch"];
                $result->description = $item["day"]["condition"]["text"];
                $result->max_c = round($item["day"]["maxtemp_c"], 1);
                $result->min_c = round($item["day"]["mintemp_c"], 1);
                $result->pressure = $item["hour"][8]["pressure_mb"];
                $result->maxwind_ms = round($item["day"]["maxwind_mph"] / 3.6, 2);
                $result->humidity = $item["day"]["avghumidity"];
                $result->uv = $item["day"]["uv"];
                $result->icon = $item["day"]["condition"]["icon"];
                $result->save();
            }
            $dayQueryTwo->latitude = $bulk['location']['lat'];
            $dayQueryTwo->longitude = $bulk['location']['lon'];
            $dayQueryTwo->save();
        }

        $weatherSummaryTwo['ip'] = $ip;
        $weatherSummaryTwo['coordinates']['lat'] = $dayQueryTwo->latitude;
        $weatherSummaryTwo['coordinates']['lon'] = $dayQueryTwo->longitude;
        $weatherSummaryTwo['coordinates']['source'] = 'weatherapi.com';
        $weatherSummaryTwo['forecast'] = WeatherResult::where('query_id', $dayQueryTwo->query_id)->get()->toArray();

        return view('weather', ['bulkone' => $weatherSummary, 'bulktwo' => $weatherSummaryTwo, 'ip' => $ip]);
    }
}
