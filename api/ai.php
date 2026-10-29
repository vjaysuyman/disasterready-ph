<?php
// api/ai.php
require_once '../config/database.php';
header('Content-Type: application/json');

$apiKey = $_ENV['GROQ_API_KEY'] ?? '';

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'Groq API key not configured.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? 'Hello';

$url = "https://api.groq.com/openai/v1/chat/completions";

$data = [
    "model" => "openai/gpt-oss-120b",
    "messages" => [
        ["role" => "system", "content" => "You are a disaster preparedness assistant for the Philippines. Provide practical, concise safety advice. Always remind users to follow official government advisories."],
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.7,
    "max_tokens" => 500
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $response) {
    echo $response;
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to communicate with Groq AI.',
        'http_code' => $httpcode,
        'response' => json_decode($response, true)
    ]);
}