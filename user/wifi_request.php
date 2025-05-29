<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_message = trim($_POST['request_message']);
    $user_id = $_SESSION['user_id'];

    if (!empty($request_message)) {
        $stmt = $conn->prepare("INSERT INTO wifi_requests (user_id, request_message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $request_message);

        if ($stmt->execute()) {
            $message = "Your request has been submitted successfully!";
        } else {
            $message = "Error submitting request. Please try again.";
        }
    } else {
        $message = "Please enter your request message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request WiFi Access - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f8; }
    .container {
      width: 500px; margin: 40px auto; background: white;
      padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
    }
    h2 { text-align: center; color: #005baa; }
    textarea {
      width: 100%; height: 150px; padding: 12px;
      border: 1px solid #ccc; border-radius: 5px; resize: vertical;
    }
    button {
      width: 100%; padding: 12px; background: #0072ce;
      color: white; border: none; border-radius: 5px;
    }
    button:hover { background: #004080; cursor: pointer; }
    .message { text-align: center; color: green; margin-top: 10px; }
    .error { text-align: center; color: red; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; }
    .btn { background-color: #0072ce; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; }
  </style>
</head>
<body>
  <div class="container">
    <div class="top-bar">
      <h2>WiFi Access Request</h2>
      <a class="btn" href="dashboard.php">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
      <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="request_message">Your Request (e.g., location, reason):</label>
      <textarea name="request_message" id="request_message" required></textarea>

      <button type="submit">Submit Request</button>
    </form>
  </div>
</body>
</html>
