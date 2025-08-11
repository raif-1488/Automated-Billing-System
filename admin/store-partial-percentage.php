<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo "Invalid request method";
    exit();
}


$order_id = $_POST['order_id'] ?? null;
$service_id = $_POST['service_id'] ?? null;
$total_amount = $_POST['total_amount'] ?? null;
$percent = $_POST['percent'] ?? null;

if (!$order_id || !$service_id || !$total_amount || $percent === null) {
    http_response_code(400); 
    echo "Missing required fields.";
    exit();
}

// Remove previous partial payment (if any)
$pdo->prepare("DELETE FROM partial_payment WHERE order_id = ? AND service_id = ?")
    ->execute([$order_id, $service_id]);


if (is_numeric($percent) && $percent > 0) {
    $partial_amount = ($percent / 100) * $total_amount;

    // $stmt = $pdo->prepare("INSERT INTO partial_payment (order_id, service_id, amount, percentage) VALUES (?, ?, ?, ?)");
    $stmt = $pdo->prepare("INSERT INTO partial_payment (order_id, service_id, amount, percentage) VALUES (?, ?, ?, ?)");

    $stmt->execute([$order_id, $service_id, $partial_amount, $percent]);

    // Set the flag to 1
    $pdo->prepare("UPDATE orders SET allow_partial_payment = 1 WHERE order_id = ?")->execute([$order_id]);
} else {
    // Set the flag to 0 if NA or 0 is selected
    $pdo->prepare("UPDATE orders SET allow_partial_payment = 0 WHERE order_id = ?")->execute([$order_id]);
}

header("Location: order.php?success=1");
exit();

