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
        $stmt = $conn->prepare("SELECT id,name,password FROM tourists WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id,$name,$hash);

        if($stmt->num_rows > 0){
            $stmt->fetch();
            if(password_verify($password, $hash)){
                $_SESSION['tourist_id'] = $id;
                $_SESSION['tourist_name'] = $name;
                header("Location: tourist_dashboard.php");
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
<title>Tourist Login | Agro-Tourism</title>
<style>
body{
    font-family:'Segoe UI',Tahoma;
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
    box-shadow:0 12px 30px rgba(0,0,0,0.4);
    width:400px;
}
h2{text-align:center;color:#2e7d32;margin-bottom:30px;}
input,button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:6px;
    font-size:16px;
}
input{border:1px solid #ccc;}
button{
    background:#2e7d32;
    border:none;
    color:white;
    cursor:pointer;
}
button:hover{background:#1b5e20;}
.error{
    background:#ffe0e0;
    border-left:5px solid #ff4b5c;
    padding:10px;
    margin-bottom:10px;
}
p{text-align:center;}
a{color:#2e7d32;text-decoration:none;}
</style>
</head>

<body>
<div class="container">
<h2>Tourist Login</h2>

<?php
foreach($errors as $err){
    echo "<div class='error'>$err</div>";
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<p>Don't have an account? <a href="tourist_registration.php">Register here</a></p>
<p><a href="welcomepage.php">⬅ Back to Welcome Page</a></p>
</div>
</body>
</html>
