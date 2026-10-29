<?php
// api/news.php
require_once '../config/database.php'; // Para mabasa ang .env
header('Content-Type: application/json');

$apiKey = $_ENV['CURRENTS_API_KEY'] ?? '';

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'News API key not configured.']);
    exit;
}

// Currents API endpoint (Pangita og balita bahin sa disaster sa Pilipinas)
$url = "https://api.currentsapi.services/v1/search?keywords=disaster,typhoon,earthquake,Philippines&language=en&apiKey={$apiKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    echo $response;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch news data.']);
}