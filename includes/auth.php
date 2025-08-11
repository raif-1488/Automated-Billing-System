<?php
if(session_status() == PHP_SESSION_NONE ){
    session_name("admin_session");
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])){
    header('Location: admin-login.php');
    exit;
}

?>