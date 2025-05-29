<?php
// Start session if needed
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bank WiFi Management System</title>
  <link rel="stylesheet" href="css/style.css"> <!-- Link to your stylesheet -->
  <style>
    /* Embedded style for demo purposes, move to css/style.css */
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }
    header {
      background-color: #005baa;
      padding: 20px;
      color: white;
      text-align: center;
    }
    nav {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    .nav-button {
      background-color: #0072ce;
      border: none;
      color: white;
      padding: 15px 25px;
      text-decoration: none;
      font-size: 16px;
      border-radius: 8px;
      transition: 0.3s;
    }
    .nav-button:hover {
      background-color: #004f8a;
      cursor: pointer;
    }
    .container {
      max-width: 800px;
      margin: 50px auto;
      text-align: center;
    }
    footer {
      margin-top: 50px;
      padding: 20px;
      background-color: #ddd;
      text-align: center;
    }
  </style>
</head>
<body>

  <header>
    <h1>Bank WiFi Management System</h1>
    <p>Request WiFi access, get support, and manage credentials securely</p>
  </header>

  <div class="container">
    <nav>
      <a href="user/wifi_request.php" class="nav-button">Request WiFi Access</a>
      <a href="user/ticket_submit.php" class="nav-button">Submit Support Ticket</a>
      <a href="user/login.php" class="nav-button">Login</a>
      <a href="user/register.php" class="nav-button">Register</a>
    </nav>
  </div>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> Bank WiFi Management System. All rights reserved.</p>
  </footer>

</body>
</html>
