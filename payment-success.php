<?php
require_once 'includes/auth-user.php';
require_once 'includes/db.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/sendMail.php';


$payment_id = $_GET['payment_id'];
//$razorpay_order_id = $_GET['razorpay_order_id'] ;
$order_id = $_GET['order_id'] ;


if (!$payment_id || !$order_id) { // || !$razorpay_order_id
    die("Missing payment data");
}

//status
$stmt = $pdo->prepare("SELECT gid, service_id FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);


$gid = $order['gid'];
$service_id = $order['service_id'];


//API Calling from razorpay to fetch amount
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/' . $payment_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, 'rzp_live_96vB6NzNb1i94F:rYuE1BJ25rGt4xu6PCAmfmmT');  // use env file in real projects, :- key_id, key_secret
$response = curl_exec($ch);    
curl_close($ch);

$payment_data = json_decode($response, true);
$amount_paid = $payment_data['amount'] / 100;  // in INR
// $timestamp = $payment_data['created_at'];
$datetime = gmdate("c", $payment_data['created_at']);

$printToken = bin2hex(random_bytes(16));// Generate secure print token

$insert = $pdo->prepare("Insert into transactions (gid, payment_id, amount_paid, datetime, service_id, print_token) values (:gid, :payment_id, :amount_paid, :datetime, :service_id, :print_token)");
$insert->execute([
    //'razorpay_order_id' => $razorpay_order_id,
    'gid' => $gid,
    'payment_id' => $payment_id,
    'amount_paid' => $amount_paid,
    'datetime' => $datetime,
    'service_id' => $service_id,
    'print_token' => $printToken
]);


// sending success mail
$stmt = $pdo->prepare("
    SELECT c.email, c.name, s.service_name
    FROM consumers c
    JOIN orders o ON c.consumer_id = o.consumer_id
    JOIN services s ON o.service_id = s.service_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    sendPaymentSuccessMail(
        $data['email'],
        $data['name'],
        $data['service_name'],
        $payment_id,
        $amount_paid
    );
}




//Fetch total amount for this order
$stmt = $pdo->prepare("SELECT total_amount FROM orders WHERE gid = ? "); 
$stmt->execute([$gid]);
$orderAmountRow = $stmt->fetch(PDO::FETCH_ASSOC);
$total_amount = (float)($orderAmountRow['total_amount'] );


//Fetch total paid so far 
$stmt = $pdo->prepare("SELECT SUM(amount_paid) AS paid FROM transactions WHERE gid = ? ");
$stmt->execute([$gid]);
$txn = $stmt->fetch(PDO::FETCH_ASSOC);
$paid_so_far = (float)($txn['paid'] ?? 0);


if ($paid_so_far >= $total_amount) {
    $new_status = 'paid';
} elseif ($paid_so_far > 0 && $paid_so_far < $total_amount ) {
    $new_status = 'partial';
}

//Update status in orders table
$update = $pdo->prepare("UPDATE orders SET status = ? WHERE gid = ?");
$update->execute([$new_status, $gid]);


echo "<h2>Payment successful!</h2>";


header("Location: cons-login.php");  
exit();
?>
