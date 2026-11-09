<?php
// api/weather.php
header('Content-Type: application/json');

$lat = 14.5995;
$lon = 120.9842;
$apiKey = $_ENV['OPENWEATHER_API_KEY'] ?? '';

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured.']);
    exit;
}

// Cache file path
$cacheFile = __DIR__ . '/weather_cache.json';
$cacheTime = 1800; // 30 minutes

// 1. Susiha kung naay cached data
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    echo file_get_contents($cacheFile);
    exit;
}

// 2. Fetch sa OpenWeatherMap API
$url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

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
    // Fallback data kung mag-error
    echo json_encode([
        'main' => ['temp' => 'N/A', 'humidity' => 'N/A'],
        'wind' => ['speed' => 'N/A'],
        'weather' => [['description' => 'Unavailable', 'icon' => '01d']]
    ]);
}