<?php
// api/earthquake.php
header('Content-Type: application/json');

// USGS API endpoint (Mga linog duol sa Pilipinas sa miaging 7 ka adlaw)
$url = "https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson&starttime=" . date('Y-m-d', strtotime('-7 days')) . "&minlatitude=5&maxlatitude=20&minlongitude=115&maxlongitude=130&minmagnitude=3";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DisasterReadyPH/1.0');
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    echo $response;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch earthquake data.']);
}