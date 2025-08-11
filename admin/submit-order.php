<?php
require_once '../includes/db.php';
require_once '../vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $consumer_id = $_POST['consumer_id'];
    $service_id = $_POST['service_id'];
    $amount = $_POST['service_price'];

    $serviceStmt = $pdo->prepare("SELECT billing_period, billing_unit FROM services WHERE service_id = ?");
    $serviceStmt->execute([$service_id]);
    $service = $serviceStmt->fetch(PDO::FETCH_ASSOC);

    
    if ($service){
        $billingPeriod = (int)$service['billing_period'];
        $billingUnit = $service['billing_unit']; 


        $stmt = $pdo->prepare(
            "SELECT * FROM orders 
            WHERE consumer_id = ? AND service_id = ?
            ORDER BY order_date DESC
            LIMIT 1"
        );


        $stmt->execute([$consumer_id, $service_id]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        if($existingOrder) {
            $orderDate = new DateTime($existingOrder['order_date']);
            $currentDate = new DateTime(); 
            $endDate = (clone $orderDate)->modify("+$billingPeriod $billingUnit");  //if unit is year then php' date:: modify function adds one calender year to existing date


            if ($currentDate < $endDate) { // Service still active
            
                echo "<script>
                    alert('Service still active until: " . $endDate->format('d M Y') . ". Proceed to renewal.');
                    window.location.href = 'order.php';
                </script>";
                exit();
            }
  
        }

     }

    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(gid, 3) AS UNSIGNED)) AS max_gid FROM orders");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextNumber = ($row && $row['max_gid'] !== null) ? $row['max_gid'] + 1 : 10000;

    $gid = 'gc' . $nextNumber;
    $stmt = $pdo->prepare("INSERT INTO orders (consumer_id, service_id, total_amount, gid) VALUES (?, ?, ?, ?)");
    $stmt->execute([$consumer_id, $service_id, $amount, $gid]);

    header("Location: order.php?success=1");
    exit();
}

?>
