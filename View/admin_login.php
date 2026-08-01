<?php
session_start();

// Set your fixed username and password here
$fixed_username = "admin";
$fixed_password = "admin123"; 

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Direct credential verification
    if($username === $fixed_username && $password === $fixed_password){
        // Secure session handling
        session_regenerate_id(true); 
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body{ 
            font-family: 'Poppins', sans-serif; 
            background: #f4f7f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-box{ 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
            width: 340px; 
            text-align: center; 
        }
        h2{ color: #2e7d32; margin-bottom: 25px; font-weight: 600; }
        input{ 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 14px;
        }
        input:focus{
            outline: none;
            border-color: #2e7d32;
        }
        button{ 
            width: 100%; 
            padding: 12px; 
            background: #2e7d32; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 16px;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        button:hover{ background: #1b5e20; }
        .error{ 
            color: #d32f2f; 
            font-size: 14px; 
            margin-bottom: 15px; 
            background: #ffebee;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>
    
    <?php if(isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>