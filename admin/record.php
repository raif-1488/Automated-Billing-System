<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$currentPage = basename($_SERVER['PHP_SELF']);

$search = $_GET['search'] ?? '';

// Query
$query = "
SELECT t.payment_id, 
       t.amount_paid, 
       t.datetime,
       c.name AS consumer_name,
       o.gid,
       o.order_id,
       s.service_name
FROM transactions t
JOIN orders o ON t.gid = o.gid 
JOIN consumers c ON o.consumer_id = c.consumer_id
JOIN services s ON o.service_id = s.service_id
";

if ($search !== '') {
    $query .= " WHERE c.name LIKE :search OR c.gid LIKE :search";
}

$query .= " ORDER BY t.datetime DESC";

$stmt = $pdo->prepare($query);

if ($search !== '') {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Records</title>
    <style>
        .search-box {
            float: right;
            margin: 20px;
            /* padding: 10px 15px; */
            margin-top: 30px;
            margin-bottom:10px;

        }

        .main-content {
            margin-left: 160px;
            margin-right: 0px;
            padding: 40px;
            transition: margin-left 0.3s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 10px  10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        body.sidebar-collapsed .main-content{
            margin-left: 65px;
            
        }
    </style>
</head>


<body>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/navbar.php'; ?>


<div class="search-box">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by name or GID" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
</div>

<div class="main-content">
    <h2 style="text-align:center;">Transaction Records</h2>

    <table>
        <thead>
            <tr>
                <th>GID</th> 
                <!-- it is actually gid but to confuse i stored it in the name of order id -->
                <th>Consumer Name</th>
                <th>Transaction ID</th>
                <th>Amount Paid</th>
                <th>Service</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($transactions) > 0): ?>
                <?php foreach ($transactions as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['gid']) ?></td>
                        <td><?= htmlspecialchars($row['consumer_name']) ?></td>
                        <td><?= htmlspecialchars($row['payment_id']) ?></td>
                        <td>₹<?= number_format($row['amount_paid'], 2) ?></td>
                        <td><?= htmlspecialchars($row['service_name']) ?></td>
                        <td><span class="utc-time" data-utc="<?= htmlspecialchars($row['datetime']) ?>"></span></td>
               
                <?php endforeach; ?>
                
            <?php else: ?>
                <tr><td colspan="6">No records found.</td></tr>
            <?php endif; ?>
            
        </tbody>
    </table>

</div>
<script>
  function convertUTCDateToLocalDate(date) {
    var newDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
    var offset = date.getTimezoneOffset() / 60;
    var hours = date.getHours();
    newDate.setHours(hours - offset);
    return newDate;
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".utc-time").forEach(function (el) {
      const utcStr = el.dataset.utc;
      if (utcStr) {
        const utcDate = new Date(utcStr);
        const localDate = convertUTCDateToLocalDate(utcDate);
        el.textContent = localDate.toLocaleString(); // show in browser's local format
      }
    });
  });
</script>


</body>
</html>

