<?php

ob_start(); // Start output buffering  => “Hold on, don’t spit anything out to the browser yet—even if there’s an error!”
ini_set('display_errors', 0); // Disable error display
error_reporting(E_ALL);       // Still log all errors internally

require_once 'includes/auth-user.php';
require_once 'includes/db.php';
//session_start();  //  for $_SESSION
header('Content-Type: application/json');



$order_id = $_POST['order_id'] ?? null;
$type = $_POST['type'] ?? 'full';

if (!$order_id) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID is missing.']);
    exit;
}



// Fetch the order details
// $stmt = $pdo->prepare("SELECT o.*, s.gst_price FROM orders o JOIN services s ON o.service_id = s.service_id WHERE o.order_id = ?");
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found.']);
    exit;
}

if ($order['status'] === 'expired') {
    echo json_encode(['status' => 'done', 'message' => 'This order has expired. Please generate a new order.']);
    exit;
}


$gid          = $order['gid'];
$service_id   = $order['service_id'];
$total_amount = (float)$order['total_amount'];




// Check if there's any transaction for this gid
$stmt = $pdo->prepare("SELECT SUM(amount_paid) AS paid, COUNT(*) as txn_count FROM transactions WHERE gid = ?");
$stmt->execute([$gid]);
$txn = $stmt->fetch(PDO::FETCH_ASSOC);

$amount_paid = (float)($txn['paid'] ?? 0);
$txn_exists = ($txn['txn_count'] > 0);



// Fetch partial payment info if available
$partialAmount = null;
$stmt = $pdo->prepare("SELECT amount FROM partial_payment WHERE order_id = ?");
$stmt->execute([$order_id]);
$partialRow = $stmt->fetch(PDO::FETCH_ASSOC);
$partialAmount = (float)$partialRow['amount'] ;



// Case: already paid full
if ($amount_paid >= $total_amount) {
    echo json_encode([
            'status' => 'done', 
            'message' => 'Payment already completed.'
        ]);
        exit;
}


//Case: Default full payment fallback
$finalType   = 'full';
$finalAmount =  $total_amount;
$remaining = $total_amount - $amount_paid;



//Case: When no transaction exists yet, and partial is allowed
if ($amount_paid==0 && $type === 'partial' && $partialAmount !== null) {
   
    $finalType   = 'partial';
    $finalAmount = $partialAmount;

} 

//Case:  When payment exists but full not completed
else if ($amount_paid > 0 && $amount_paid < $total_amount) {
    
    $finalType   = 'partial';
    $finalAmount = $remaining;
    
} 
   

$_SESSION['current_payment'] = [
    'order_id' => $order_id,
    'type'     => $finalType,
    'amount'   => round($finalAmount, 2)
];


echo json_encode([
    'status'  => 'proceed',
    'message' => 'Payment session initialized securely.'
]);
exit;


ob_end_clean(); // Clear any accidental output

