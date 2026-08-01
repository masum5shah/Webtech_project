<?php
session_start();
require_once "../config/db.php";

$errors = [];
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $secret_code = $_POST['secret_code'];

    // Validation
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match!"; }
    if ($secret_code !== "AGENT2024") { $errors[] = "Invalid Secret Code! Contact Admin."; }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM agents WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Username already taken!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO agents (username, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $username, $email, $hashed_pass);
            
            if ($insert->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $errors[] = "Database error. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Registration</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #e8f5e9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 380px; }
        h2 { color: #2e7d32; text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #2e7d32; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        button:hover { background: #1b5e20; }
        .error { color: #d32f2f; font-size: 13px; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .success { color: #2e7d32; font-size: 13px; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .info-text { font-size: 12px; color: #666; text-align: center; margin-top: 15px; }
        a { color: #2e7d32; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Agent Sign Up</h2>
        <?php foreach($errors as $err) echo "<div class='error'>$err</div>"; ?>
        <?php if($success) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="text" name="secret_code" placeholder="Secret Agent Code" required>
            <button type="submit" name="register">Register Account</button>
        </form>
        <div class="info-text">
            Already an Agent? <a href="agentlogin.php">Login here</a><br><br>
            Don't have a code? <a href="mailto:admin@agro.com">Request Code from Admin</a>
        </div>
    </div>
</body>
</html>