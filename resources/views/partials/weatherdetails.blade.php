<div>
    <h2 class="text-xl">{{$slot}}</h2>
    <p>IP:{{$bulk['ip']}}</p>
    <p>Coordinates: {{$bulk['coordinates']['lat']}} : {{$bulk['coordinates']['lon']}} taken from {{$bulk['coordinates']['source']}}</p>
</div>
            <?php foreach ($bulk['forecast'] as $data): ?>
            <div class="report-container bg-white p-6 bg-white rounded-lg ">
                <h3 class="text-xl font-semibold text-gray-900 "><?php echo date("l jS F, Y", $data['date']); ?></h3>
                <div class= "small-info">
                    <div class="flex">
                        <span>
                            <img src="<?= $data['icon']; ?>" class= "weather-icon" />
                        </span>
                        <span class="inline-block align-middle weather-description">
                            <?php echo ucwords($data['description']); ?>
                        </span>
                    </div>

                </div>
                <div class="weather-forecast">
                    <span class="min-temperature">Min: <?php echo $data['min_c']; ?>&deg;C    Max: <?php echo $data['max_c']; ?>&deg;C</span>
                </div>
                <div class="small-info">
                    <p>Humidity: <?php echo $data['humidity']; ?> %</p>
                    <p>Wind: <?php echo $data['maxwind_ms']; ?> m/s</p>
                    <p>Pressure: <?php echo $data['pressure']; ?> hPa</p>
                    <p>UV: <?php echo $data['uv']; ?></p>
                </div>
            </div>
            <?php endforeach;?>
