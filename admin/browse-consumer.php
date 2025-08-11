<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$consumers = $pdo->query("SELECT consumer_id, name, email FROM consumers")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Consumer</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; cursor: pointer; }
        tr:hover { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h3>Choose a Consumer</h3>
<table>
    <tr><th>Name</th><th>Email</th></tr>
    <?php foreach ($consumers as $row): ?>
        <tr onclick="selectConsumer('<?= $row['consumer_id'] ?>', '<?= htmlspecialchars($row['name']) ?>')">
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
function selectConsumer(id, name) {
    window.opener.setConsumer(id, name);
    window.close();
}
</script>
</body>
</html>
