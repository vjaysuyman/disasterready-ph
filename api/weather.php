<?php
// api/weather.php
header('Content-Type: application/json');

// Coordinates para sa Manila (Pwede nimo usbon)
$lat = 14.5995;
$lon = 120.9842;

// Open-Meteo API endpoint
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Asia%2FManila";

// Pag-fetch gamit ang cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    echo $response;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch weather data.']);
}