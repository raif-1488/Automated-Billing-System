<?php
require_once '../../includes/auth.php';     
require_once '../../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$admin_id = $_SESSION['admin_id'];
$adminData = ['admin_username' => '', 'email' => ''];

// Fetch current admin info dynamically
$stmt = $pdo->prepare("SELECT admin_username, email FROM admins WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$adminData = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'] ?? '';  //PHP's null coalescing operator ??, which is a safe way to check if a key exists in an array without throwing a warning.
    

    $changesMade = false;


    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';
    } 
    elseif (!empty($password) && $password !== $confirmPassword) {
        $message = '<p style="color:red;">Passwords do not match.</p>';
    } else{
        if(!empty($username)  && $username !== $adminData['admin_username'] ){
            $stmt = $pdo->prepare("UPDATE admins SET admin_username  = ? WHERE admin_id = ?");
            $stmt->execute([$username, $admin_id]);
            $changesMade = true;
        }
        if (!empty($email)  && $email !== $adminData['email'] ) {
            $stmt = $pdo->prepare("UPDATE admins SET email = ? WHERE admin_id = ?");
            $stmt->execute([$email, $admin_id]);
            $changesMade = true;
        }
        if(!empty($password)){
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
            $stmt->execute([$hashedPassword, $admin_id]);
            $changesMade = true;
        }
        
        if($changesMade){
           $message = '<p style="color:green;">Profile updated successfully!</p>';
        }

        // Refresh the fetched data
            $stmt = $pdo->prepare("SELECT admin_username, email FROM admins WHERE admin_id = ?");
            $stmt->execute([$admin_id]);
            $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    }

 } else {
    $stmt = $pdo->prepare("SELECT admin_username, email FROM admins WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <style>
        .main-content {
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        body.sidebar-collapsed .main-content {
            margin-left: 50px;
        }
        .profile-container {
            display: flex;
            gap: 30px;
            margin-top: 30px;
        }
        .profile-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .profile-box label {
            font-weight: bold;
            color: #333;
        }
        .profile-box input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-set {
            background-color: #28a745; 
            color: white;
            padding: 8px 16px;
            border: none;
            font-weight: normal;
            border-radius: 2px;
            cursor: pointer;
            margin-left: 500px;
            margin-top: 30px;
        }

        .btn-set:hover {
            background-color: #218838;
        }

        .btn-reset {
            background-color: #dc3545; 
            color: white;
            padding: 8px 16px;
            border: none;
            font-weight: normal;
            border-radius: 2px;
            cursor: pointer;
        }

        .btn-reset:hover {
            background-color: #c82333;
        }

    </style>

</head>


<body>

<?php include '../includes/sidebar.php';?>
<?php include '../includes/navbar.php';?>

<div class="main-content">
    <h2>Admin Profile</h2>
    <?= isset($message) ? $message : '' ?>


    <form method="POST" action=""> 
        <div class="profile-container">
            <!-- Username -->
            <div class="profile-box">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($adminData['admin_username']) ?>">
            </div>

            <div class="profile-box">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($adminData['email']) ?>">
            </div>

            <!-- Password -->
            <div class="profile-box">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********">
            </div>
            <div class="profile-box">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="">
            </div>

            
        </div>
        
        <div style="flex-direction: row; gap: 40px;">
                <button type="submit" name="update_profile" class="btn-set">Set</button>
                <button type="reset" class="btn-reset">Reset</button>
        </div>

    </form>
</div>

</body>
</html>