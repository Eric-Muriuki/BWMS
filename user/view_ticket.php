<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect to login if admin not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Validate ticket ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ticket ID.";
    exit();
}

$ticket_id = intval($_GET['id']);

// Fetch ticket details
$stmt = $conn->prepare("
    SELECT t.*, u.fullname, u.email 
    FROM tickets t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.id = ?
");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ticket not found.";
    exit();
}

$ticket = $result->fetch_assoc();

// Handle admin reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reply'])) {
    $reply = htmlspecialchars(trim($_POST['reply']));

    $update = $conn->prepare("UPDATE tickets SET reply = ?, status = 'Replied', replied_at = NOW() WHERE id = ?");
    $update->bind_param("si", $reply, $ticket_id);

    if ($update->execute()) {
        header("Location: view_ticket.php?id=" . $ticket_id);
        exit();
    } else {
        $error = "Failed to send reply.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Ticket - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; }
    .container {
      width: 90%; max-width: 800px; margin: 40px auto;
      background: white; padding: 30px; border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { color: #005baa; }
    .ticket-info p { margin: 10px 0; }
    .label { font-weight: bold; color: #333; }
    textarea {
      width: 100%; height: 120px; padding: 10px; margin-top: 10px;
      border-radius: 5px; border: 1px solid #ccc;
    }
    .btn {
      background: #0072ce; color: white; padding: 10px 20px;
      text-decoration: none; border: none; border-radius: 5px;
      margin-top: 10px; cursor: pointer;
    }
    .btn:hover { background: #004080; }
    .status {
      display: inline-block; padding: 5px 10px; border-radius: 5px;
    }
    .Open { background: #f0ad4e; color: white; }
    .Replied { background: #5cb85c; color: white; }
    .Closed { background: #d9534f; color: white; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Ticket #<?= $ticket['id'] ?> - <?= htmlspecialchars($ticket['subject']) ?></h2>

    <div class="ticket-info">
      <p><span class="label">From:</span> <?= htmlspecialchars($ticket['fullname']) ?> (<?= htmlspecialchars($ticket['email']) ?>)</p>
      <p><span class="label">Submitted on:</span> <?= $ticket['created_at'] ?></p>
      <p><span class="label">Status:</span> <span class="status <?= $ticket['status'] ?>"><?= $ticket['status'] ?></span></p>
      <p><span class="label">Message:</span><br><?= nl2br(htmlspecialchars($ticket['message'])) ?></p>
      <?php if ($ticket['reply']): ?>
        <p><span class="label">Reply:</span><br><?= nl2br(htmlspecialchars($ticket['reply'])) ?></p>
        <p><span class="label">Replied on:</span> <?= $ticket['replied_at'] ?></p>
      <?php endif; ?>
    </div>

    <?php if ($ticket['status'] === 'Open'): ?>
      <form method="POST">
        <label for="reply"><strong>Reply to User:</strong></label>
        <textarea name="reply" required placeholder="Write your reply here..."></textarea>
        <br>
        <button type="submit" class="btn">Send Reply</button>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
