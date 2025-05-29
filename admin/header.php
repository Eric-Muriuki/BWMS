<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f1f1f1; }
    .sidebar {
      height: 100vh; width: 200px; position: fixed;
      background-color: #003366; color: white; padding-top: 20px;
    }
    .sidebar a {
      display: block; color: white; padding: 12px 16px;
      text-decoration: none; font-size: 16px;
    }
    .sidebar a:hover {
      background-color: #0059b3;
    }
    .main {
      margin-left: 200px; padding: 20px;
    }
    .topbar {
      background: #0072ce; color: white;
      padding: 10px 20px; position: fixed;
      left: 200px; right: 0; top: 0;
      z-index: 1000;
    }
    .topbar h2 {
      margin: 0; font-size: 20px;
    }
    .logout {
      float: right; color: white; text-decoration: none;
    }
    .logout:hover { text-decoration: underline; }
  </style>
</head>
<body>

<div class="sidebar">
  <h3 style="text-align:center;">Admin Panel</h3>
  <a href="dashboard.php">Dashboard</a>
  <a href="manage_requests.php">WiFi Requests</a>
  <a href="manage_tickets.php">Support Tickets</a>
  <a href="logout.php">Logout</a>
</div>

<div class="topbar">
  <h2>Bank WiFi Admin <a href="logout.php" class="logout">Logout</a></h2>
</div>

<div class="main">
