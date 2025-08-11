<?php
$anyRemainderSent = false;
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/sendMail.php';

// var_dump($_SESSION);

function logPaymentReminderIfNotSent($pdo, $order_id, $service_id, $daysSince) {
    try {
        //echo "func";
        $insertStmt = $pdo->prepare("INSERT INTO reminder_logs 
            (order_id, service_id, type, day, sent_on) 
            VALUES (?, ?, 'payment', ?, CURDATE())");

        $insertStmt->execute([$order_id, $service_id, $daysSince]);
        return true;
    } 
    catch (PDOException $e) {
        if ($e->getCode() == '23000') { // Duplicate entry
            return false;
        }
        throw $e;
    }
}

$today = new DateTime();

$stmt = $pdo->prepare("SELECT o.*, s.service_id, s.service_name, c.name AS consumer_name, c.email AS consumer_email
                       FROM orders o
                       JOIN services s ON o.service_id = s.service_id
                       JOIN consumers c ON o.consumer_id = c.consumer_id
                       WHERE o.status = 'pending' OR o.status = 'partial'");

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($orders as $order) {
    $orderDate = new DateTime($order['order_date']);  //date time object to ease the manipulations and modifications with dates, raw strings don't allow that
    $daysSince = (int)$orderDate->diff($today)->format('%r%a');
    $service_id = $order['service_id'];

    //echo "Order ID: {$order['order_id']} | Days Since: $daysSince<br>";


    if (in_array($daysSince, [0, 1, 2])) {
       // echo "HIIII";
        $logged = logPaymentReminderIfNotSent(
            $pdo,
            $order['order_id'],
            $order['service_id'],
            $daysSince
        );
        

        if ($logged) {
            $anyRemainderSent = true;
            sendPaymentReminder(
                $order['consumer_email'],
                $order['consumer_name'],
                $order['service_name'],
                $order['gid'],
                $daysSince
            );
            usleep(23000);
        }
    } 
    elseif ($daysSince >= 3) {
        // Expire the order
        $update = $pdo->prepare("UPDATE orders SET status = 'expired' WHERE order_id = ?");
        $update->execute([$order['order_id']]);
        echo "{$order['consumer_name']} ({$order['service_name']}) Order ID: {$order['gid']} has expired.<br>";
    }
}
if(!$anyRemainderSent){
        echo "<p style='color: grey; font-weight: bold;'>All customers already notified today.</p>";
    }
?>
