<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$data = read_json();
require_fields($data, ['first_name','last_name','email','username','password']);

$first = trim($data['first_name']);
$last  = trim($data['last_name']);
$email = trim($data['email']);
$usern = trim($data['username']);
$pass  = $data['password'];
$student_id = isset($data['student_id']) ? trim($data['student_id']) : null;
$role  = isset($data['role']) && in_array($data['role'], ['student','faculty','driver','admin']) ? $data['role'] : 'student';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(422, ['error' => 'Invalid email']);
}

$exists = $pdo->prepare("SELECT 1 FROM users WHERE username=:u OR email=:e LIMIT 1");
$exists->execute([':u'=>$usern, ':e'=>$email]);
if ($exists->fetch()) {
    respond(409, ['error' => 'Username or email already exists']);
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO users (first_name,last_name,username,email,student_id,role,password_hash)
                    VALUES (:f,:l,:u,:e,:sid,:r,:ph)");
$ins->execute([
    ':f'=>$first, ':l'=>$last, ':u'=>$usern, ':e'=>$email,
    ':sid'=>$student_id, ':r'=>$role, ':ph'=>$hash
]);

$id = (int)$pdo->lastInsertId();
$user = [
    'id'=>$id,'first_name'=>$first,'last_name'=>$last,'username'=>$usern,
    'email'=>$email,'student_id'=>$student_id,'role'=>$role
];

// Auto-login (optional)
session_regenerate_id(true);
$_SESSION['user'] = $user;

respond(201, ['user' => $user]);
