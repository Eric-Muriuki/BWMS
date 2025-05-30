<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Validate ticket ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ticket ID.";
    exit();
}

$ticket_id = intval($_GET['id']);

// Fetch ticket details
$query = "SELECT * FROM support_tickets WHERE id = $ticket_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Support ticket not found.";
    exit();
}

$ticket = mysqli_fetch_assoc($result);

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply'])) {
    $reply = mysqli_real_escape_string($conn, trim($_POST['reply']));
    $status = 'Resolved';

    $update = mysqli_query($conn, "UPDATE support_tickets SET reply = '$reply', status = '$status' WHERE id = $ticket_id");

    if ($update) {
        $success = "Reply sent successfully.";
        // Refresh ticket data
        $result = mysqli_query($conn, $query);
        $ticket = mysqli_fetch_assoc($result);
    } else {
        $error = "Failed to send reply.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Support Ticket - Admin</title>
  <link rel="stylesheet" href="../css/style.css" />
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
      padding: 1rem;
      color: white;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .container {
      max-width: 800px;
      margin: 2rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    
    h2 {
      text-align: center;
      color: var(--primary-dark);
      margin-bottom: 1.5rem;
      border-bottom: 2px solid var(--primary);
      padding-bottom: 0.5rem;
    }
    
    .ticket-info {
      background: rgba(255, 228, 208, 0.3);
      padding: 1.5rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--primary);
    }
    
    p {
      margin: 0.8rem 0;
    }
    
    label {
      font-weight: 600;
      color: var(--gray-dark);
      display: inline-block;
      min-width: 80px;
    }
    
    textarea {
      width: 100%;
      padding: 12px;
      margin: 0.8rem 0;
      min-height: 120px;
      border: 1px solid var(--gray);
      border-radius: 6px;
      resize: vertical;
      font-family: inherit;
      transition: border 0.3s;
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
      border-radius: 6px;
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
    
    .resolved-badge {
      background: #2e7d32;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      display: inline-block;
      margin-top: 1rem;
      font-weight: 600;
    }
    
    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
      font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
      .container {
        margin: 1rem;
        padding: 1.5rem;
      }
      
      .header h1 {
        font-size: 1.5rem;
      }
      
      label {
        display: block;
        margin-bottom: 0.3rem;
      }
    }
  </style>
</head>
<body>

<header class="header">
  <h1>Support Ticket Details</h1>
</header>

<main class="container">
  <?php if (isset($success)) echo "<div class='message'>$success</div>"; ?>
  <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

  <div class="ticket-info">
    <p><label>User ID:</label> <?= htmlspecialchars($ticket['user_id']) ?></p>
    <p><label>Name:</label> <?= htmlspecialchars($ticket['full_name']) ?></p>
    <p><label>Subject:</label> <?= htmlspecialchars($ticket['subject']) ?></p>
    <p><label>Status:</label> <?= htmlspecialchars($ticket['status']) ?></p>
  </div>

  <div class="ticket-info">
    <label>Message:</label>
    <p><?= nl2br(htmlspecialchars($ticket['message'])) ?></p>
  </div>

  <?php if (!empty($ticket['reply'])) : ?>
    <div class="ticket-info">
      <label>Reply:</label>
      <p><?= nl2br(htmlspecialchars($ticket['reply'])) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($ticket['status'] != 'Resolved') : ?>
    <form method="POST">
      <label for="reply">Reply to Ticket:</label>
      <textarea id="reply" name="reply" required placeholder="Enter your detailed reply here..."></textarea>
      <button type="submit">Send Reply & Resolve</button>
    </form>
  <?php else : ?>
    <div class="resolved-badge">This ticket has been resolved</div>
  <?php endif; ?>
</main>

<footer>
  <p>Bank WiFi System &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>