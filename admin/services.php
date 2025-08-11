<?php
     require_once '../includes/auth.php';
     require_once '../includes/db.php';

     $gst_rate = 0.18;
     
     $currentPage = basename($_SERVER['PHP_SELF']);
     
     $search = isset($_GET['search']) ? trim($_GET['search']) : '';

     if($search !== '') {
       $query = "SELECT * FROM services WHERE service_name LIKE :search";
       $stmt = $pdo->prepare($query);
       $stmt->execute(['search' => "%$search%"]);
     }

     else{
        $stmt = $pdo->query("SELECT * FROM services ORDER BY created_at");
        $services = $stmt->fetchALL();
     }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Services </title>

    <style>

        .main-content {
            margin-left: 200px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        /* when sidebar is collapsed */
        body.sidebar-collapsed .main-content{
            margin-left: 65px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
/*  
        thead {
            /* background-color: #343a40; */
            /* background-color: #f2f2f2;
            color: black;
        } */ 

        th, td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
            /* font-size: 15px; */
        }

         th {
            background-color: #f2f2f2;
        }


        .add-service-btn {
            padding: 10px 18px;
            background-color:rgb(55, 162, 98);
            color: white;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
        }

        .add-service-btn:hover {
            background-color:rgb(52, 118, 88);
        }

        body.sidebar-collapsed .add-service-btn {
            margin-right: 0px;
        }

        .search-bar {
            padding: 8px 15px;
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
    <h2>All Services</h2>
    
    <a href="add-service.php" class="add-service-btn">+ Add New </a>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service Name</th>
                <th>Description</th>
                <th>Billing Cycle</th>
                <th>Base Price (₹)</th>
                <th>Price w/ GST (₹)</th>
                <th>Created At</th>
                <th>Delete</th>
            </tr>
        </thead>

        <input type="text" id="orderSearch" class="search-bar" placeholder="search service" onkeyup="filterOrders()">

        <tbody>
            
            <?php foreach ($services as $index => $service): ?>
                <tr id="service-row-<?= $service['service_id'] ?>">

                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($service['service_name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($service['description'])) ?></td>
                    <!-- <td><?= ucfirst($service['billing_cycle']) ?></td> -->
                    <td><?= $service['billing_period'] . ' ' . ucfirst($service['billing_unit']) ?></td>
                    <td><?= number_format($service['price'], 2) ?></td>
                    <td><?= number_format($service['gst_price'], 2) ?></td>
                    <td><?= $service['created_at'] ?></td>

                    <td>
                        <button onclick="deleteService(<?= $service['service_id'] ?>)" style="color:red; font-weight:bold;">✖️</button>
                    </td>

                </tr>
            <?php endforeach; ?>
          

            <?php if (count($services) === 0): ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No Services found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function deleteService(serviceId) {
        if (confirm('Are you sure you want to delete this service?')) {
            $.ajax({
                url: 'delete-service.php',
                type: 'POST',
                data: { service_id: serviceId },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#service-row-' + serviceId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Failed to delete service.');
                    }
                }
            });
        }
}



</script>
</body>

</html>
