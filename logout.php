<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Redirect to home page or login page
header("Location: login.php"); 
exit;
?>
