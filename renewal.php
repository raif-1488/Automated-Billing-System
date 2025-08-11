<?php
require 'includes/auth-user.php';
require 'includes/db.php';

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

$currentPage = basename($_SERVER['PHP_SELF']);

$consumer_id = $_SESSION['consumer_id'];


$query = "
        SELECT o.*, s.service_name, s.billing_period, s.billing_unit
        FROM orders o
        JOIN services s ON o.service_id = s.service_id
        WHERE o.consumer_id = :consumer_id
        AND o.parent_order_id IS NULL
        ORDER BY order_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['consumer_id' => $consumer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$eligibleOrders = [];

foreach ($orders as $order) {
    $orderDate = new DateTime($order['order_date']);
    $billingPeriod = (int)$order['billing_period'];
    $billingUnit = strtolower(rtrim($order['billing_unit'], 's'));
    // $billingUnit = rtrim($billingUnit, 's');  

    $endDate = (clone $orderDate)->modify("+$billingPeriod $billingUnit")->modify('-1 day')->setTime(23, 59, 59);
    $now = new DateTime();

    //check once if already renewed
    $checkRenew = $pdo->prepare("SELECT 1 FROM orders WHERE parent_order_id = ?");
    $checkRenew->execute([$order['order_id']]);
    $alreadyRenewed = $checkRenew->fetchColumn();

    if ($now > $endDate && !$alreadyRenewed) {
        $order['end_date'] = $endDate->format('d M Y h:i A');
        $eligibleOrders[] = $order;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Renew Services</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .renew-btn {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .renew-btn:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>

<h2>Renew Services</h2>

<?php if (empty($eligibleOrders)): ?>
    <p>No services are currently eligible for renewal.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Total Amount</th>
                <th>Order Date</th>
                <th>Ended On</th>
                <th>Status</th>
                <th>Renew</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eligibleOrders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['service_name']) ?></td>
                    <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    <td><?= $order['end_date'] ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td>
                        <form method="POST" action="renew-submit.php">
                            <input type="hidden" name="parent_order_id" value="<?= $order['order_id'] ?>">
                            <input type="hidden" name="service_id" value="<?= $order['service_id'] ?>">
                            <input type="hidden" name="consumer_id" value="<?= htmlspecialchars($_SESSION['consumer_id']) ?>">
                            <input type="hidden" name="total_amount" value="<?= $order['total_amount'] ?>">
                            <button type="submit" class="renew-btn">Renew Now</button>
                        </form>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>