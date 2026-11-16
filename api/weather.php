<?php
// api/weather.php
header('Content-Type: application/json');

// Coordinates para sa Iligan City, Lanao del Norte
$lat = 8.2280;
$lon = 124.2452;

// Cache file path
$cacheFile = __DIR__ . '/weather_cache.json';
$cacheTime = 1800; // 30 minutes

// 1. Susiha kung naay cached data
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $cachedData = file_get_contents($cacheFile);
    $data = json_decode($cachedData, true);
    if (isset($data['current'])) {
        echo $cachedData;
        exit;
    }
}

// 2. Fetch sa Open-Meteo API
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Asia%2FManila";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    file_put_contents($cacheFile, $response);
    echo $response;
} else {
    // Fallback data (Iligan City sample)
    $fallbackData = [
        'current' => [
            'temperature_2m' => 29.5,
            'relative_humidity_2m' => 75,
            'weather_code' => 2,
            'wind_speed_10m' => 12.5
        ],
        'daily' => [
            'time' => [date('Y-m-d')],
            'weather_code' => [2],
            'temperature_2m_max' => [31.0],
            'temperature_2m_min' => [25.0]
        ]
    ];
    
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode($fallbackData);
    }
}