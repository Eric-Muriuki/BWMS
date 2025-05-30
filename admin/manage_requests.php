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
            $success = "Reply sent and status updated successfully!";
        } else {
            $error = "Error sending reply. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Reply message cannot be empty.";
    }
}

// Fetch all requests
$query = "SELECT wr.id, wr.user_id, u.full_name, wr.request_date, wr.request_message, 
                 wr.reply_message, wr.reply_date, wr.status 
          FROM wifi_requests wr
          JOIN users u ON wr.user_id = u.id
          ORDER BY wr.request_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage WiFi Requests - Admin Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    :root {
      --dark: #16151A;
      --primary: #F67011;
      --primary-dark: #873800;
      --gray-dark: #262626;
      --gray: #878787;
      --light: #FFE4D0;
      --success: #2e7d32;
      --error: #c62828;
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
      color: white;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .container {
      width: 95%;
      max-width: 1200px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    h2 {
      text-align: center;
      color: var(--primary-dark);
      margin-bottom: 1.5rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid var(--primary);
    }
    
    .alert {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 6px;
      text-align: center;
      border-left: 4px solid;
    }
    
    .alert-success {
      background: rgba(46, 125, 50, 0.1);
      color: var(--success);
      border-left-color: var(--success);
    }
    
    .alert-error {
      background: rgba(198, 40, 40, 0.1);
      color: var(--error);
      border-left-color: var(--error);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    th {
      background: var(--primary-dark);
      color: white;
      font-weight: 600;
    }
    
    tr:nth-child(even) {
      background-color: rgba(255, 228, 208, 0.3);
    }
    
    tr:hover {
      background-color: rgba(246, 112, 17, 0.05);
    }
    
    textarea {
      width: 100%;
      min-height: 100px;
      padding: 0.75rem;
      margin: 0.5rem 0;
      border: 1px solid var(--gray);
      border-radius: 6px;
      resize: vertical;
      font-family: inherit;
      transition: all 0.3s;
    }
    
    textarea:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(246, 112, 17, 0.15);
    }
    
    .btn {
      background: var(--primary);
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(135, 56, 0, 0.2);
    }
    
    .status {
      display: inline-block;
      padding: 0.35rem 0.75rem;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    
    .status-pending {
      background: rgba(255, 193, 7, 0.2);
      color: #ff9800;
    }
    
    .status-replied {
      background: rgba(46, 125, 50, 0.2);
      color: var(--success);
    }
    
    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }
    
    @media (max-width: 768px) {
      .container {
        padding: 1.25rem;
        width: 100%;
        border-radius: 0;
      }
      
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
      
      th, td {
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body>

<header class="header">
  <h1>WiFi Access Requests Management</h1>
</header>

<main class="container">
  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <h2>All WiFi Requests</h2>

  <table>
    <thead>
      <tr>
        <th>User</th>
        <th>Date Requested</th>
        <th>Request Message</th>
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
                  <textarea name="reply_message" required placeholder="Enter your reply message..."></textarea>
                  <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                  <button type="submit" class="btn">Send Reply</button>
                </form>
              <?php endif; ?>
            </td>
            <td><?= $row['reply_date'] ? htmlspecialchars($row['reply_date']) : '-' ?></td>
            <td>
              <span class="status status-<?= strtolower($row['status']) ?>">
                <?= htmlspecialchars($row['status']) ?>
              </span>
            </td>
            <td>
              <?php if (!$row['reply_message']): ?>
                <em>Pending Response</em>
              <?php else: ?>
                <strong>Completed</strong>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align: center;">No WiFi requests found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>

<footer>
  <p>Bank WiFi Management System &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>