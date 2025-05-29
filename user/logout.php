<?php
session_start(); // Start session to access session variables

// Destroy all session variables
$_SESSION = [];
session_unset();
session_destroy();

// Optional: Redirect to login page (for users or admins)
header("Location: login.php");
exit();
