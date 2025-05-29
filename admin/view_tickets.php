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
  <title>View Support Ticket - Admin</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 20px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 0 5px #ccc;
    }
    h2 {
      text-align: center;
      color: #005baa;
    }
    p {
      margin: 10px 0;
    }
    label {
      font-weight: bold;
    }
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      height: 80px;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: none;
    }
    button {
      background: #0072ce;
      color: white;
      padding: 10px 16px;
      border: none;
      margin-top: 10px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #004080;
    }
    .message {
      color: green;
      margin-bottom: 15px;
      text-align: center;
    }
    .error {
      color: red;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Support Ticket Details</h2>

  <?php if (isset($success)) echo "<p class='message'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <p><label>User ID:</label> <?= htmlspecialchars($ticket['user_id']) ?></p>
  <p><label>Name:</label> <?= htmlspecialchars($ticket['full_name']) ?></p>
  <p><label>Subject:</label> <?= htmlspecialchars($ticket['subject']) ?></p>
  <p><label>Message:</label><br /> <?= nl2br(htmlspecialchars($ticket['message'])) ?></p>
  <p><label>Status:</label> <?= htmlspecialchars($ticket['status']) ?></p>
  <p><label>Reply:</label><br /> <?= nl2br(htmlspecialchars($ticket['reply'])) ?></p>

  <?php if ($ticket['status'] != 'Resolved') : ?>
    <form method="POST">
      <label>Reply to Ticket:</label>
      <textarea name="reply" required placeholder="Enter your reply..."></textarea>
      <button type="submit">Send Reply</button>
    </form>
  <?php else : ?>
    <p><strong>This ticket has been resolved.</strong></p>
  <?php endif; ?>
</div>

</body>
</html>
