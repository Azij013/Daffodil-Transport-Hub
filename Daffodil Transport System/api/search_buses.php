<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

$data = read_json();
require_fields($data, ['from','to','time']); // <-- Now requires 'time'

$from = trim($data['from']);
$to   = trim($data['to']);
$time = trim($data['time']); // <-- Reads the time
$date = date('Y-m-d'); // <-- Gets today's date automatically

$stmt = $pdo->prepare("
SELECT t.id AS trip_id, t.route_from, t.route_to, t.depart_time, t.duration_minutes, t.price,
    b.name AS bus_name, b.seat_count, b.type AS bus_type, b.amenities
FROM trips t
JOIN buses b ON b.id = t.bus_id
WHERE t.active=1 AND b.active=1
AND t.route_from = :f AND t.route_to = :t AND t.depart_time = :time
ORDER BY t.depart_time
");
$stmt->execute([':f'=>$from, ':t'=>$to, ':time'=>$time]); // <-- Adds time to the query
// ... rest of the file is unchanged and will work correctly
$rows = $stmt->fetchAll();

$result = [];
if ($rows) {
    $seatStmt = $pdo->prepare("
    SELECT COUNT(bs.seat_number) AS booked
    FROM bookings bk
    LEFT JOIN booking_seats bs ON bs.booking_id = bk.id
    WHERE bk.trip_id = :tid AND bk.journey_date = :jdate
    ");
    foreach ($rows as $r) {
        $seatStmt->execute([':tid'=>$r['trip_id'], ':jdate'=>$date]);
        $booked = (int)$seatStmt->fetchColumn();
        $available = max(0, (int)$r['seat_count'] - $booked);
        $result[] = [
            'trip_id' => (int)$r['trip_id'],
            'name'    => $r['bus_name'],
            'time'    => substr($r['depart_time'],0,5),
            'seats'   => $available,
            'price'   => (int)$r['price'],
            'type'    => $r['bus_type'],
            'duration'=> $r['duration_minutes'].' min',
            'amenities' => array_filter(array_map('trim', explode(',', (string)$r['amenities']))),
            'seat_count' => (int)$r['seat_count'],
        ];
    }
}

respond(200, ['buses' => $result]);
