<?php
// api/weather.php
header('Content-Type: application/json');

$lat = 14.5995;
$lon = 120.9842;

// Cache file path
$cacheFile = __DIR__ . '/weather_cache.json';
$cacheTime = 1800; // 30 minutos

// 1. Susiha kung naay cached data ug wala pa ma-expire
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $cachedData = file_get_contents($cacheFile);
    $data = json_decode($cachedData, true);
    // Kung naay valid data sa cache, i-return kini dayon
    if (isset($data['current'])) {
        echo $cachedData;
        exit;
    }
}

// 2. Kung wala nay cache, fetch sa Open-Meteo API
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Asia%2FManila";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    // I-save sa cache aron dili na mo-call og balik sa sunod
    file_put_contents($cacheFile, $response);
    echo $response;
} else {
    // 3. Fallback data kung maabot ang limit (429) o mag-error
    $fallbackData = [
        'current' => [
            'temperature_2m' => 'N/A',
            'relative_humidity_2m' => 'N/A',
            'weather_code' => 0,
            'wind_speed_10m' => 'N/A'
        ],
        'daily' => [
            'time' => [date('Y-m-d')],
            'weather_code' => [0],
            'temperature_2m_max' => ['N/A'],
            'temperature_2m_min' => ['N/A']
        ],
        'error_message' => 'Weather data temporarily unavailable (API limit). Showing fallback.'
    ];
    
    // Kung naay daan nga cache, mas maayo gamiton to nato imbes fallback
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode($fallbackData);
    }
}