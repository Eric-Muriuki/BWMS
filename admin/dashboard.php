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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    :root {
      --dark: #16151A;
      --primary: #F67011;
      --primary-dark: #873800;
      --gray-dark: #262626;
      --gray: #878787;
      --light: #FFE4D0;
    }
    
    body { 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
      margin: 0; 
      padding: 0;
      background-color: var(--light);
      color: var(--gray-dark);
      line-height: 1.6;
    }
    
    .header { 
      background: linear-gradient(135deg, var(--dark), var(--gray-dark));
      padding: 1rem;
      color: white;
      text-align: center;
      position: relative;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .container { 
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .welcome { 
      font-size: 1.4rem; 
      margin-bottom: 2rem;
      color: var(--gray-dark);
    }
    
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    
    .card {
      background-color: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      text-align: center;
      transition: all 0.3s ease;
      border-top: 4px solid var(--primary);
    }
    
    .card:hover { 
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(246, 112, 17, 0.1);
    }
    
    .card h3 { 
      margin: 1rem 0;
      color: var(--dark);
    }
    
    .card a {
      display: inline-block;
      text-decoration: none;
      color: var(--primary);
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .card a:hover { 
      color: var(--primary-dark);
      background-color: rgba(246, 112, 17, 0.1);
    }
    
    .logout { 
      position: absolute; 
      top: 1rem; 
      right: 1.5rem; 
      color: white; 
      text-decoration: none;
      padding: 0.5rem;
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .logout:hover { 
      text-decoration: none;
      background-color: rgba(255,255,255,0.2);
    }
    
    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }
      
      .header h1 {
        font-size: 1.5rem;
        padding-top: 0.5rem;
      }
      
      .logout {
        position: static;
        display: inline-block;
        margin-top: 0.5rem;
      }
    }
    
    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<header class="header">
  <h1>Admin Dashboard</h1>
  <a href="logout.php" class="logout">Logout</a>
</header>

<main class="container">
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
</main>

<footer>
  <p>Bank WiFi System &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>