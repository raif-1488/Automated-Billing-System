<?php
require_once 'includes/auth-user.php';  // to ensure consumer is logged in
require_once 'includes/db.php';

// session_start();

$consumer_id = $_SESSION['consumer_id'] ?? null;
$paymentData = $_SESSION['current_payment'] ?? null;

if (!$consumer_id) {
    header("Location: cons-login.php");
    exit();
}

if (!$paymentData) {
    header("Location: cons-orders.php");
    exit();
}

$order_id = $paymentData['order_id'] ?? null;
$type = $paymentData['type'] ?? 'full'; 
$plan = $type;
$custom_amount = $paymentData['amount'] ?? null;

//echo $custom_amount;

if ($custom_amount !== null) {
    $amount = (float) $custom_amount;
    if ($amount <= 0) {
        echo "<div style='padding:20px; background:#eaeaea; border-radius:10px; text-align:center; margin:50px auto; max-width:600px;'>
                <h2 style='color:green;'>Payment already completed.</h2>
            </div>";
        exit;
}
}


// Fetch order details and verify it belongs to logged-in user
$stmt = $pdo->prepare("
    SELECT o.*, s.service_name 
    FROM orders o 
    JOIN services s ON o.service_id = s.service_id 
    WHERE o.order_id = :order_id AND o.consumer_id = :consumer_id
");
$stmt->execute(['order_id' => $order_id, 
                'consumer_id' => $consumer_id
             ]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);



//to pre fill values in razorpay "startPayment" function 
$stmt = $pdo->prepare("select name, email, contact from consumers where consumer_id = ?");
$stmt->execute([$consumer_id]);
$consumer = $stmt->fetch(PDO::FETCH_ASSOC);

$consumer_name = htmlspecialchars($consumer['name']);
$consumer_email = htmlspecialchars($consumer['email']);
$consumer_contact = htmlspecialchars($consumer['contact']);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        .container {
            padding: 30px;
            max-width: 500px;
            margin: auto;
            border: 1px solid #ccc;
            margin-top: 50px;
            border-radius: 8px;
            box-shadow: 0 0 10px #aaa;
        }
        .container h2 {
            text-align: center;
        }
        .info {
            margin-bottom: 15px;
        }
        .btn {
            padding: 10px 15px;
            background-color: rgb(55, 162, 98);
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: block;
            width: 90%;
            text-align: center;
        }
        .btn:hover {
            background-color: rgb(52, 118, 88);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment</h2>
    <div class="info"><strong>Service:</strong> <?= htmlspecialchars($order['service_name']) ?></div>
    <div class="info"><strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?></div>
    <div class="info"><strong>To be Paid Now:</strong> ₹<?= number_format($amount, 2) ?></div>

    <!-- <a href="pay-online.php?order_id=<?= $order_id ?>&type=full" id="pay" class="btn">Proceed to Payment</a> -->
    <div class="Card-footer">
        <button class="btn" id="pay" data-order-id="<?= $order_id ?>" data-type="<?= $type ?>" data-amount="<?= $amount?>">Proceed to Pay</button>
    </div>

</div>

<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
   $(document).ready(function(){
    $("#pay").click(function(){
        const orderId = $(this).data("order-id");
        const type = $(this).data("type");
        const amount = $(this).data("amount");

        $.ajax({
            type:"POST",
            url:"pay-online.php",
            data:{
                order_id: orderId,
                type: type,
                amount: amount
            },
            // dataType: 'json',
            success: function(res){            
                var r_order_id = JSON.parse(res).razorpay_order_id;
                var r_amount = JSON.parse(res).razorpay_amount;
                
                startPayment(r_order_id, r_amount)
             },
            //  error: function(xhr, status, err) {
            //     console.error("Status:", status);
            //     console.error("Error:", err);
            //     console.error("Response:", xhr.responseText); // <-- this helps a lot to know actual issue in console
            //     alert("Unable to initiate payment. Please try again.");
            // }

        })
    })
   });
  
    const consumerName = "<?= $consumer_name ?>";
    const consumerEmail = "<?= $consumer_email ?>";
    const consumerPhone = "<?= $consumer_contact ?>";

   function startPayment(r_order_id, r_amount) {
        var options = {
            key: "rzp_live_96vB6NzNb1i94F", // Enter the Key ID generated from the Dashboard
            amount: r_amount, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
            currency: "INR",
            name: "STP Drive",
            description: "Test transaction",
           //image: "https://razorpay.com/docs/build/browser/static/razorpay-docs-dark.6f09b030.svg"
            image: "https://cdn.razorpay.com/logos/GhRQcyean79PqE_medium.png",
            order_id: r_order_id, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            prefill: {
                name: consumerName,
                email: consumerEmail,
                contact: consumerPhone
            },
            // notes: {
            //     address: "Razorpay Corporate Office"
            // },
            theme: {
                "color": "#3399cc"
            },

            "handler": function (response){
                //window.location="cons-login.php";
                const paymentId = response.razorpay_payment_id;
               // const OrderID = options.order_id; //razorpay order_id
                const order_id = "<?= $order_id ?>"
                window.location = `payment-success.php?payment_id=${paymentId}&order_id=${order_id}`;  //&razorpay_order_id=${OrderID}
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();

        rzp.on('payment.failed', function (response){
            // alert(response.error.code);
            // alert(response.error.description);
            // alert(response.error.source);
            // alert(response.error.step);
            alert(response.error.reason);
            // alert(response.error.metadata.order_id);
            // alert(response.error.metadata.payment_id);
        });
    }

</script>
</body>
</html>
