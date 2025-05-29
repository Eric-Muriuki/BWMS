<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (!empty($subject) && !empty($message_text)) {
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $subject, $message_text);

        if ($stmt->execute()) {
            $message = "Your support ticket has been submitted successfully!";
        } else {
            $message = "There was an error submitting your ticket. Please try again.";
        }
    } else {
        $message = "Please fill in both subject and message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Support Ticket - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f1f1f1; }
    .container {
      width: 500px; margin: 40px auto; background: #fff;
      padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
    }
    h2 { text-align: center; color: #005baa; }
    input[type=text], textarea {
      width: 100%; padding: 12px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;
    }
    textarea { height: 150px; resize: vertical; }
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
      <h2>Submit Support Ticket</h2>
      <a class="btn" href="dashboard.php">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
      <p class="<?= strpos($message, 'successfully') !== false ? 'message' : 'error' ?>"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="subject">Subject:</label>
      <input type="text" name="subject" id="subject" required>

      <label for="message">Describe your issue:</label>
      <textarea name="message" id="message" required></textarea>

      <button type="submit">Submit Ticket</button>
    </form>
  </div>
</body>
</html>
