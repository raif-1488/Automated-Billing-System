<?php 

session_name("consumer_session");
session_start();

require_once 'includes/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM consumers WHERE email = ?");
    $stmt->execute([$email]);
    $cons = $stmt->fetch();

    if($cons && password_verify($password, $cons['password'])) {
        $_SESSION['cons_logged_in'] = true;
        $_SESSION['consumer_id'] = $cons['consumer_id'];
        $_SESSION['email'] = $cons['email'];
        
        header("Location: cons-dashboard.php");
        exit;
    }
    else {
        $error = "Invalid email or password";
    }
}
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Get Catalyzed</title>
    <style>
        
        :root {
            --primary-rust-yellow: #E0A75E; 
            --dark-text: #333; 
            --light-bg: #f5f7fa; 
            --card-bg: #ffffff; 
            --input-border: #e0e0e0; 
            --focus-glow: rgba(224, 167, 94, 0.3); 
            --error-color: #dc3545; 
            --link-color: #007bff; 
            --footer-text: #777; 
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: var(--light-bg);
            display: flex;
            flex-direction: column; 
            min-height: 100vh; 

            overflow-x: hidden; 
        }

        .main-content-wrapper {
            flex-grow: 1; 
            display: flex;
            justify-content: center; 
            align-items: center;
            width: 100%;
            padding: 20px; 
            box-sizing: border-box; 
        }

        .login-container {
            display: flex;
            width: 80%;
            max-width: 850px; 
            border-radius: 15px;
            overflow: hidden;
            background-color: var(--card-bg); 
        }

        .login-image-section {
            flex: 1.2; 
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            background-color: #fcfcfc; 
        }

        .login-image-section img {
            max-width: 90%;
            height: auto;
            object-fit: contain; 
            display: block; 
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1)); 
        }

        .login-form-section {
            flex: 1; /* Less space for the form section */
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .brand-logo img {
            max-width: 180px; 
            height: auto;
            display: block;
            margin: 0 auto; 
        }

        .login-form-section h2 {
            font-size: 2.5em; 
            color: var(--dark-text);
            margin-bottom: 25px;
            font-weight: 700; 
        }

        .error-message {
            color: var(--error-color);
            background-color: #ffebeba6;
            border: 1px solid var(--error-color);
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 0.9em;
            width: 100%;
            box-sizing: border-box;
        }

        .login-form form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px; 
        }

        .login-form input {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid var(--input-border);
            border-radius: 10px;
            font-size: 1em;
            color: var(--dark-text);
            background-color: #fdfdfd;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box; 
        }

        .login-form input:focus {
            border-color: var(--primary-rust-yellow);
            outline: none;
            box-shadow: 0 0 0 4px var(--focus-glow); 
        }

        .login-form button {
            width: 100%;
            padding: 16px;
            background: #f9c62dff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.15em; 
            font-weight: 600;
            margin-top: 15px;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(224, 167, 94, 0.2); 
        }

        .login-form button:hover {
            background: #CC9A4F; 
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(224, 167, 94, 0.4);
        }

        .forgot-password {
            margin-top: 20px;
            font-size: 0.95em;
        }

        .forgot-password a {
            color: var(--link-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-rust-yellow);
            text-decoration: underline;
        }

        .footer {
            width: 100%;
            text-align: center;
            padding: 20px 0; 
            font-size: 0.8em;
            color: var(--footer-text);
            
        }

        .footer a {
            color: var(--link-color);
            text-decoration: none;
            margin: 0 8px;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--primary-rust-yellow);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column; 
                width: 95%;
                max-width: 450px;
            }

            .login-image-section {
                padding: 20px;
                order: 1;
            }

            .login-image-section img {
                max-width: 60%; 
            }

            .login-form-section {
                padding: 30px 25px;
                order: 2; 
            }

            .brand-logo {
                margin-bottom: 20px;
            }

            .brand-logo img {
                max-width: 150px;
            }

            .login-form-section h2 {
                font-size: 2em;
                margin-bottom: 20px;
            }

            .login-form input {
                padding: 12px 15px;
            }

            .login-form button {
                padding: 14px;
                font-size: 1.05em;
            }

            .error-message {
                padding: 8px 12px;
            }

            .forgot-password {
                font-size: 0.9em;
            }
        }

        @media (max-width: 480px) {
            .login-form-section {
                padding: 25px 20px;
            }
            .login-image-section img {
                max-width: 70%;
            }
            .login-form-section h2 {
                font-size: 1.8em;
            }
            .login-form input {
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>

<div class="main-content-wrapper">
    <div class="login-container">
        <div class="login-image-section">
            <img src="images/cons-login.jpg" alt="Login Illustration">
        </div>
        <div class="login-form-section">
            <div class="brand-logo">
                <img src="images/logo.jpg" alt="Get Catalyzed Logo">
            </div>
            <h2>Welcome Back!</h2>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="login-form">
                <form method="POST" autocomplete="on">
                    <input type="email" name="email" placeholder="Email address" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
                <div class="forgot-password">
                    <a href="cons-forgot-pass.php">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <a href="privacy-policy.php">Privacy Policy</a> •
    <a href="terms.php">Terms & Conditions</a> •
    <a href="refund.php">Refund Policy</a>
</div>

</body>
</html>