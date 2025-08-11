<?php
session_name("admin_session");
session_start();
require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email and password required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Successful login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            session_regenerate_id(true); // Prevent session fixation
            header("Location: admin-dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Get catalyzed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef1f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 25px;
        }

        .login-container input {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 40px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        h2 {
        color: #007bff; 
        font-family: Arial, sans-serif;
    }

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center"><a href="forgot-password.php">Forgot Password?</a></p>

    </div>
</body>
</html>





















