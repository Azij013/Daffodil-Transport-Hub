<?php
require __DIR__ . '/config.php';
require __DIR__ . '/_init.php';

// We wrap everything in a try/catch to see any hidden errors
try {
    $user = require_login();
    $data = read_json();
    require_fields($data, ['trip_id','date','seats','payment_method']);

    $trip_id = (int)$data['trip_id'];
    $date    = $data['date'];
    $seats   = array_values(array_unique(array_map('intval', $data['seats'])));
    $pay     = trim($data['payment_method']);

    $pdo->beginTransaction();

    // Check for trip existence first
    $tripQ = $pdo->prepare("SELECT price FROM trips WHERE id = :tid");
    $tripQ->execute([':tid'=>$trip_id]);
    $trip = $tripQ->fetch();
    if (!$trip) {
        throw new Exception("Trip ID not found in database.");
    }

    // Insert the main booking record
    $ticket_price = (int)$trip['price'] * count($seats);
    $total = $ticket_price + 5;
    $code = 'DIU' . substr(strtoupper(bin2hex(random_bytes(4))), 0, 5);

    $insB = $pdo->prepare(
        "INSERT INTO bookings (user_id, trip_id, journey_date, booking_code, payment_method, service_charge, total_amount)
         VALUES (:uid, :tid, :jdate, :code, :pay, 5, :tot)"
    );
    $insB->execute([
        ':uid'   => $user['id'],
        ':tid'   => $trip_id,
        ':jdate' => $date,
        ':code'  => $code,
        ':pay'   => $pay,
        ':tot'   => $total
    ]);

    // Check if the insert ACTUALLY worked
    $booking_id = (int)$pdo->lastInsertId();
    if ($booking_id === 0) {
        // This is a critical failure check
        $pdo->rollBack();
        throw new Exception("CRITICAL ERROR: The main booking record failed to insert. lastInsertId() returned zero.");
    }

    // Insert the seats
    $insS = $pdo->prepare(
        "INSERT INTO booking_seats (booking_id, trip_id, journey_date, seat_number)
         VALUES (:bid, :tid, :jdate, :sn)"
    );
    foreach ($seats as $sn) {
        $insS->execute([':bid'=>$booking_id, ':tid'=>$trip_id, ':jdate'=>$date, ':sn'=>$sn]);
    }

    // If we get here, everything was inserted. Now commit.
    $pdo->commit();

    // Respond with success
    respond(201, ['booking' => ['booking_code' => $code, 'total' => $total]]);

} catch (Throwable $e) {
    // If ANY error happens anywhere above, we will catch it here.
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // This will now show us the REAL error message.
    respond(500, ['error' => 'A server error occurred: ' . $e->getMessage()]);
}