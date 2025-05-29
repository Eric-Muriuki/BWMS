<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use isset to avoid undefined index warning
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    
    $id_number = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $department = isset($_POST['department']) ? trim($_POST['department']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Basic validation
    if (empty($full_name) || empty($id_number) || empty($phone) || empty($department) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if id_number is already registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "ID Number is already registered.";
    }
    $stmt->close();

    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, id_number, phone, department, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $id_number, $phone, $department, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful. You can now <a href='login.php'>login</a>.";
        } else {
            $errors[] = "Error registering user. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register - Bank WiFi System</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body { font-family: Arial, sans-serif; background: #f1f1f1; }
    .register-box {
      width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
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
    .success { background-color: #d4ffd4; color: #060; }
    .login-link { text-align: center; margin-top: 15px; }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Employee Registration</h2>

    <!-- Show errors -->
    <?php if (!empty($errors)): ?>
      <div class="message error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Show success -->
    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <label>Full Name</label>
      <input type="text" name="full_name" required>

      <label>Employee Id</label>
      <input type="text" name="id_number" required>

      <label>Phone</label>
      <input type="text" name="phone" required>

      <label>Department</label>
      <input type="text" name="department" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required>

      <button type="submit">Register</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</body>
</html>
