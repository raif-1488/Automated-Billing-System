<?php
require_once 'includes/auth-user.php';
require_once 'includes/db.php';

$consumer_id = $_SESSION['consumer_id'] ?? null;

if (!$consumer_id) {
    header("Location: cons-login.php");
    exit();
}


$currentPage = basename($_SERVER['PHP_SELF']);

$query = "
    SELECT o.*, c.name AS consumer_name, s.service_name AS service_name
    FROM orders o
    JOIN consumers c ON o.consumer_id = c.consumer_id
    JOIN services s ON o.service_id = s.service_id
    WHERE o.consumer_id = :consumer_id
    ORDER BY o.order_date DESC
";


$stmt = $pdo->prepare($query);
$stmt->execute(['consumer_id' => $consumer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

//echo "Session Consumer ID: " . $_SESSION['consumer_id'];
// SELECT * FROM orders WHERE consumer_id = <printed id>;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your Orders</title>
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
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }
        
        td .pay-btn{
            display: inline-block;
        }

        td {
            text-align: center;
        }

        .add-order-box {
            position: absolute;
            width: 100px;
            height: 20px;
            margin-bottom:25px;
            background-color: #F4C430;
            border-radius: 7px;
            text-align: center;
            padding: 10px 14px; /*second one is for width*/
            cursor: pointer;
            
        }

        .add-order-link {
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .plus-icon {
            background-color: white;
            color: #F4C430;
            font-weight: bold;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 14px;
        }

        .add-order-box:hover {
            background-color: #e6b800;
        }

        .action-btn {
            cursor: pointer;
            color: black;
            position: center;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
            border-bottom: 1px solid #eee;
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

      
        .search-bar {
            float: right;
            padding: 8px;
            margin-bottom: 10px;
            width: 250px;
        }
        body.sidebar-collapsed .search-bar{
            margin-right: 0px;
        }

        body.sidebar-collapsed .main-content{
            margin-left: 90px;
        }

        .pay-btn {
            /* background-color: transparent;
            color: #F4C430; *//* sir bolenge to hata denge*/
            background-color: #F4C430;
            color: white;
            font-weight: bold;
            border: 2px solid #F4C430;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: center;
        }
        .pay-btn:hover {
            background-color: #e0b000;
            color: white;
        }


        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
            }
        .modal-content {
            background-color: white;
            padding: 20px;
            margin: 15% auto;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px gray;
        }
        .modal-content button {
            margin: 10px;
            padding: 8px 15px;
            cursor: pointer;
        }

    </style>
</head>

<body>
<?php require 'cons-sidebar.php'; ?>
<?php require 'cons-navbar.php'; ?>

<div class="main-content">
    <h2>Your Service Orders</h2>
    
    <div class="add-order-box">
            <a href="create-cons-order.php" class="add-order-link">
                <span class="plus-icon">+</span> Add Order
            </a>
    </div>
    <br></br>


    <table>
        <thead>
            <tr>
                <th></th>
                <th>Order_ID</th>
                <th>Service</th>
                <th>Total Amount</th>
                <th>Status</th>
                <!-- <th>Transaction ID</th> -->
                <th>Order Date</th>
            </tr>
        </thead>

        <tbody id="ordersTable">
            <?php if(count($orders)): ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td> 
                        <button class="pay-btn"
                            data-order-id="<?= $order['order_id'] ?>"
                            data-allow-partial="<?= $order['allow_partial_payment'] ?>">
                            PAY
                        </button>

                    </td>
                    <td><?= htmlspecialchars($order['gid']) ?></td>
                    <td><?= htmlspecialchars($order['service_name']) ?></td>
                    <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= date('d-m-Y', strtotime($order['order_date'])) ?></td>
                </tr>
                <?php endforeach; ?>

             <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;"><b>No orders found</b></td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

<!-- Payment Options Modal -->
<div id="paymentOptionsModal" class="modal">
  <div class="modal-content">
    <p>Select Payment Method</p>
    <button id="fullPaymentBtn">Make Full Payment</button>
    <button id="partialPaymentBtn">Make Partial Payment</button>
    <br><br>
    <button onclick="closePaymentModal()">Cancel</button>
  </div>
</div>

</div>


<script>
    function filterOrders() {
        const input = document.getElementById('orderSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tr');
        rows.forEach(row => {
            const service = row.cells[3].textContent.toLowerCase();
            const name = row.cells[2].textContent.toLowerCase();
            row.style.display = (service.includes(input) || name.includes(input)) ? '' : 'none';
        });
    }


    function toggleDropdown(trigger) {
        const dropdown = trigger.nextElementSibling;

        // Close other open dropdowns
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if (d !== dropdown) d.style.display = 'none';
        });

        // Toggle this one
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    }

    // Close dropdowns if clicked outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
        }
    });


    let selectedOrderId = null;

    function closePaymentModal() {
        document.getElementById('paymentOptionsModal').style.display = 'none';
    }

    document.querySelectorAll('.pay-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            selectedOrderId = this.dataset.orderId;
            const allowPartial = this.dataset.allowPartial === '1';

            document.getElementById('paymentOptionsModal').style.display = 'block';
            document.getElementById('partialPaymentBtn').style.display = allowPartial ? 'inline-block' : 'none';
        });
    });

    document.getElementById('fullPaymentBtn').onclick = function () {
        //window.location.href = `payment.php?order_id=${selectedOrderId}&type=full`;
        checkPaymentStatus('full');
    
    };

    document.getElementById('partialPaymentBtn').onclick = function () {
       // window.location.href = `payment.php?order_id=${selectedOrderId}&type=partial`;
    
        checkPaymentStatus('partial');
    };
    
    
    function checkPaymentStatus(type) {
        console.log("Checking status for:", selectedOrderId, type);

        fetch('check-payment-status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${selectedOrderId}&type=${type}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'done') {
            alert(data.message);
        } else if (data.status === 'proceed') {
            //const url =;
            // window.location.href = `payment.php?order_id=${selectedOrderId}&type=${data.type}&amount=${data.amount}`;
            window.location.href = `payment.php`;
        } else {
            alert(data.message);
        }
    });
}
   

</script>

</body>
</html>

