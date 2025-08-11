<?php
require_once 'includes/db.php';
require_once 'includes/auth-user.php';
require_once 'vendor/autoload.php';


// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

use Ramsey\Uuid\Uuid;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $consumer_id = $_SESSION['consumer_id']; //already in session
    $service_id = $_POST['service_id'] ;
    $original_order_id = $_POST['parent_order_id'] ;

    // if (!$consumer_id || !$service_id || !$original_order_id) {
    //     echo "Missing required fields.";
    //     exit;
    // }

    // original order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$original_order_id]);
    $originalOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$originalOrder) {
        echo "Original order not found.";
        exit;
    }

    
    //service details
    $serviceStmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
    $serviceStmt->execute([$service_id]);
    $service = $serviceStmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        echo "Service not found.";
        exit;
    }
    $billingPeriod = (int)$service['billing_period'];
    $billingUnit = $service['billing_unit'];
    $billingUnit = rtrim(strtolower($billingUnit), 's');

    $orderDate = new DateTime($originalOrder['order_date']);
    $currentDate = new DateTime();
    $endDate = (clone $orderDate)->modify("+$billingPeriod $billingUnit")->modify('-1 day')->setTime(23, 59, 59);
    
    $renewalWindowStart = (clone $endDate)->modify('-30 days');

    if (strtolower($originalOrder['status']) !== 'expired' &&
        $currentDate < $renewalWindowStart
    ) {
        echo "<script>
            alert('Renewal not allowed before: " . $renewalWindowStart->format('d M Y') . "');
            window.location.href = 'renewal.php';
        </script>";
        exit;
    }


    // Insert new order
    $new_gid = Uuid::uuid4()->toString();
    $stmt = $pdo->prepare("INSERT INTO orders 
        (consumer_id, service_id, total_amount, gid, parent_order_id)
        VALUES (?, ?, ?, ?, ?)");

    $stmt->execute([
        $consumer_id,
        $service_id,
        $originalOrder['total_amount'],
        $new_gid,
        $original_order_id
    ]);

    echo "<script>
        alert('Renewal successful!');
        window.location.href = 'renewal.php';
    </script>";
    exit;
}
?>
