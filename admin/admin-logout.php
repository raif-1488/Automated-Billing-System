<?php
session_name("admin_session");
session_start();
session_destroy();
header("Location: admin-login.php");
