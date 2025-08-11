<?php
require_once '../includes/auth.php';     
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: automate-billing/admin/admin-login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $query = "
        SELECT o.*,
               c.name AS consumer_name, 
               s.service_name AS service_name,
            
               p.percentage AS percent
               
              FROM orders o
              JOIN consumers c ON o.consumer_id = c.consumer_id
              JOIN services s ON o.service_id = s.service_id
              LEFT JOIN partial_payment p ON o.order_id = p.order_id
              ORDER BY o.order_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => "%$search%"]);

} else {
    $query = "
        SELECT o.*, 
               c.name AS consumer_name, 
               s.service_name AS service_name,
               
               p.percentage AS partial_percent

              FROM orders o
              JOIN consumers c ON o.consumer_id = c.consumer_id
              JOIN services s ON o.service_id = s.service_id
              LEFT JOIN partial_payment p ON o.order_id = p.order_id
              ORDER BY o.order_date DESC
    ";

    $stmt = $pdo->query($query);
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body{
            margin: 0;
            padding: 0px 10px; 
        }
        
        .main-content {
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s ease;
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

        .add-order-btn {
            padding: 10px 18px;
            background-color:rgb(55, 162, 98);
            color: white;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
        }

        .add-order-btn:hover {
            background-color:rgb(52, 118, 88);
        }

        body.sidebar-collapsed .add-order-btn {
            margin-right: 0px;
        }
       
        /* when sidebar is collapsed */
        body.sidebar-collapsed .main-content{
            margin-left: 65px;
            /* margin-right: 0px; */
        }
        
        .search-bar {
            padding: 8px 15px;
            /* margin-left: 900px; */
            margin-bottom: 15px;
            float: right;
        }

        body.sidebar-collapsed .search-bar{
            margin-right: 0px;
        }
        
        
    </style>
</head>

<body>

<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="main-content">
    <h2>All Orders</h2>
   
    <a href="create-order.php?select_order=true" class="add-order-btn">+ Add New Order</a>

    <table>
        <thead>
            <tr>
                <th>Consumer</th>
                <th>Service</th>
                <th>Total Amount</th>
                <th>Status</th>
                <!-- <th>Transaction ID</th> -->
                <th>Order Date</th>
                <th>partial payment</th>
                <th>Delete</th>


            </tr>
        </thead>
       
        <input type="text" id="orderSearch" class="search-bar" placeholder="search service or consumer" onkeyup="filterOrders()">

        <tbody>
            <?php if(count($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['consumer_name']) ?></td>
                        <td><?= htmlspecialchars($order['service_name']) ?></td>
                        <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>

                        <td>
                            <form method="POST" action="store-partial-percentage.php">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <input type="hidden" name="service_id" value="<?= $order['service_id'] ?>">
                                <input type="hidden" name="total_amount" value="<?= $order['total_amount'] ?>">    
                                <?php $selectedPercent = $order['partial_percent'] ?? ''; ?>
                                <input type="number" name="percent" min="0" max="100" placeholder="%"
                                    value="<?= htmlspecialchars($selectedPercent) ?>" style="width:60px;" required>

                                <button type="submit">Apply</button>
                            </form>
                        </td>

                        <td>
                            <!-- <form method="POST" action="delete-order.php" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" style="
                                    background: none;
                                    border: none;
                                    font-size: 18px;
                                    color: red;
                                    cursor: pointer;
                                ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form> -->
                            <button class="delete-btn" data-order-id="<?= $order['order_id'] ?>">❌</button>

                        </td>


                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;"><b>No orders found</b></td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    
</div>


<!-- ajax call -->
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        if (confirm('Are you sure you want to delete this order?')) {
            fetch('delete-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `order_id=${orderId}`
            })
            .then(res => res.text())
            .then(response => {
                this.closest('tr').remove();  // Remove row from table immediately
            })
            .catch(err => alert('Failed to delete order.'));
        }
    });
});
</script>


</body>
</html>
