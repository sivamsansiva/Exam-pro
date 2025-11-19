<?php
session_start(); // Start session

// Destroy session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
