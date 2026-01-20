<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$errors = [];
if(isset($_POST['register'])){
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $phone = $_POST['phone'];

    if(empty($name)||empty($email)||empty($password)){
        $errors[] = "All required fields must be filled!";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format!";
    }
    if($password !== $confirm){
        $errors[] = "Passwords do not match!";
    }
    if(strlen($password) < 6){
        $errors[] = "Password must be at least 6 characters!";
    }
    if(!preg_match('/^\d{11}$/',$phone)){
        $errors[] = "Phone must be 11 digits!";
    }

    $stmt = $conn->prepare("SELECT id FROM tourists WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $errors[] = "Email already registered!";
    }

    if(empty($errors)){
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO tourists (name,email,password,phone) VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssss",$name,$email,$hash,$phone);
        if($stmt->execute()){
            $_SESSION['success'] = "Registration successful! Login now.";
            header("Location: tourist_login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tourist Registration | Agro-Tourism</title>
<style>
body{
    font-family:'Segoe UI';
    margin:0;
    min-height:100vh;
    background:
        linear-gradient(rgba(0,0,0,0.55),rgba(0,0,0,0.55)),
        url("updates/farm2.jpg") no-repeat center/cover;
    display:flex;
    justify-content:center;
    align-items:center;
}
.container{
    background:rgba(255,255,255,0.95);
    padding:40px 50px;
    border-radius:12px;
    width:400px;
}
h2{text-align:center;color:#2e7d32;}
input,button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:6px;
}
button{
    background:#2e7d32;
    color:white;
    border:none;
}
button:hover{background:#1b5e20;}
.error{
    background:#ffe0e0;
    border-left:5px solid #ff4b5c;
    padding:10px;
}
.success{
    background:#e0ffe0;
    border-left:5px solid #28a745;
    padding:10px;
}
p{text-align:center;}
</style>
</head>

<body>
<div class="container">
<h2>Tourist Registration</h2>

<?php
foreach($errors as $err){
    echo "<div class='error'>$err</div>";
}
if(isset($_SESSION['success'])){
    echo "<div class='success'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <input type="text" name="phone" placeholder="Phone (11 digits)" maxlength="11" required>
    <button type="submit" name="register">Register</button>
</form>

<p>Already have an account? <a href="tourist_login.php">Login here</a></p>
<p><a href="welcomepage.php">⬅ Back to Welcome Page</a></p>
</div>
</body>
</html>
