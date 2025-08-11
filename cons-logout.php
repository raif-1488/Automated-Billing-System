<?php
session_name("consumer_session");
session_start();

$_SESSION = [];

session_destroy();

header("Location: cons-login.php"); 
exit();

?>
