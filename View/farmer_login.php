<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$errors = [];
if(isset($_POST['login'])){
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $errors[] = "Both fields are required!";
    } else {
        $stmt = $conn->prepare("SELECT id,name,password FROM farmers WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id,$name,$hash);

        if($stmt->num_rows > 0){
            $stmt->fetch();
            if(password_verify($password, $hash)){
                $_SESSION['farmer_id'] = $id;
                $_SESSION['farmer_name'] = $name;
                header("Location: farmer_dashboard.php");
                exit;
            }else{
                $errors[] = "Incorrect password!";
            }
        } else {
            $errors[] = "Email not registered!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Farmer Login | Agro-Tourism</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    min-height: 100vh;

    /* 🌾 BACKGROUND IMAGE */
    background: 
        linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
        url("updates/farm2.jpg") no-repeat center center/cover;

    display: flex;
    justify-content: center;
    align-items: center;
}

/* LOGIN BOX */
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
    color: #2e7d32;
}

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
    border-color: #2e7d32;
    box-shadow: 0 0 6px rgba(46,125,50,0.4);
}

button {
    width: 100%;
    padding: 12px;
    background-color: #2e7d32;
    border: none;
    border-radius: 6px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
}

button:hover {
    background-color: #1b5e20;
}

.error {
    background: #ffe0e0;
    border-left: 5px solid #ff4b5c;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
    color: #a70000;
}

a {
    color: #2e7d32;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

p {
    text-align: center;
    margin-top: 15px;
}
</style>
</head>

<body>

<div class="container">
    <h2>Farmer Login</h2>

    <?php
    if(!empty($errors)){
        foreach($errors as $err){
            echo "<div class='error'>$err</div>";
        }
    }
    ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p>Don't have an account? <a href="farmer_register.php">Register here</a></p>
    <p><a href="welcomepage.php">⬅ Back to Welcome Page</a></p>
</div>

</body>
</html>
