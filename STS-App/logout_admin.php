<?php
session_name("admin_session");
session_start();

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: admin_login.php");
exit();
?>
