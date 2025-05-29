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
    // Sanitize inputs
    $fullname = trim(mysqli_real_escape_string($conn, $_POST['fullname']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $employee_id = trim(mysqli_real_escape_string($conn, $_POST['employee_id']));
    $phone = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (!$fullname || !$email || !$employee_id || !$phone || !$password || !$confirm_password) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email or employee_id already exists
        $check_sql = "SELECT id FROM admins WHERE email = '$email' OR employee_id = '$employee_id' LIMIT 1";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email or Employee ID already registered.";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO admins (full_name, email, employee_id, phone, password) VALUES ('$fullname', '$email', '$employee_id', '$phone', '$hashed_password')";
        if (mysqli_query($conn, $insert_sql)) {
            $_SESSION['admin_id'] = mysqli_insert_id($conn);
            $_SESSION['admin_name'] = $fullname;
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Registration - Bank WiFi System</title>
<link rel="stylesheet" href="../css/style.css" />
<style>
  body { font-family: Arial, sans-serif; background: #f1f1f1; }
  .register-box {
    width: 450px; margin: 50px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
  }
  h2 { text-align: center; color: #003366; }
  input[type=text], input[type=email], input[type=password] {
    width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;
  }
  button {
    width: 100%; padding: 12px; background: #003366; color: white; border: none; border-radius: 5px;
  }
  button:hover { background: #005baa; cursor: pointer; }
  .login-link { text-align: center; margin-top: 15px; }
  .errors { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="register-box">
  <h2>Admin Registration</h2>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="" method="POST">
    <label>Full Name</label>
    <input type="text" name="fullname" required value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>">

    <label>Email</label>
    <input type="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

    <label>Employee ID</label>
    <input type="text" name="employee_id" required value="<?= isset($employee_id) ? htmlspecialchars($employee_id) : '' ?>">

    <label>Phone</label>
    <input type="text" name="phone" required value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" required>

    <button type="submit">Register</button>
  </form>

  <div class="login-link">
    <p>Already registered? <a href="login.php">Login here</a></p>
  </div>
</div>
</body>
</html>
