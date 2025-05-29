<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's WiFi requests and admin replies
$stmt = $conn->prepare("SELECT request_date, request_message, reply_message, reply_date, status FROM wifi_requests WHERE user_id = ? ORDER BY request_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wifi_requests = $stmt->get_result();
$stmt->close();

// Fetch user's support tickets and replies
$stmt2 = $conn->prepare("SELECT subject, message, reply, created_at, status FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$support_tickets = $stmt2->get_result();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; }
    .container {
      width: 90%; max-width: 1000px; margin: 40px auto; background: white;
      padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
      margin-bottom: 50px;
    }
    h2, h3 { color: #005baa; }
    h2 { text-align: center; margin-bottom: 30px; }
    table {
      width: 100%; border-collapse: collapse; margin-top: 15px;
    }
    th, td {
      padding: 12px 15px; border: 1px solid #ccc; text-align: left;
    }
    th {
      background: #005baa; color: white;
    }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .btn {
      background-color: #0072ce; color: white; padding: 10px 15px;
      text-decoration: none; border-radius: 5px;
    }
    .btn:hover { background-color: #004080; }
    .top-bar {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 40px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container">
    <div class="top-bar">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
      <a class="btn" href="logout.php">Logout</a>
    </div>

    <!-- WiFi Access Requests Table -->
    <h3>Your WiFi Access Requests</h3>
    <table>
      <thead>
        <tr>
          <th>Date Requested</th>
          <th>Your Message</th>
          <th>Reply from Admin</th>
          <th>Date Replied</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($wifi_requests->num_rows > 0): ?>
          <?php while ($row = $wifi_requests->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['request_date']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['request_message'])) ?></td>
              <td>
                <?php if (!empty($row['reply_message'])): ?>
                  <?= nl2br(htmlspecialchars($row['reply_message'])) ?>
                <?php else: ?>
                  <em>Awaiting reply</em>
                <?php endif; ?>
              </td>
              <td><?= $row['reply_date'] ? htmlspecialchars($row['reply_date']) : '-' ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">No WiFi requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="container">
    <!-- Support Tickets Table -->
    <h3>Your Support Tickets</h3>
    <table>
      <thead>
        <tr>
          <th>Date Submitted</th>
          <th>Subject</th>
          <th>Description</th>
          <th>Reply</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($support_tickets->num_rows > 0): ?>
          <?php while ($ticket = $support_tickets->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($ticket['created_at']) ?></td>
              <td><?= htmlspecialchars($ticket['subject']) ?></td>
              <td><?= nl2br(htmlspecialchars($ticket['message'])) ?></td>
              <td>
                <?php if (!empty($ticket['reply'])): ?>
                  <?= nl2br(htmlspecialchars($ticket['reply'])) ?>
                <?php else: ?>
                  <em>Awaiting reply</em>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($ticket['status']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">No support tickets found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
