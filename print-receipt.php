<?php
require_once 'includes/auth-user.php';
require_once 'includes/db.php';

if (!isset($_SESSION['consumer_id'])) {
    header("Location: cons-login.php");
    exit;
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Invalid request.');
}

$stmt = $pdo->prepare("
    SELECT t.*, o.*, c.name AS customer_name, c.address AS customer_address, s.service_name, s.price AS service_price, s.gst_price
    FROM transactions t
    JOIN orders o ON t.gid = o.gid
    JOIN consumers c ON o.consumer_id = c.consumer_id
    JOIN services s ON t.service_id = s.service_id
    WHERE t.print_token = ? AND o.consumer_id = ?
");
$stmt->execute([$token, $_SESSION['consumer_id']]);
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receipt) {
    die('Unauthorized or invalid receipt.');
}

$order_id = $receipt['gid'];
$order_date = date('d-m-Y', strtotime($receipt['order_date']));
$transaction_id = $receipt['payment_id'];
$customer_name = htmlspecialchars($receipt['customer_name']);
$customer_address = !empty($receipt['customer_address']) ? nl2br(htmlspecialchars($receipt['customer_address'])) : 'N/A';
$service_name = htmlspecialchars($receipt['service_name']);
$amount_paid = number_format($receipt['amount_paid'], 2);
$service_price = number_format($receipt['service_price'], 2);
$gst_price = number_format($receipt['gst_price'], 2);
$price_without_gst = number_format($receipt['service_price'] - $receipt['gst_price'], 2);
$grand_total = $service_price; // assuming GST included in price


$service_price = (float)$receipt['service_price'];  // Base price (without GST)
$gst_price     = (float)$receipt['gst_price'];      // Price including GST

$gst_amount = $gst_price - $service_price;
$gst_percent = ($service_price > 0) 
    ? round(($gst_amount / $service_price) * 100, 2)
    : 0;


?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: Arial; margin: 30px; font-size: 14px; color: #333; }
        .logo { display: block; margin: 0 auto 20px auto; width: 180px; } /* Logo slightly bigger */
        .header { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .header div { width: 48%; line-height: 1.6; }
        h2 { text-align: center; margin-bottom: 100px; }
        hr { border: 1px solid #ccc; margin: 20px 0; }
        .section { margin-bottom: 25px; line-height: 1.7; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        .totals { width: 40%; float: right; margin-top: 20px; }
        .totals table { width: 100%; }
        .totals td { text-align: right; }
        .note { font-size: 12px; text-align: center; margin-top: 150px; }
    </style>
</head>
<body onload="window.print()">

<img src="images/logo.jpg" alt="Get Catalyzed Logo" class="logo">

<h2>Receipt</h2>

<div class="header">
    <div>
        <strong>Order ID:</strong> <?= htmlspecialchars($receipt['gid']) ?><br>
        <strong>Transaction ID:</strong> <?= htmlspecialchars($receipt['payment_id']) ?>
    </div>
    <div style="text-align: right;">
        <strong>Order Date:</strong> <?= date('d-m-Y', strtotime($receipt['order_date'])) ?>
    </div>
</div>

<hr>

<div class="header">
    <div>
        <strong>Bill To:</strong><br>
        <?= $customer_name ?><br>
        <?= $customer_address ?>
    </div>
    <div style="text-align: right;">
        <strong>Pay To:</strong><br>
        Get Catalyzed<br>
        29, Shiv Puri Colony,<br>
        Shyam Nagar, Jaipur,<br>
        Rajasthan 302019
    </div>
</div>



<table>
    <thead>
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Price (incl. GST)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $service_name ?></td>
            <td>Service as per order</td>
            <td>1</td>
            <td>₹<?= $service_price ?></td>
        </tr>
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td>Price (without GST):</td>
            <td>₹<?= $service_price ?></td>
        </tr>
        <tr>
            <td>GST Tax (<?= $gst_percent ?>%):</td>
            <td>₹<?= number_format($gst_amount, 2) ?></td>
        </tr>
        <tr>
            <th>Grand Total (incl. GST):</th>
            <th>₹<?= $gst_price ?></th>
        </tr>
    </table>
</div>

<div style="clear: both;"></div>


<div class="note">
    For queries, contact Get Catalyzed at 
    <span style="color: blue;">support@getcatalyzed.in</span>
</div>

</body>
</html>
