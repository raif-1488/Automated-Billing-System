<?php
   require_once 'includes/auth-user.php';
   require_once 'includes/db.php';

   if(!isset($_SESSION['cons_logged_in'])){
     header("Location: cons-login.php");
     exit;
   }

   $consumer_id = $_SESSION['consumer_id'] ;
   
   $currentPage = basename($_SERVER['PHP_SELF']);

    $stmt = $pdo->prepare("
        SELECT t.payment_id , 
            t.gid AS orderID,
            t.service_id, 
            t.amount_paid AS Amount_Paid, 
            t.datetime AS Date,
            t.print_token 
        FROM transactions t
        JOIN orders o ON t.gid = o.gid
        WHERE o.consumer_id = ?
        ORDER BY t.datetime DESC
    ");

    $stmt->execute([$consumer_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>
<head>
    <title>My Transactions</title>
    <style>
         body {
            font-family: Arial, 
            sans-serif; 
            margin: 0; 
            /* padding: 10px 10px; */
            
        }
        .main-content {
            margin-left: 220px; 
            padding: 25px;
            transition: margin-left 0.3s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px ;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        h2 {
            text-align: center;
        }

        .print-link {
            color: #4CAF50;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s ease;
        }

        .print-link:hover {
            color: #388e3c;
            text-decoration: none;
        }

        
    </style>
</head>
<body>

<?php include 'cons-sidebar.php'; ?>
<?php include 'cons-navbar.php'; ?>


<div class = "main-content">
    <h2 style = "text-align: center;">My Transaction History</h2>

    <?php if (count($transactions) === 0): ?>
        <p style="text-align: center; color: grey;">You have no transaction records yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Transaction ID</th>
                        <th>Service ID</th>
                        <th>Amount Paid</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td><?= htmlspecialchars($txn['orderID']) ?></td>
                            <td><?= htmlspecialchars($txn['payment_id']) ?></td>
                            <td><?= htmlspecialchars($txn['service_id']) ?></td>
                            <td><?= number_format($txn['Amount_Paid'], 2) ?></td>
                            <td><?= htmlspecialchars($txn['Date']) ?></td>
                            <td>
                                <a href="print-receipt.php?token=<?= urlencode($txn['print_token']) ?>" 
                                    class="print-link"
                                    target="_blank">
                                    <i class="fa fa-print"></i> Print Receipt
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    <?php endif; ?>

</div>
</body>
</html>
