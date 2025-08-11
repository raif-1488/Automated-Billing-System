<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;

    if ($service_id) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
    }

    echo json_encode(['status' => 'success']);
    exit;
}
?>
