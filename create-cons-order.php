<?php
require_once 'includes/auth-user.php';
require_once 'includes/db.php';

$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['consumer_id'])) {
    header('Location: cons-login.php');
    exit();
}

$consumer_id = $_SESSION['consumer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        input[readonly] {
            background-color: #f9f9f9;
        }
        button {
            padding: 6px 12px;
            margin-left: 10px;
        }
        #createOrderBtn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        #createOrderBtn:disabled {
            background-color: grey;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <h2>Create New Order</h2>
   
    <form method="POST" action="submit.php" id="orderForm">
    
        <input type="hidden" name="consumer_id" value="<?= htmlspecialchars($consumer_id) ?>">

        <div class="section">
            <label>Service:</label>
            <input type="text" name="service_name" id="serviceInput" placeholder="Select service" readonly required>
            <input type="hidden" name="service_id" id="service_id">
            <input type="hidden" name="service_gst_price" id="service_gst_price">
            <button type="button" onclick="openServicePicker()">Browse</button>
        </div>

        <div class="section">
            <label>Total Amount:</label>
            <input type="text" id="totalAmount" readonly>
        </div>

        <button type="submit" id="createOrderBtn" disabled>Create Order</button>
    </form>


<script>
    //let selectedConsumer = null;
    let selectedService = null;

    const consumerId = "<?= htmlspecialchars($consumer_id) ?>";

    function openServicePicker() {
        window.open('admin/browse-service.php?consumer_id=' + consumerId, 'Select Service', 'width=600,height=400');
    }


    function setService(id, name, price) {
        selectedService = { id, name, price };
        document.getElementById('serviceInput').value = name;
        document.getElementById('service_id').value = id;
        document.getElementById('service_gst_price').value = price;
        document.getElementById('totalAmount').value = price;
        checkReadyToCreate();
    }

    function checkReadyToCreate() {
        document.getElementById('createOrderBtn').disabled = !(selectedService);
    }
</script>

</body>
</html>
