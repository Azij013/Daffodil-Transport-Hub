<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$data = read_json();
if (!$data) { // allow GET for convenience
    $data = ['trip_id'=>$_GET['trip_id'] ?? null, 'date'=>$_GET['date'] ?? null];
}
require_fields($data, ['trip_id','date']);

$trip_id = (int)$data['trip_id'];
$date    = $data['date'];

$info = $pdo->prepare("SELECT b.seat_count
                    FROM trips t JOIN buses b ON b.id = t.bus_id
                    WHERE t.id = :tid");
$info->execute([':tid'=>$trip_id]);
$seat_count = (int)$info->fetchColumn();
if ($seat_count <= 0) {
    respond(404, ['error'=>'Trip not found']);
}

$occ = $pdo->prepare("
SELECT bs.seat_number
FROM bookings bk
JOIN booking_seats bs ON bs.booking_id = bk.id
WHERE bk.trip_id = :tid AND bk.journey_date = :jdate
ORDER BY bs.seat_number
");
$occ->execute([':tid'=>$trip_id, ':jdate'=>$date]);
$occupied = array_map('intval', array_column($occ->fetchAll(), 'seat_number'));

respond(200, ['seat_count'=>$seat_count, 'occupied'=>$occupied]);
