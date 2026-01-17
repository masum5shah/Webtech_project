<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$errors = [];
if(isset($_POST['register'])){
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $farm_name = $_POST['farm_name'];

    // PHP Validation
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
        $errors[] = "Name, Email and Password are required!";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format!";
    }
    if($password !== $confirm_password){
        $errors[] = "Passwords do not match!";
    }
    if(strlen($password) < 6){
        $errors[] = "Password must be at least 6 characters!";
    }
    if(!preg_match('/^\d{11}$/', $phone)){
        $errors[] = "Phone number must be exactly 11 digits!";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM farmers WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $errors[] = "Email already registered!";
    }

    // Insert into database if no errors
    if(empty($errors)){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO farmers (name,email,password,phone,farm_name) VALUES(?,?,?,?,?)");
        $stmt->bind_param("sssss",$name,$email,$hash,$phone,$farm_name);
        if($stmt->execute()){
            $_SESSION['success'] = "Registration successful! Login now.";
            header("Location: farmer_login.php");
            exit;
        }else{
            $errors[] = "Database error! Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Registration | Agro-Tourism</title>
    <script>
        function validateRegistration(){
            var form = document.forms["regForm"];
            var pass = form["password"].value;
            var cpass = form["confirm_password"].value;
            var phone = form["phone"].value;

            if(pass.length < 6){
                alert("Password must be at least 6 characters!");
                return false;
            }
            if(pass !== cpass){
                alert("Passwords do not match!");
                return false;
            }
            if(!/^\d{11}$/.test(phone)){
                alert("Phone number must be exactly 11 digits!");
                return false;
            }
            return true;
        }
    </script>
    <style>
        /* Body */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;

            /* 🌾 Background Image with overlay */
            background: 
                linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                url("updates/farm2.jpg") no-repeat center center/cover;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2e7d32; /* Match login page */
        }

        /* Input Fields */
        input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 16px;
        }

        input:focus {
            border-color: #2e7d32; /* Match login page */
            box-shadow: 0 0 6px rgba(46,125,50,0.4);
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32; /* Green like login page */
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1b5e20; /* Darker green on hover */
        }

        /* Links */
        a {
            color: #2e7d32; /* Match login page */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Error / Success Messages */
        .error {
            background: #ffe0e0;
            border-left: 5px solid #ff4b5c;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            color: #a70000;
        }

        .success {
            background: #e0ffe0;
            border-left: 5px solid #28a745;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            color: #006400;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Farmer Registration</h2>

    <?php
    if(!empty($errors)){
        foreach($errors as $err){
            echo "<div class='error'>$err</div>";
        }
    }
    if(isset($_SESSION['success'])){
        echo "<div class='success'>".$_SESSION['success']."</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form name="regForm" method="POST" onsubmit="return validateRegistration()">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <input type="text" name="phone" placeholder="Phone (11 digits)" maxlength="11" pattern="\d{11}" required>
        <input type="text" name="farm_name" placeholder="Farm Name">
        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="farmer_login.php">Login here</a></p>
    <p><a href="welcomepage.php">⬅ Back to Welcome Page</a></p>
</div>
</body>
</html>
