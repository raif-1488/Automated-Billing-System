<?php
$anyRemainderSent = false;

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/sendMail.php';


function logRenewalReminderIfNotSent($pdo, $order_id, $service_id, $daysLeft) 
{
    try{  
        $insertStmt = $pdo->prepare("INSERT INTO reminder_logs 
                (order_id,service_id, type, day, sent_on) 
                VALUES ( ?, ?, 'renewal', ?, CURDATE())"
            );
            
        $insertStmt->execute([$order_id, $service_id, $daysLeft]);
        return true;

    }
catch (PDOException $e) {

        if($e->getCode() == '23000') {
                return false; //logged already
        }
        throw $e;
 }
}

$today = new DateTime();

$stmt = $pdo->prepare("SELECT o.*, s.service_id, s.service_name, s.billing_period,
            s.billing_unit, o.order_id, o.order_date,
            c.consumer_id, c.name AS consumer_name, c.email AS consumer_email
            FROM orders o
            JOIN services s ON o.service_id = s.service_id
            JOIN consumers c ON o.consumer_id = c.consumer_id
            WHERE o.status = 'paid'
        ");

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($orders as $order) {
    $orderDate = new DateTime($order['order_date']); //making date time object 
    $billingPeriod = (int) $order['billing_period'];
    $billingUnit = rtrim(strtolower($order['billing_unit']), 's');

    $expiryDate = (clone $orderDate)->modify("+$billingPeriod $billingUnit")->modify('-1 day')->setTime(23, 59, 59);
    $daysLeft = (int) $today->diff($expiryDate)->format('%r%a'); //r for '+' or '-' and a for numeric value
    $service_id = $order['service_id'];
    
    if(in_array($daysLeft, [30, 15, 7]) || $daysLeft <= 3 && $daysLeft >= 1){
        $logged = logRenewalReminderIfNotSent(
            $pdo,
            $order['order_id'],
            $order['service_id'],
            $daysLeft
        );
        

        if ($logged) {
           // echo "hiiii";
            $anyRemainderSent = true;
            sendRenewalReminder($order['consumer_email'], $order['consumer_name'], $order['service_name'], $expiryDate, $daysLeft);
            //echo "khana";
            usleep(23000);
        }
    }

}
if(!$anyRemainderSent){
        echo "<p style='color: grey; font-weight: bold;'>All customers already notified today.</p>";
    }

?>
