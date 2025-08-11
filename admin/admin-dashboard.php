<?php
    require_once '../includes/auth.php';     
    require_once '../includes/db.php'; 

    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: admin-login.php");
        exit;
    }
    

    $currentPage = basename($_SERVER['PHP_SELF']);
    //echo "Current page: $currentPage";  // Debug line

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM consumers");
    $totalConsumers = $totalStmt->fetchColumn();

    $unpaidStmt = $pdo->query("
        SELECT COUNT(DISTINCT consumer_id)
        FROM orders
        WHERE status = 'Pending'
    ");
    $unpaidConsumers = $unpaidStmt->fetchColumn();


?>

<!DOCTYPE html>
<html lang="en">
<head >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <style>
        body { 
            font-family: Arial, 
            sans-serif; 
            margin: 0; 
            padding: 0px 10px; 
         }
        .main-content { margin-left: 220px; padding: 20px; transition: margin-left 0.3s ease; }
        
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            flex: 1 1 250px;
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #F4C430;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            min-width: 100px;
            max-width: 200px;
        }
    </style>
</head>
<body>

<?php require 'includes/sidebar.php'; ?>
<?php require 'includes/navbar.php'; ?>

<div class="main-content">
    <h1>Hello!</h1>
    
    <div class="card-container">
        <div class="card">
            <h3>Total Consumers</h3>
            <p><?= $totalConsumers ?></p>
        </div>

        <div class="card">
            <h3>Unpaid Consumers</h3>
            <p><?= $unpaidConsumers ?></p>
        </div>

         <!-- Add more cards like this in future -->
        <!--
        <div class="card">
            <h3>Other Metric</h3>
            <p>123</p>
        </div>
        -->
    </div> 
</div>

</body>
</html>