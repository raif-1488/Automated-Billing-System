<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require '/../vendor/autoload.php';  
// require '../vendor/phpmailer/phpmailer/src/Exception.php';
// require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
// require '../vendor/phpmailer/phpmailer/src/SMTP.php';


function sendCredentials($toEmail, $username, $plainPassword) {
    $mail = new PHPMailer(true);

    try {
        //$mail->SMTPDebug = 2; // add this
        // $mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // Use App Password, not your Gmail password
        $mail->Username   = 'chhavinawria@gmail.com'; // replace
        $mail->Password   = 'ppwbicxczxtvgjvp';    // replace
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //tls; 
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('chhavinawria@gmail.com', 'chhavi');
        $mail->addAddress($toEmail, $username);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Account Credentials';
        $mail->Body    = "
            <h3>Hello $username,</h3>
            <p>Your account has been successfully created.</p>
            <p><strong>Email:</strong> $toEmail<br>
            <strong>Password:</strong> $plainPassword</p>
            <p>Please login and change your password if needed.</p>
            <br><p>Regards,<br>Get Catalyzed Team</p>
        ";

        $mail->send();
        //echo 'Message has been sent';
        return true;
    } catch (Exception $e) {
        echo 'Message could not be sent.';
        return false;
    }
}


function sendRenewalReminder($toEmail, $username, $serviceName, $expiryDate, $daysLeft) {
    echo $toEmail;
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host          = 'smtp.gmail.com';
        $mail->SMTPAuth      =  true;
        $mail->Username      = 'chhavinawria@gmail.com';
        $mail->Password      = 'ppwbicxczxtvgjvp';
        $mail->SMTPSecure    =  PHPMailer::ENCRYPTION_STARTTLS;//'tls'; //'ssl'; 
        $mail->Port          = 587;//465;

        $mail->setFrom('chhavinawria@gmail.com', 'Get Catalyzed');
        $mail->addAddress($toEmail, $username);

        $mail->isHTML(true);
        $mail->Subject = "Renewal Remainder - Service Expiry in $daysLeft Day(s)";
       

        $mail->Body = "
            <h3>Hello $username, </h3>
            <p>This is to remind you that your service <strong>$serviceName</strong>  will expire on <strong>" . $expiryDate->format('d M Y') . "</strong>.</p>
            <p><strong>Days remaining:</strong> $daysLeft day(s)</p>
            <p>To avoid interruption, please renew your service before expiry.</p>
            <br>
            <p>Regards,<br>Get Catalyzed Team</p> 
             
        ";
        

        $mail->send();
        echo "Remainder sent to $toEmail (Days left: $daysLeft) <br>";
       // error_log("Reminder sent to $toEmail");
       // $mail->SMTPDebug = 2;
        return true;

    } catch (Exception $e) {
        echo "Email error for $toEmail: {$mail->ErrorInfo}<br>";
        return false;
    }
 
}

function sendPaymentReminder($toEmail, $username,$serviceName, $gid, $daysSince) {
    //echo "HIIII";
    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = 2;  // Verbose debug output (logs connection issues)
        // $mail->Debugoutput = 'html';  // To print logs on page

        $mail->isSMTP();
        $mail->Host          = 'smtp.gmail.com';
        $mail->SMTPAuth      =  true;
        $mail->Username      = 'chhavinawria@gmail.com';
        $mail->Password      = 'ppwbicxczxtvgjvp';
        $mail->SMTPSecure    =  'tls';//'ssl';  //PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port          =  587; //465; 

        $mail->setFrom('chhavinawria@gmail.com', 'Get Catalyzed');
        $mail->addAddress($toEmail, $username);

        $mail->isHTML(true);
        $mail->Subject = "Payment Reminder - Day {$daysSince}";
      
        $mail->Body = "
                <h3>Dear $username,</h3> 
                <p>payment for your <strong>service: $serviceName </strong> is still pending. Kindly complete payment to avoid expiration.</p>
                <br>
                <p>Regards,<br>Get Catalyzed Team</p>
                ";
        $mail->send();

        echo "Reminder sent to $toEmail  <br>";                            
        return true;

    } catch (Exception $e) {
        echo "Email error for $toEmail: {$mail->ErrorInfo}<br>";
        return false;
    }
 
}

function sendPaymentSuccessMail($toEmail, $username, $serviceName, $paymentId, $amountPaid) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'chhavinawria@gmail.com';
        $mail->Password   = 'ppwbicxczxtvgjvp';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('chhavinawria@gmail.com', 'Get Catalyzed');
        $mail->addAddress($toEmail, $username);

        $mail->isHTML(true);
        $mail->Subject = "Payment Successful - Receipt for $serviceName";

        $mail->Body = "
            <h3>Dear $username,</h3>
            <p>We have successfully received your payment for <strong>$serviceName</strong>.</p>
            <p><strong>Payment ID:</strong> $paymentId</p>
            <p><strong>Amount Paid:</strong> ₹" . number_format($amountPaid, 2) . "</p>
            <br>
            <p>Thank you for your purchase!</p>
            <p>Regards,<br>Get Catalyzed Team</p>
        ";

        $mail->send();

        echo "Success email sent to $toEmail <br>";
        return true;

    } catch (Exception $e) {
        echo "Email error for $toEmail: {$mail->ErrorInfo}<br>";
        return false;
    }
}


?>
