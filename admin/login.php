<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect logged-in admin
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    if (!$email || !$password) {
        $errors[] = "Please enter email and password.";
    } else {
        $sql = "SELECT id, full_name, password FROM admins WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No admin found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Login - Bank WiFi System</title>
<link rel="stylesheet" href="../css/style.css" />
<style>
  body { font-family: Arial, sans-serif; background: #f1f1f1; }
  .login-box {
    width: 400px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
  }
  h2 { text-align: center; color: #003366; }
  input[type=email], input[type=password] {
    width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;
  }
  button {
    width: 100%; padding: 12px; background: #003366; color: white; border: none; border-radius: 5px;
  }
  button:hover { background: #005baa; cursor: pointer; }
  .register-link { text-align: center; margin-top: 15px; }
  .errors { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="login-box">
  <h2>Admin Login</h2>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="" method="POST">
    <label>Email</label>
    <input type="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
  </form>

  <div class="register-link">
    <p>Not registered? <a href="register.php">Register here</a></p>
  </div>
</div>
</body>
</html>
