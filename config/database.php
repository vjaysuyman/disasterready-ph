<?php
// config/database.php

/**
 * Simple .env loader.
 */
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

// Kuhaon ang mga variables (apil ang DB_PORT)
$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbName = $_ENV['DB_NAME'] ?? 'defaultdb';
$dbUser = $_ENV['DB_USER'] ?? 'root';
$dbPass = $_ENV['DB_PASS'] ?? '';
$dbPort = $_ENV['DB_PORT'] ?? '3306'; // Bag-o: Kuhaon ang port

// I-apil ang port sa DSN
$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Importante: Idugang ang CA certificate para maka-connect sa Aiven
$caPath = __DIR__ . '/ca.pem';
if (file_exists($caPath)) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // Sa Render, makita nimo ang tinuod nga error sa "Logs" tab
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('A database error occurred. Please try again later.');
}