<?php
    require_once '../includes/auth.php';
    require_once '../includes/db.php';
    $consumer_id = $_GET['consumer_id'] ?? '';
    $services = $pdo->query("SELECT service_id, service_name, gst_price FROM services")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Select Service</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; cursor: pointer; }
        tr:hover { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h3>Choose a Service</h3>
<table>
    <tr><th>Service</th><th>Price</th></tr>
    <?php foreach ($services as $service): ?>
        <tr onclick="selectService('<?= $service['service_id'] ?>', '<?= htmlspecialchars($service['service_name']) ?>', '<?= htmlspecialchars($service['gst_price']) ?>')">
            <td><?= htmlspecialchars($service['service_name']) ?></td>
            <td><?= $service['gst_price'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
function selectService(id, name, price) {
    window.opener.setService(id, name, price);
    window.close();
}
</script>
</body>
</html>
