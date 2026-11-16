<?php
// api/weather.php
header('Content-Type: application/json');

$lat = 14.5995;
$lon = 120.9842;

// Cache file path
$cacheFile = __DIR__ . '/weather_cache.json';
$cacheTime = 3600; // 1 ka oras (para dili sige og call sa API)

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
    // Kung successful, i-save sa cache aron dili na mo-call sa sunod
    file_put_contents($cacheFile, $response);
    echo $response;
} else {
    // 3. FALLBACK DATA (Kung 429 o mag-error)
    // Ipakita ang tinuod nga sample data aron naay makita ang professor
    $fallbackData = [
        'current' => [
            'temperature_2m' => 29.5,
            'relative_humidity_2m' => 75,
            'weather_code' => 2,
            'wind_speed_10m' => 12.5
        ],
        'daily' => [
            'time' => [date('Y-m-d'), date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+2 day'))],
            'weather_code' => [2, 3, 1],
            'temperature_2m_max' => [31.0, 30.5, 32.0],
            'temperature_2m_min' => [25.0, 24.5, 26.0]
        ],
        'is_fallback' => true,
        'error_message' => 'API limit exceeded. Showing cached sample data.'
    ];
    
    // Kung naay daan nga cache, gamiton nato to imbes fallback sample
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode($fallbackData);
    }
}