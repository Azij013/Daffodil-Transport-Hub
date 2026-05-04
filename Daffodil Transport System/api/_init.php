<?php
// api/_init.php
if (session_status() === PHP_SESSION_NONE) {
    // For local dev without HTTPS
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => false,     // set true in production (HTTPS)
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function read_json(): array {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }
    }
    return $_POST ? $_POST : [];
}

function respond(int $code, array $payload = []): void {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

function require_fields(array $data, array $fields): void {
    foreach ($fields as $f) {
        if (!isset($data[$f]) || $data[$f] === '') {
            respond(422, ['error' => "Missing field: $f"]);
        }
    }
}

function require_login(): array {
    if (empty($_SESSION['user'])) {
        respond(401, ['error' => 'Unauthorized']);
    }
    return $_SESSION['user'];
}
