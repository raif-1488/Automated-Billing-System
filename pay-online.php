<?php 
   
   ini_set('display_errors', 1);
   error_reporting(E_ALL);

    require_once 'includes/auth-user.php';
    require_once 'includes/db.php';
    require("razorpay/Razorpay.php");
    use Razorpay\Api\Api;
    
    // $api_key = "rzp_test_yDhlshgC0Cb7GE"; 
    // $api_secret = "WpkAWVn8hLLhB2WsUiB9G3jI";

    $api_key = "rzp_live_96vB6NzNb1i94F";
    $api_secret = "rYuE1BJ25rGt4xu6PCAmfmmT";
    
    //$plan = $_POST['plan'];

    $orderId = $_POST['order_id'] ?? null;
    $type = $_POST['type'] ?? 'full'; 
    $amount = $_POST['amount']?? 'null' ;


    $amount = floatval($amount);
    if (!$orderId || !$amount || $amount <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid order ID or amount"]);
        exit;
    }
    

    try{
        $api = new Api($api_key, $api_secret);
        $plan = $type;
        
        //razorpay order creation (amount in paise)

         $order = $api->order->create(array(   //to create order for every payment
            'receipt' => 'receipt_'.time(),  //invoice bill no. we could give
            'amount' => round( $amount * 100),  //this is in paise, to convert to rupees multiply it by 100
            'currency' => 'INR', 
            'notes'=> array(
                'orderId' => $orderId,
                'plan'=> $plan,
               // 'key2'=> 'value2' //here additional info of any product or user we can give to be shown after payment has done
            ))); // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
            

            $razorpay_order_id = $order['id'];
            $razorpay_amount = $order['amount'];

            echo json_encode(array("razorpay_order_id"=>$razorpay_order_id, 
                                   "razorpay_amount"=>$razorpay_amount
                                  ));
          
    
        }
        catch(Exception $e){
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(["error" => $e->getMessage()]);
            exit;

            //die("Error ".$e->getMessage()); //to get message in readable form
        }

?>