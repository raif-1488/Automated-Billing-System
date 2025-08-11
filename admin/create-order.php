<?php
require_once '../includes/auth.php'; 
require_once '../includes/db.php';

$currentPage = basename($_SERVER['PHP_SELF']);

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


<form method="POST" action="submit-order.php" id="orderForm">
    
    <div class="section">
        <label>Customer:</label>
        <input type="text" name="consumer_name" id="consumerInput" placeholder="Select customer" readonly required>
        <input type="hidden" name="consumer_id" id="consumer_id">
        <button type="button" onclick="openCustomerPicker()">Browse</button>
    </div>

    <div class="section">
        <label>Service:</label>
        <input type="text" name="service_name" id="serviceInput" placeholder="Select service" readonly required>
        <input type="hidden" name="service_id" id="service_id">
        <input type="hidden" name="service_price" id="service_price">
        <button type="button" onclick="openServicePicker()">Browse</button>
    </div>

    <div class="section">
        <label>Total Amount:</label>
        <input type="text" id="totalAmount" readonly>
    </div>

    <button type="submit" id="createOrderBtn" disabled>Create Order</button>
</form>

<script>
    let selectedConsumer = null;
    let selectedService = null;

    function openCustomerPicker() {
        window.open('browse-consumer.php', 'Select Customer', 'width=600,height=400');
    }


    function openServicePicker() {
        if (!selectedConsumer) {
            alert("Please select a customer first.");
            return;
        }
        window.open('browse-service.php?consumer_id=' + selectedConsumer.id, 'Select Service', 'width=600,height=400');
    }

    function setConsumer(id, name) {
        selectedConsumer = { id, name };
        document.getElementById('consumerInput').value = name;
        document.getElementById('consumer_id').value = id;
        checkReadyToCreate();
    }

    
    function setService(id, name, price) {
        selectedService = { id, name, price };
        document.getElementById('serviceInput').value = name;
        document.getElementById('service_id').value = id;
        document.getElementById('service_price').value = price;
        document.getElementById('totalAmount').value = price;
        checkReadyToCreate();
    }

    function checkReadyToCreate() {
        document.getElementById('createOrderBtn').disabled = !(selectedConsumer && selectedService);
    }
</script>

</body>
</html>
