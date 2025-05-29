<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle ticket reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['reply'])) {
    $ticket_id = $_POST['ticket_id'];
    $reply = trim($_POST['reply']);

    if (!empty($reply)) {
        $stmt = $conn->prepare("UPDATE tickets SET reply = ?, status = 'Replied', replied_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $reply, $ticket_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all support tickets with user info
$query = "
    SELECT st.*, u.full_name AS employee_name
    FROM tickets st
    JOIN users u ON st.user_id = u.id
    ORDER BY st.created_at DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Support Tickets - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; }
    .header { background: #003366; color: white; padding: 15px; text-align: center; }
    .container { padding: 30px; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
      vertical-align: top;
    }
    tr:hover { background-color: #f9f9f9; }
    textarea {
      width: 100%;
      height: 60px;
      padding: 10px;
      margin-top: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      resize: vertical;
      font-family: Arial, sans-serif;
    }
    button {
      background-color: #0072ce;
      color: white;
      border: none;
      padding: 8px 14px;
      margin-top: 5px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover { background-color: #00509e; }
    .status { font-weight: bold; }
  </style>
</head>
<body>

<div class="header">
  <h1>Support Tickets</h1>
</div>

<div class="container">
  <h2>View & Respond to Support Tickets</h2>

  <table>
    <thead>
      <tr>
        <th>Employee Name</th>
        <th>Subject</th>
        <th>Description</th>
        <th>Date</th>
        <th>Status</th>
        <th>Reply</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($ticket = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($ticket['employee_name']) ?></td>
            <td><?= htmlspecialchars($ticket['subject']) ?></td>
            <td><?= nl2br(htmlspecialchars($ticket['message'])) ?></td>
            <td><?= htmlspecialchars($ticket['created_at']) ?></td>
            <td class="status"><?= htmlspecialchars($ticket['status']) ?></td>
            <td><?= nl2br(htmlspecialchars($ticket['reply'] ?? '')) ?></td>
            <td>
              <?php if ($ticket['status'] !== 'Replied' && $ticket['status'] !== 'Closed'): ?>
                <form method="POST" action="">
                  <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                  <textarea name="reply" placeholder="Enter your reply..." required></textarea>
                  <button type="submit">Send Reply</button>
                </form>
              <?php else: ?>
                <em>Resolved</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">No support tickets found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
