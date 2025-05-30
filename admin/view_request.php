<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure request ID is provided
if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$request_id = intval($_GET['id']);

// Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $reply = trim($_POST['reply']);
    if (!empty($reply)) {
        $stmt = $conn->prepare("UPDATE wifi_requests SET reply = ?, status = 'Approved' WHERE id = ?");
        $stmt->bind_param("si", $reply, $request_id);
        $stmt->execute();
        $stmt->close();
        $success = "Reply sent successfully!";
        // Refresh request data
        $stmt = $conn->prepare("SELECT * FROM wifi_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Please enter a reply with WiFi credentials.";
    }
}

// Fetch request details
$stmt = $conn->prepare("SELECT * FROM wifi_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    echo "Request not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View WiFi Request - Bank WiFi System</title>
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
      background: var(--light);
      margin: 0;
      padding: 0;
      color: var(--gray-dark);
      line-height: 1.6;
    }
    
    .header {
      background: linear-gradient(135deg, var(--dark), var(--gray-dark));
      padding: 1.5rem;
      color: white;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .container {
      max-width: 800px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    h2 {
      color: var(--primary-dark);
      margin-bottom: 1.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--primary);
    }
    
    .info-card {
      background: rgba(255, 228, 208, 0.3);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--primary);
    }
    
    .info-row {
      display: flex;
      margin-bottom: 0.8rem;
    }
    
    label {
      font-weight: 600;
      color: var(--gray-dark);
      min-width: 150px;
    }
    
    .value {
      color: var(--gray-dark);
    }
    
    textarea {
      width: 100%;
      padding: 12px;
      margin: 1rem 0;
      min-height: 120px;
      border: 1px solid var(--gray);
      border-radius: 8px;
      resize: vertical;
      font-family: inherit;
      transition: all 0.3s;
    }
    
    textarea:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 2px rgba(246, 112, 17, 0.2);
    }
    
    button {
      background: var(--primary);
      color: white;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      display: block;
      width: 100%;
      max-width: 200px;
      margin: 1.5rem auto 0;
    }
    
    button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }
    
    .message {
      color: #2e7d32;
      background: #e8f5e9;
      padding: 1rem;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      text-align: center;
      border-left: 4px solid #2e7d32;
    }
    
    .error {
      color: #c62828;
      background: #ffebee;
      padding: 1rem;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      text-align: center;
      border-left: 4px solid #c62828;
    }
    
    .approved-badge {
      background: #2e7d32;
      color: white;
      padding: 0.8rem;
      border-radius: 6px;
      text-align: center;
      margin-top: 1.5rem;
      font-weight: 600;
    }
    
    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 2rem;
      font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
      .container {
        margin: 1rem;
        padding: 1.5rem;
      }
      
      .info-row {
        flex-direction: column;
      }
      
      label {
        margin-bottom: 0.3rem;
      }
      
      button {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<header class="header">
  <h1>WiFi Request Details</h1>
</header>

<main class="container">
  <?php if (isset($success)) echo "<div class='message'>$success</div>"; ?>
  <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

  <div class="info-card">
    <div class="info-row">
      <label>Employee Name:</label>
      <span class="value"><?= htmlspecialchars($request['employee_name']) ?></span>
    </div>
    <div class="info-row">
      <label>Department:</label>
      <span class="value"><?= htmlspecialchars($request['department']) ?></span>
    </div>
    <div class="info-row">
      <label>Request Date:</label>
      <span class="value"><?= htmlspecialchars($request['created_at']) ?></span>
    </div>
    <div class="info-row">
      <label>Status:</label>
      <span class="value"><?= htmlspecialchars($request['status']) ?></span>
    </div>
  </div>

  <div class="info-card">
    <label>Reason for Request:</label>
    <p class="value"><?= nl2br(htmlspecialchars($request['reason'])) ?></p>
  </div>

  <?php if (!empty($request['reply'])): ?>
    <div class="info-card">
      <label>WiFi Credentials:</label>
      <p class="value"><?= nl2br(htmlspecialchars($request['reply'])) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($request['status'] !== 'Approved'): ?>
    <form method="POST">
      <label>Reply with WiFi Credentials:</label>
      <textarea name="reply" required placeholder="Enter WiFi credentials (e.g., SSID: BankSecure | Password: xxxxxxxx)"></textarea>
      <button type="submit">Approve & Send Credentials</button>
    </form>
  <?php else: ?>
    <div class="approved-badge">This request has been approved</div>
  <?php endif; ?>
</main>

<footer>
  <p>Bank WiFi System &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>