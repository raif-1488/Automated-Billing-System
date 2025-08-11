<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$name = "sachin";
$email = "s@gmail.com";
$plainPassword = "s@123";

$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (admin_username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $hashedPassword]);

echo "Admin inserted successfully with hashed password.";
?>
