<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$data = read_json();
require_fields($data, ['student_id','booking_code']);

$sid  = trim($data['student_id']);
$code = trim($data['booking_code']);

$q = $pdo->prepare("
SELECT bk.booking_code, bk.journey_date, bk.total_amount, bk.payment_method,
    t.route_from, t.route_to, t.depart_time, t.duration_minutes, t.price,
    b.name AS bus_name,
    u.first_name, u.last_name, u.student_id
FROM bookings bk
JOIN trips t ON t.id = bk.trip_id
JOIN buses b ON b.id = t.bus_id
JOIN users u ON u.id = bk.user_id
WHERE u.student_id = :sid AND bk.booking_code = :code
LIMIT 1
");
$q->execute([':sid'=>$sid, ':code'=>$code]);
$row = $q->fetch();

if (!$row) respond(404, ['error'=>'Ticket not found']);

$seatsQ = $pdo->prepare("SELECT seat_number FROM booking_seats WHERE booking_id = (SELECT id FROM bookings WHERE booking_code = :code LIMIT 1) ORDER BY seat_number");
$seatsQ->execute([':code'=>$code]);
$seats = array_map('intval', array_column($seatsQ->fetchAll(),'seat_number'));

respond(200, [
    'ticket' => [
        'booking_code' => $row['booking_code'],
        'journey_date' => $row['journey_date'],
        'route'        => $row['route_from'].' → '.$row['route_to'],
        'time'         => substr($row['depart_time'],0,5),
        'duration'     => $row['duration_minutes'].' min',
        'bus'          => $row['bus_name'],
        'seats'        => $seats,
        'total'        => (int)$row['total_amount'],
        'payment'      => $row['payment_method'],
        'student'      => $row['first_name'].' '.$row['last_name'].' ('.$row['student_id'].')'
    ]
]);
