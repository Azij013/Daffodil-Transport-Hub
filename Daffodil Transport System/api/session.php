<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$user = $_SESSION['user'] ?? null;
respond(200, ['user' => $user]);
<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$user = $_SESSION['user'] ?? null;
respond(200, ['user' => $user]);
