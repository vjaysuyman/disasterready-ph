<?php
// api/weather.php
header('Content-Type: application/json');

$lat = 14.5995;
$lon = 120.9842;

// Fetch sa Open-Meteo API (Walay cache para makita dayon ang tinuod nga status)
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Asia%2FManila";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($httpcode === 200 && $response) {
    // Malampuson! I-return ang live data
    echo $response;
} else {
    // Kung mapakyas, ipakita ang tinuod nga error para ma-debug nato
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch weather data from Open-Meteo.',
        'http_code' => $httpcode,
        'curl_error' => $curl_error,
        'raw_response' => $response
    ]);
}