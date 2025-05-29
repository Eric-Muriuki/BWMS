<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle reply submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'], $_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $reply_message = trim($_POST['reply_message']);

    if (!empty($reply_message)) {
        $reply_date = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("UPDATE wifi_requests SET reply_message = ?, reply_date = ?, status = 'Replied' WHERE id = ?");
        $stmt->bind_param("ssi", $reply_message, $reply_date, $request_id);

        if ($stmt->execute()) {
            $success = "Reply sent and status updated.";
        } else {
            $error = "Error sending reply.";
        }
        $stmt->close();
    } else {
        $error = "Reply message cannot be empty.";
    }
}

// Fetch all requests
$query = "SELECT wr.id, wr.user_id, u.full_name, wr.request_date, wr.request_message, wr.reply_message, wr.reply_date, wr.status 
          FROM wifi_requests wr
          JOIN users u ON wr.user_id = u.id
          ORDER BY wr.request_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage WiFi Requests - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; }
    .container {
      width: 95%; max-width: 1200px; margin: 30px auto; background: #fff;
      padding: 25px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
    }
    h2 { text-align: center; color: #003366; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td {
      padding: 12px; border: 1px solid #ccc; text-align: left;
    }
    th { background: #003366; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    textarea {
      width: 100%; height: 80px; resize: vertical; padding: 8px;
      margin-top: 10px; margin-bottom: 10px;
    }
    button {
      background-color: #0072ce; color: white; padding: 8px 15px;
      border: none; border-radius: 4px; cursor: pointer;
    }
    button:hover { background-color: #005baa; }
    .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>

<div class="container">
  <h2>Manage WiFi Access Requests</h2>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>User</th>
        <th>Date Requested</th>
        <th>Message</th>
        <th>Reply</th>
        <th>Date Replied</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['request_date']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['request_message'])) ?></td>
            <td>
              <?php if ($row['reply_message']): ?>
                <?= nl2br(htmlspecialchars($row['reply_message'])) ?>
              <?php else: ?>
                <form method="post">
                  <textarea name="reply_message" required></textarea>
                  <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                  <button type="submit">Send Reply</button>
                </form>
              <?php endif; ?>
            </td>
            <td><?= $row['reply_date'] ? htmlspecialchars($row['reply_date']) : '-' ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
              <?php if (!$row['reply_message']): ?>
                <em>Awaiting reply</em>
              <?php else: ?>
                <strong>Replied</strong>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">No WiFi requests found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
