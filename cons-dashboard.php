<?php

   require_once 'includes/auth-user.php';
   require_once 'includes/db.php';
   
   $notifications = [];

   if(!isset($_SESSION['cons_logged_in'])){
     header("Location: cons-login.php");
     exit;
   }
   
   $currentPage = basename($_SERVER['PHP_SELF']);

   $user_name = "User";
      
   //var_dump($_SESSION['email']);
   if(isset($_SESSION['email'])) {
        $stmt = $pdo->prepare("SELECT name FROM consumers WHERE email = ?");
        $stmt->execute([$_SESSION['email']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);  //Fetches the result as an associative array
        if ($row) {
            $user_name = $row['name'];
        }
   }
   
    
    $consumer_id = $_SESSION['consumer_id'];
    $stmt1 = $pdo->prepare("SELECT COUNT(order_id) AS total_opted FROM orders WHERE consumer_id = :consumer_id");
    $stmt1->execute(['consumer_id' => $consumer_id]);
    $res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $totalOpted = $res1['total_opted'] ?? 0;

   
    // 2. Total upcoming renewals (card and notification thing)
        $today = new DateTime();
        $upcomingRenewals = 0;

        $stmt2 = $pdo->prepare("SELECT o.order_date, o.status, s.billing_period, s.billing_unit, s.service_name
                                FROM orders o
                                JOIN services s ON o.service_id = s.service_id
                                WHERE o.consumer_id = :consumer_id AND o.status = 'paid'");
        
        $stmt2->execute(['consumer_id' => $consumer_id]);
        $orders = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $order) {
            $orderDate = new DateTime($order['order_date']);
            $billingPeriod = (int) $order['billing_period'];
            $billingUnit = rtrim(strtolower($order['billing_unit']), 's');

            $expiryDate = (clone $orderDate)
                        ->modify("+$billingPeriod $billingUnit")
                        ->modify('-1 day')
                        ->setTime(23, 59, 59);

            $daysLeft = (int) $today->diff($expiryDate)->format('%r%a');

            if ($daysLeft >= 0 && $daysLeft <= 30) {
                $upcomingRenewals++;
            }
            if ($upcomingRenewals > 0) {
               $notifications[] = "Renewal due for service: " . htmlspecialchars($order['service_name']) . " in $daysLeft day(s).";
            }
        }


        // 3. Total unpaid (pending) services 
        // Fetch unpaid services with their service names
        $stmt2 = $pdo->prepare("SELECT s.service_name
                                FROM orders o
                                JOIN services s ON o.service_id = s.service_id
                                WHERE o.consumer_id = :consumer_id AND o.status = 'pending'");
        $stmt2->execute(['consumer_id' => $consumer_id]);
        $pendingOrders = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Total unpaid services (for dashboard card)
        $unpaidServices = count($pendingOrders);

        // Notifications (for ticker)
        foreach ($pendingOrders as $pending) {
            $serviceName = htmlspecialchars($pending['service_name']);
            $notifications[] = "Payment pending for $serviceName";
        }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    
    <style>
        body {
            font-family: Arial, 
            sans-serif; 
            margin: 0; 
            /* padding: 10px 10px; */
        }

        .main-content {
            margin-left: 220px; 
            /* padding: 20px; */
            /* transition: margin-left 0.3s ease; */
        }

        .greeting-box {
            margin-top: 30px;
            margin-right: 520px;
            background: #f8f9faf2;
            padding: 40px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

            /* to overflow image outside the greeting box */
            position: relative;
            overflow: visible;
            padding-right: 150px;
        }

        .floating-img {
            position: absolute;
            right: 10px;
            top: 30;
            height: 140px;
        }

        .greeting-box h2 {
            margin-top: 20px;
            color: #444;
        }

        .greeting-box img {
            width: 200px;
            height: 170px;
            /* border-radius: 50%; */
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            height: 5px;
            margin-top: 10px;
        }

        .card {
            flex: 1 1 280px;
            background: #f8f9faf2;
            padding: 10px;
            border-left: 5px solid #F4C430;
            /* box-shadow: 0 2px 5px rgba(0,0,0,0.05); */
            min-width: 185px;
            max-width: 200px;
            border-radius: 10px;
            /* margin-top: 1px; */
            transition: transform 0.1s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 15px;
            color: #333;
        }

        .card .value {
            font-size: 24px;
            font-weight: bold;
            color:rgb(214, 147, 52);
        }
        
        
        body.sidebar-collapsed .main-content{
            margin-left: 90px;
        }


        /* Ticker notifications */
       
        .notification-ticker-wrapper {
            /* margin-left: 0px;         Sidebar width */
            background: white;
            overflow: hidden;
            white-space: nowrap;
            padding: 5px 0;
        }
        .notification-ticker {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            background: white;
            color: red;
            font-weight: bold;
            font-size: 18px;
            padding: 5px 0;
            margin: 15px 0; /* Adds spacing between greeting box and cards */
        }

        .ticker-content {
            display: inline-block;
            padding-left: 8%;  
            animation: scroll-left 15s linear infinite;
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

    </style>
</head>

<body>
<?php require 'cons-sidebar.php'; ?>
<?php require 'cons-navbar.php'?>

<div class="main-content">

    <div class="greeting-box">
        <h2>Hello, <?= htmlspecialchars($user_name) ?></h2>
        <img src="images/icon3.jpg" alt="User Icon" class="floating-img">
    </div>



    <?php if (!empty($notifications)): ?>
        <div class="notification-ticker-wrapper">
            <div class="notification-ticker">
                <div class="ticker-content">
                    <?= htmlspecialchars(implode(' | ', $notifications)) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>




    <div class="card-container">
        <div class="card">
            <i class="fas fa-briefcase fa-2x"></i>
            <br></br>
            <h3>Opted Services</h3>
            <p class="value"><?= $totalOpted?> </p>
            
        </div>

        <div class="card">
            <i class="fas fa-calendar-check fa-2x"></i>
            <br></br>
            <h3>Upcoming Renewals</h3>
            <p class="value"><?= $upcomingRenewals?> </p>
            
        </div>

        <div class="card">
            <i class="fas fa-wallet fa-2x"></i>
            <br></br>
            <h3>Unpaid Services</h3>
            <p class="value"><?= $unpaidServices?> </p>
        </div>
    </div>



</div>



</body>
</html>
