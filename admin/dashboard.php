<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Admin name
$admin_name = $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background-color: #f5f7fa; }
    .header { background-color: #003366; padding: 15px; color: white; text-align: center; }
    .container { padding: 30px; }
    .welcome { font-size: 1.4em; margin-bottom: 20px; }
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .card {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }
    .card:hover { transform: scale(1.03); }
    .card h3 { margin: 10px 0; }
    .card a {
      text-decoration: none;
      color: #003366;
      font-weight: bold;
    }
    .logout { position: absolute; top: 15px; right: 20px; color: white; text-decoration: none; }
    .logout:hover { text-decoration: underline; }
  </style>
</head>
<body>

<div class="header">
  <h1>Admin Dashboard</h1>
  <a href="logout.php" class="logout">Logout</a>
</div>

<div class="container">
  <div class="welcome">Welcome, <strong><?= htmlspecialchars($admin_name) ?></strong> 👋</div>

  <div class="card-grid">
    <div class="card">
      <h3>Manage WiFi Requests</h3>
      <a href="manage_requests.php">View Requests</a>
    </div>

    <div class="card">
      <h3>Support Tickets</h3>
      <a href="manage_tickets.php">View Tickets</a>
    </div>

  </div>
</div>

</body>
</html>
