<?php
// api/weather.php
header('Content-Type: application/json');

// Visual Crossing: lat/lon format is "lat,lon"
$location = "14.5995,120.9842"; // Manila

// Cache file path
$cacheFile = __DIR__ . '/weather_cache.json';
$cacheTime = 1800; // 30 minutes

// 1. Check for valid cache first
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $cachedData = file_get_contents($cacheFile);
    $data = json_decode($cachedData, true);
    if (isset($data['currentConditions'])) {
        echo $cachedData;
        exit;
    }
}

// 2. Fetch from Visual Crossing (no API key)
$url = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/{$location}"
     . "?unitGroup=metric&include=current,days&contentType=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    file_put_contents($cacheFile, $response);
    echo $response;
} else {
    // 3. Fallback
    $fallbackData = [
        'currentConditions' => [
            'temp' => 'N/A',
            'humidity' => 'N/A',
            'conditions' => 'Unavailable',
            'windspeed' => 'N/A',
            'icon' => 'unknown'
        ],
        'days' => [[
            'datetime' => date('Y-m-d'),
            'tempmax' => 'N/A',
            'tempmin' => 'N/A',
            'conditions' => 'Unavailable'
        ]],
        'error_message' => 'Weather data temporarily unavailable. Showing fallback.'
    ];

    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode($fallbackData);
    }
}