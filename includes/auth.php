<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if user is logged in
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

// 2. Check if user is Admin
function is_admin(): bool {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

// 3. Require Login (Kung wala naka-login, ibalik sa login page)
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /disasterready/login.php');
        exit;
    }
}

// 4. Require Admin (Kung residente ra, bawalan mosulod sa admin pages)
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die("Access Denied. Admin privileges required.");
    }
}

// 5. Generate CSRF Token
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 6. Verify CSRF Token
function verify_csrf_token(?string $token): void {
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die("Invalid CSRF token. Request blocked.");
    }
}