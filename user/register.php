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
$full_name = $email = $id_number = $phone = $department = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim inputs
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $id_number = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $department = isset($_POST['department']) ? trim($_POST['department']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validation
    if (empty($full_name) || empty($email) || empty($id_number) || empty($phone) || empty($department) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if ID number already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "ID Number is already registered.";
    }
    $stmt->close();

    // Insert user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, id_number, phone, department, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $email, $id_number, $phone, $department, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful. You can now <a href='login.php'>login</a>.";
            // Clear form values on success
            $full_name = $email = $id_number = $phone = $department = "";
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
    label {
      font-weight: 600;
      display: block;
      margin-top: 12px;
    }
    input[type=text], input[type=email], input[type=password] {
      width: 100%; padding: 12px; margin: 6px 0 12px 0; border: 1px solid #ccc; border-radius: 5px;
      font-size: 1rem;
    }
    button {
      width: 100%; padding: 12px; background: #0072ce; color: white; border: none; border-radius: 5px;
      font-size: 1.1rem;
      font-weight: 700;
      margin-top: 10px;
    }
    button:hover { background: #004080; cursor: pointer; }
    .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
    .error { background-color: #ffd4d4; color: #900; }
    .success { background-color: #d4ffd4; color: #060; }
    .login-link { text-align: center; margin-top: 15px; }
    a {
      color: #005baa;
      text-decoration: none;
      font-weight: 600;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Employee Registration</h2>

    <?php if (!empty($errors)): ?>
      <div class="message error" role="alert">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="message success" role="alert"><?= $success ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST" novalidate>
      <label for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($full_name) ?>">

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

      <label for="id_number">Employee Id</label>
      <input type="text" id="id_number" name="id_number" required value="<?= htmlspecialchars($id_number) ?>">

      <label for="phone">Phone</label>
      <input type="text" id="phone" name="phone" required value="<?= htmlspecialchars($phone) ?>">

      <label for="department">Department</label>
      <input type="text" id="department" name="department" required value="<?= htmlspecialchars($department) ?>">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">Register</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</body>
</html>
