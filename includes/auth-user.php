<?php
if(session_status() == PHP_SESSION_NONE ){
    session_name("consumer_session");
    session_start();
}

if(!isset($_SESSION['consumer_id'])){
    header('Location: cons-login.php');
    exit;
}

?>