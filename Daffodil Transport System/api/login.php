<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$data = read_json();
require_fields($data, ['username', 'password']);

$u = trim($data['username']);
$p = $data['password'];

$stmt = $pdo->prepare("SELECT id, first_name, last_name, username, email, student_id, role, password_hash
                      FROM users WHERE username = :u OR email = :u LIMIT 1");
$stmt->execute([':u' => $u]);
$user = $stmt->fetch();

if (!$user || !password_verify($p, $user['password_hash'])) {
    respond(401, ['error' => 'Invalid username/email or password']);
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'id'         => $user['id'],
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
    'username'   => $user['username'],
    'email'      => $user['email'],
    'student_id' => $user['student_id'],
    'role'       => $user['role'],
];

respond(200, ['user' => $_SESSION['user']]);