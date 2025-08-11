<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;

    if ($order_id) {

        $stmt = $pdo->prepare("SELECT gid FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $gid = $order['gid'];

            // Delete transactions associated with this gid
            $stmt = $pdo->prepare("DELETE FROM transactions WHERE gid = ?");
            $stmt->execute([$gid]);

            // Delete the order itself
            $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->execute([$order_id]);
        }
    }
}

echo 'error';
exit;
?>
