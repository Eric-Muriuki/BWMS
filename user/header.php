<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bank WiFi Management System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; }
    .navbar {
      background-color: #0072ce;
      overflow: hidden;
      padding: 14px 20px;
      color: white;
    }
    .navbar h1 {
      float: left;
      margin: 0;
      font-size: 24px;
    }
    .navbar .nav-links {
      float: right;
    }
    .navbar .nav-links a {
      color: white;
      padding: 10px 16px;
      text-decoration: none;
      display: inline-block;
    }
    .navbar .nav-links a:hover {
      background-color: #004080;
      border-radius: 5px;
    }
    .clear { clear: both; }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>Bank WiFi System</h1>
    <div class="nav-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="wifi_request.php">Request WiFi</a>
      <a href="ticket_submit.php">Support</a>
      <a href="logout.php">Logout</a>
    </div>
    <div class="clear"></div>
  </div>
