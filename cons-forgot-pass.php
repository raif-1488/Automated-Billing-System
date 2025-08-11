<?php
//require_once 'includes/auth-user.php';
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $message = '<p class="error">All fields are required.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error">Invalid email format.</p>';
    } elseif ($newPassword !== $confirmPassword) {
        $message = '<p class="error">Passwords do not match.</p>';
    } else {
        $stmt = $pdo->prepare("SELECT consumer_id FROM consumers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE consumers SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);

            $message = '<p class="success">Password has been reset successfully.</p>';
        } else {
            $message = '<p class="error">No consumer found with this email.</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            width: 60%;
            max-width: 300px;
            max-height: 600px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            text-align: center;
            background-color: #28a745;
            color: white;
            padding: 10px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 150px;
            
            margin-top: 50px;
            /* to align reset password to center */
            margin: 10px auto 0 auto;  
            
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: -10px;
        }

        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 10px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #007bff;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <?= $message ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your admin email" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Reset Password</button>
    </form>

    <div class="back-link">
        <a href="cons-login.php">← Back to Login</a>
    </div>
</div>

</body>
</html>
