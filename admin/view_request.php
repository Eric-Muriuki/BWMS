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
  <title>View WiFi Request</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
    .container {
      background: #fff; padding: 30px; border-radius: 8px;
      max-width: 700px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { color: #003366; }
    .info { margin-bottom: 15px; }
    label { font-weight: bold; }
    .value { margin-left: 10px; }
    textarea {
      width: 100%; height: 80px; padding: 10px;
      margin-top: 10px; border-radius: 5px; border: 1px solid #ccc;
    }
    button {
      margin-top: 10px; padding: 10px 20px; border: none;
      background: #0072ce; color: #fff; border-radius: 5px;
      cursor: pointer;
    }
    button:hover { background: #004080; }
  </style>
</head>
<body>

<div class="container">
  <h2>WiFi Request Details</h2>

  <div class="info">
    <label>Employee Name:</label><span class="value"><?= htmlspecialchars($request['employee_name']) ?></span>
  </div>
  <div class="info">
    <label>Department:</label><span class="value"><?= htmlspecialchars($request['department']) ?></span>
  </div>
  <div class="info">
    <label>Reason:</label><span class="value"><?= nl2br(htmlspecialchars($request['reason'])) ?></span>
  </div>
  <div class="info">
    <label>Request Date:</label><span class="value"><?= htmlspecialchars($request['created_at']) ?></span>
  </div>
  <div class="info">
    <label>Status:</label><span class="value"><?= htmlspecialchars($request['status']) ?></span>
  </div>
  <div class="info">
    <label>Reply:</label><span class="value"><?= nl2br(htmlspecialchars($request['reply'])) ?: '<em>Not yet responded</em>' ?></span>
  </div>

  <?php if ($request['status'] !== 'Approved'): ?>
    <form method="POST">
      <label for="reply">Reply with WiFi Credentials</label>
      <textarea name="reply" required placeholder="e.g., SSID: BankSecure | Password: xxxxxxxx"></textarea>
      <button type="submit">Send Reply</button>
    </form>
  <?php else: ?>
    <p><em>This request has already been approved.</em></p>
  <?php endif; ?>
</div>

</body>
</html>
