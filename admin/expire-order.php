<?php
require_once '../includes/db.php';

try {
    $threeDaysAgo = (new DateTime())->modify('-3 days')->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'expired'
        WHERE status = 'pending' AND order_date < ?
    ");

    $stmt->execute([$threeDaysAgo]);

    // Optional: log how many were updated
    $updated = $stmt->rowCount();
    echo "Cron ran successfully. $updated order(s) expired.";

} 
catch (Exception $e) {
    echo "Cron error: " . $e->getMessage();
}
