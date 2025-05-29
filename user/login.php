<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($id_number) || empty($password)) {
        $error = "Please enter both ID Number and password.";
    } else {
        // Query uses id_number (not employee_id) to match registration field
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE id_number = ?");
        $stmt->bind_param("s", $id_number);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $full_name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['full_name'] = $full_name;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No account found with that ID Number.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body { font-family: Arial, sans-serif; background: #f1f1f1; }
    .login-box {
      width: 400px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
    }
    h2 { text-align: center; color: #005baa; }
    input[type=text], input[type=password] {
      width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;
    }
    button {
      width: 100%; padding: 12px; background: #0072ce; color: white; border: none; border-radius: 5px;
    }
    button:hover { background: #004080; cursor: pointer; }
    .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
    .error { background-color: #ffd4d4; color: #900; }
    .register-link { text-align: center; margin-top: 15px; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Employee Login</h2>

    <?php if (!empty($error)): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <label>ID Number</label>
      <input type="text" name="id_number" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>

    <div class="register-link">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
</body>
</html>
