<?php
session_start();
require_once "../config/db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM agents WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_pass);
    
    if ($stmt->fetch() && password_verify($password, $hashed_pass)) {
        $_SESSION['agent_logged_in'] = true;
        $_SESSION['agent_id'] = $id;
        $_SESSION['agent_user'] = $username;
        header("Location: agent_dashboard.php");
        exit;
    } else {
        $error = "Invalid Credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Login</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f8e9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 340px; }
        h2 { color: #1b5e20; text-align: center; margin-bottom: 25px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #1b5e20; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .error { color: red; text-align: center; font-size: 14px; margin-bottom: 10px; }
        a { display: block; text-align: center; margin-top: 20px; color: #1b5e20; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Agent Login</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="agent_registration.php">Need an account? Register</a>
    </div>
</body>
</html>