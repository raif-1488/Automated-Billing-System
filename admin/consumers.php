<?php 
require_once "../includes/auth.php";
require_once "../includes/db.php";

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin-login.php");
    exit;
}


$currentPage = basename($_SERVER['PHP_SELF']);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

     if($search !== '') {
       $query = "SELECT * FROM consumers WHERE consumer_id LIKE :search OR name LIKE :search";
       $stmt = $pdo->prepare($query);
       $stmt->execute(['search' => "%$search%"]);
       $consumers = $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     else {
    $stmt = $pdo->query("SELECT * FROM consumers ORDER BY created_at");
    $consumers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// $stmt = $pdo->query("SELECT * FROM consumers ORDER BY created_at ");
// $consumers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Consumers</title>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');

            document.body.classList.toggle('sidebar-collapsed');
        }
    </script>


    <style>
         body {
            font-family: Arial, 
            sans-serif;
            padding: 0px;
         } 

        /* when sidebar expanded */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            /* transition: margin-left 0.3s ease; */
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

        th, td {
            padding: 10px 5px;
            border: 1px solid #dee2e6; 
            text-align: center;
            /* font-size: 15px; */
        }
        
        th {
            background-color: #343a40;
            color: white;
            /* background-color:rgb(242, 242, 242); */
        }


        .add-consumer-btn {
            padding: 10px 18px;
            background-color:rgb(55, 162, 98);
            color: white;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
        }
        .add-consumer-btn:hover {
            background-color:rgb(52, 118, 88);
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
    <h2>All Consumers</h2>
    <a href="add-consumer.php" class="add-consumer-btn">+ Add New </a>

    <!-- <form method="POST" action="" >
        <button type="submit" name="add_consumer" class="add-consumer-btn">
            Add New 
        </button>
    </form> -->


    <table>
        <thead>
            <tr>
                <th>Consumer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Created At</th>
                <!-- <th>Delete</th> -->
            </tr>
        </thead>

        <input type="text" id="orderSearch" class="search-bar" placeholder="search consumer" onkeyup="filterOrders()">


        <tbody>
            <?php foreach ($consumers as $consumer): ?>
                <tr>
                    <td><?= htmlspecialchars($consumer['consumer_id']) ?></td>
                    <td><?= htmlspecialchars($consumer['name']) ?></td>
                    <td><?= htmlspecialchars($consumer['email']) ?></td>
                    <td><?= htmlspecialchars($consumer['contact']) ?></td>
                    <td><?= htmlspecialchars($consumer['address']) ?></td>
                    <td><?= htmlspecialchars($consumer['created_at']) ?></td>
                    <!-- <td>
                        <button onclick="deleteConsumer(<?= $consumer['consumer_id'] ?>)" style="color:red; font-weight:bold;">✖️</button>
                    </td> -->

                </tr>
            <?php endforeach; ?>

            <?php if (count($consumers) === 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No consumers found.</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>


<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function deleteConsumer(consumerId) {
        if (confirm('Are you sure you want to delete this consumer?')) {
            $.ajax({
                url: 'delete-consumer.php',
                type: 'POST',
                data: { consumer_id: consumerId },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#consumer-row-' + consumerId).remove();
                    } else {
                        alert('Failed to delete consumer.');
                    }
                }
            });
        }
}

</script> -->

</body>
</html>