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
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $employee_id = trim($_POST['employee_id']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($fullname) || empty($email) || empty($employee_id) || empty($phone) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } else {
        // Check if email or employee_id already exists using prepared statement
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ? OR employee_id = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email or Employee ID already registered.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (full_name, email, employee_id, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $email, $employee_id, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['admin_id'] = $stmt->insert_id;
            $_SESSION['admin_name'] = $fullname;
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration - Bank WiFi System</title>
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
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  
  .register-container {
    width: 100%;
    max-width: 500px;
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }
  
  .register-header {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .register-header h2 {
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
  }
  
  .register-header p {
    color: var(--gray);
    margin-top: 0;
  }
  
  .form-group {
    margin-bottom: 1.5rem;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--gray-dark);
  }
  
  .form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid var(--gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
  }
  
  .form-control:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(246, 112, 17, 0.2);
  }
  
  .btn {
    width: 100%;
    padding: 1rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
  }
  
  .login-link {
    text-align: center;
    margin-top: 1.5rem;
    color: var(--gray-dark);
  }
  
  .login-link a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
  }
  
  .login-link a:hover {
    text-decoration: underline;
  }
  
  .alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid;
  }
  
  .alert-danger {
    background: rgba(198, 40, 40, 0.1);
    color: var(--error);
    border-left-color: var(--error);
  }
  
  @media (max-width: 576px) {
    .register-container {
      padding: 1.5rem;
      margin: 1rem;
    }
    
    body {
      align-items: flex-start;
      padding-top: 2rem;
    }
  }
</style>
</head>
<body>
<div class="register-container">
  <div class="register-header">
    <h2>Admin Registration</h2>
    <p>Create your administrator account</p>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="" method="POST">
    <div class="form-group">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" class="form-control" required 
             value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>">
    </div>
    
    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" required 
             value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    </div>
    
    <div class="form-group">
      <label for="employee_id">Employee ID</label>
      <input type="text" id="employee_id" name="employee_id" class="form-control" required 
             value="<?= isset($employee_id) ? htmlspecialchars($employee_id) : '' ?>">
    </div>
    
    <div class="form-group">
      <label for="phone">Phone Number</label>
      <input type="text" id="phone" name="phone" class="form-control" required 
             value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">
    </div>
    
    <div class="form-group">
      <label for="password">Password (min 8 characters)</label>
      <input type="password" id="password" name="password" class="form-control" required minlength="8">
    </div>
    
    <div class="form-group">
      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
    </div>
    
    <button type="submit" class="btn">Register Account</button>
  </form>

  <div class="login-link">
    Already have an account? <a href="login.php">Login here</a>
  </div>
</div>
</body>
</html>